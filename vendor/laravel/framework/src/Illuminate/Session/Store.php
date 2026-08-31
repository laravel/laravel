<?php

namespace Illuminate\Session;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Uri;
use Illuminate\Support\ViewErrorBag;
use RuntimeException;
use SessionHandlerInterface;
use stdClass;
use UnitEnum;

use function Illuminate\Support\enum_value;

class Store implements Session
{
    use Macroable;

    /**
     * The length of session ID strings.
     *
     * @var int
     */
    protected const SESSION_ID_LENGTH = 40;

    /**
     * The session ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The session name.
     *
     * @var string
     */
    protected $name;

    /**
     * The session attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The session handler implementation.
     *
     * @var \SessionHandlerInterface
     */
    protected $handler;

    /**
     * The session store's serialization strategy.
     *
     * @var string
     */
    protected $serialization = 'php';

    /**
     * Session store started status.
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Create a new session instance.
     *
     * @param  string  $name
     * @param  \SessionHandlerInterface  $handler
     * @param  string|null  $id
     * @param  string  $serialization
     */
    public function __construct($name, SessionHandlerInterface $handler, $id = null, $serialization = 'php')
    {
        $this->setId($id);
        $this->name = $name;
        $this->handler = $handler;
        $this->serialization = $serialization;
    }

    /**
     * Start the session, reading the data from a handler.
     *
     * @return bool
     */
    public function start()
    {
        $this->loadSession();

        if (! $this->has('_token')) {
            $this->regenerateToken();
        }

        return $this->started = true;
    }

    /**
     * Load the session data from the handler.
     *
     * @return void
     */
    protected function loadSession()
    {
        $this->attributes = array_replace($this->attributes, $this->readFromHandler());

        $this->marshalErrorBag();
    }

    /**
     * Read the session data from the handler.
     *
     * @return array
     */
    protected function readFromHandler()
    {
        if ($data = $this->handler->read($this->getId())) {
            if ($this->serialization === 'json') {
                $data = json_decode($this->prepareForUnserialize($data), true);
            } else {
                $data = @unserialize($this->prepareForUnserialize($data));
            }

            if ($data !== false && is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Prepare the raw string data from the session for unserialization.
     *
     * @param  string  $data
     * @return string
     */
    protected function prepareForUnserialize($data)
    {
        return $data;
    }

    /**
     * Marshal the ViewErrorBag when using JSON serialization for sessions.
     *
     * @return void
     */
    protected function marshalErrorBag()
    {
        if ($this->serialization !== 'json' || $this->missing('errors')) {
            return;
        }

        $errorBag = new ViewErrorBag;

        foreach ($this->get('errors') as $key => $value) {
            $messageBag = new MessageBag($value['messages']);

            $errorBag->put($key, $messageBag->setFormat($value['format']));
        }

        $this->put('errors', $errorBag);
    }

    /**
     * Save the session data to storage.
     *
     * @return void
     */
    public function save()
    {
        $this->ageFlashData();

        $this->prepareErrorBagForSerialization();

        $this->handler->write($this->getId(), $this->prepareForStorage(
            $this->serialization === 'json' ? json_encode($this->attributes) : serialize($this->attributes)
        ));

        $this->started = false;
    }

    /**
     * Prepare the ViewErrorBag instance for JSON serialization.
     *
     * @return void
     */
    protected function prepareErrorBagForSerialization()
    {
        if ($this->serialization !== 'json' || $this->missing('errors')) {
            return;
        }

        $errors = [];

        foreach ($this->attributes['errors']->getBags() as $key => $value) {
            $errors[$key] = [
                'format' => $value->getFormat(),
                'messages' => $value->getMessages(),
            ];
        }

        $this->attributes['errors'] = $errors;
    }

    /**
     * Prepare the serialized session data for storage.
     *
     * @param  string  $data
     * @return string
     */
    protected function prepareForStorage($data)
    {
        return $data;
    }

    /**
     * Age the flash data for the session.
     *
     * @return void
     */
    public function ageFlashData()
    {
        $this->forget($this->get('_flash.old', []));

        $this->put('_flash.old', $this->get('_flash.new', []));

        $this->put('_flash.new', []);
    }

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Get a subset of the session data.
     *
     * @param  array  $keys
     * @return array
     */
    public function only(array $keys)
    {
        return Arr::only($this->attributes, $keys);
    }

    /**
     * Get all the session data except for a specified array of items.
     *
     * @param  array  $keys
     * @return array
     */
    public function except(array $keys)
    {
        return Arr::except($this->attributes, $keys);
    }

    /**
     * Checks if a key exists.
     *
     * @param  \UnitEnum|string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        $placeholder = new stdClass;

        return ! (new Collection(is_array($key) ? $key : func_get_args()))->contains(function ($key) use ($placeholder) {
            return $this->get($key, $placeholder) === $placeholder;
        });
    }

    /**
     * Determine if the given key is missing from the session data.
     *
     * @param  \UnitEnum|string|array  $key
     * @return bool
     */
    public function missing($key)
    {
        return ! $this->exists($key);
    }

    /**
     * Determine if a key is present and not null.
     *
     * @param  \UnitEnum|string|array  $key
     * @return bool
     */
    public function has($key)
    {
        return ! (new Collection(is_array($key) ? $key : func_get_args()))->contains(function ($key) {
            return is_null($this->get($key));
        });
    }

    /**
     * Determine if any of the given keys are present and not null.
     *
     * @param  \UnitEnum|string|array  $key
     * @return bool
     */
    public function hasAny($key)
    {
        return (new Collection(is_array($key) ? $key : func_get_args()))->filter(function ($key) {
            return ! is_null($this->get($key));
        })->count() >= 1;
    }

    /**
     * Get an item from the session.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, enum_value($key), $default);
    }

    /**
     * Get the value of a given key and then forget it.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->attributes, enum_value($key), $default);
    }

    /**
     * Determine if the session contains old input.
     *
     * @param  string|null  $key
     * @return bool
     */
    public function hasOldInput($key = null)
    {
        $old = $this->getOldInput($key);

        return is_null($key) ? count($old) > 0 : ! is_null($old);
    }

    /**
     * Get the requested item from the flashed input array.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOldInput($key = null, $default = null)
    {
        return Arr::get($this->get('_old_input', []), $key, $default);
    }

    /**
     * Replace the given session attributes entirely.
     *
     * @param  array  $attributes
     * @return void
     */
    public function replace(array $attributes)
    {
        $this->put($attributes);
    }

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param  \UnitEnum|string|array  $key
     * @param  mixed  $value
     * @return void
     */
    public function put($key, $value = null)
    {
        if (! is_array($key)) {
            $key = [enum_value($key) => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            Arr::set($this->attributes, enum_value($arrayKey), $arrayValue);
        }
    }

    /**
     * Get an item from the session, or store the default value.
     *
     * @param  \UnitEnum|string  $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember($key, Closure $callback)
    {
        if (! is_null($value = $this->get($key))) {
            return $value;
        }

        return tap($callback(), function ($value) use ($key) {
            $this->put($key, $value);
        });
    }

    /**
     * Push a value onto a session array.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->put($key, $array);
    }

    /**
     * Increment the value of an item in the session.
     *
     * @param  \UnitEnum|string  $key
     * @param  int  $amount
     * @return mixed
     */
    public function increment($key, $amount = 1)
    {
        $this->put($key, $value = $this->get($key, 0) + $amount);

        return $value;
    }

    /**
     * Decrement the value of an item in the session.
     *
     * @param  \UnitEnum|string  $key
     * @param  int  $amount
     * @return int
     */
    public function decrement($key, $amount = 1)
    {
        return $this->increment($key, $amount * -1);
    }

    /**
     * Flash a key / value pair to the session.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function flash(BackedEnum|UnitEnum|string $key, $value = true)
    {
        $key = enum_value($key);

        $this->put($key, $value);

        $this->push('_flash.new', $key);

        $this->removeFromOldFlashData([$key]);
    }

    /**
     * Flash a key / value pair to the session for immediate use.
     *
     * @param  \UnitEnum|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function now($key, $value)
    {
        $key = enum_value($key);

        $this->put($key, $value);

        $this->push('_flash.old', $key);
    }

    /**
     * Reflash all of the session flash data.
     *
     * @return void
     */
    public function reflash()
    {
        $this->mergeNewFlashes($this->get('_flash.old', []));

        $this->put('_flash.old', []);
    }

    /**
     * Reflash a subset of the current flash data.
     *
     * @param  mixed  $keys
     * @return void
     */
    public function keep($keys = null)
    {
        $this->mergeNewFlashes($keys = is_array($keys) ? $keys : func_get_args());

        $this->removeFromOldFlashData($keys);
    }

    /**
     * Merge new flash keys into the new flash array.
     *
     * @param  array  $keys
     * @return void
     */
    protected function mergeNewFlashes(array $keys)
    {
        $values = array_unique(array_merge($this->get('_flash.new', []), $keys));

        $this->put('_flash.new', $values);
    }

    /**
     * Remove the given keys from the old flash data.
     *
     * @param  array  $keys
     * @return void
     */
    protected function removeFromOldFlashData(array $keys)
    {
        $this->put('_flash.old', array_diff($this->get('_flash.old', []), $keys));
    }

    /**
     * Flash an input array to the session.
     *
     * @param  array  $value
     * @return void
     */
    public function flashInput(array $value)
    {
        $this->flash('_old_input', $value);
    }

    /**
     * Get the session cache instance.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function cache()
    {
        return Cache::store('session');
    }

    /**
     * Remove an item from the session, returning its value.
     *
     * @param  \UnitEnum|string  $key
     * @return mixed
     */
    public function remove($key)
    {
        return Arr::pull($this->attributes, enum_value($key));
    }

    /**
     * Remove one or many items from the session.
     *
     * @param  \UnitEnum|string|array  $keys
     * @return void
     */
    public function forget($keys)
    {
        Arr::forget($this->attributes, (new Collection((array) $keys))->map(fn ($key) => enum_value($key))->all());
    }

    /**
     * Remove all of the items from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->attributes = [];
    }

    /**
     * Flush the session data and regenerate the ID.
     *
     * @return bool
     */
    public function invalidate()
    {
        $this->flush();

        return $this->migrate(true);
    }

    /**
     * Generate a new session identifier.
     *
     * @param  bool  $destroy
     * @return bool
     */
    public function regenerate($destroy = false)
    {
        return tap($this->migrate($destroy), function () {
            $this->regenerateToken();
        });
    }

    /**
     * Generate a new session ID for the session.
     *
     * @param  bool  $destroy
     * @return bool
     */
    public function migrate($destroy = false)
    {
        if ($destroy) {
            $this->handler->destroy($this->getId());
        }

        $this->setExists(false);

        $this->setId($this->generateSessionId());

        return true;
    }

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Get the name of the session.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the session.
     *
     * @param  string  $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the current session ID.
     *
     * @return string
     */
    public function id()
    {
        return $this->getId();
    }

    /**
     * Get the current session ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the session ID.
     *
     * @param  string|null  $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $this->isValidId($id) ? $id : $this->generateSessionId();
    }

    /**
     * Determine if this is a valid session ID.
     *
     * @param  string|null  $id
     * @return bool
     */
    public function isValidId($id)
    {
        return is_string($id) && ctype_alnum($id) && strlen($id) === self::SESSION_ID_LENGTH;
    }

    /**
     * Get a new, random session ID.
     *
     * @return string
     */
    protected function generateSessionId()
    {
        return Str::random(self::SESSION_ID_LENGTH);
    }

    /**
     * Set the existence of the session on the handler if applicable.
     *
     * @param  bool  $value
     * @return void
     */
    public function setExists($value)
    {
        if ($this->handler instanceof ExistenceAwareInterface) {
            $this->handler->setExists($value);
        }
    }

    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token()
    {
        return $this->get('_token');
    }

    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     */
    public function regenerateToken()
    {
        $this->put('_token', Str::random(self::SESSION_ID_LENGTH));
    }

    /**
     * Determine if the previous URI is available.
     *
     * @return bool
     */
    public function hasPreviousUri()
    {
        return ! is_null($this->previousUrl());
    }

    /**
     * Get the previous URL from the session as a URI instance.
     *
     * @return \Illuminate\Support\Uri
     *
     * @throws \RuntimeException
     */
    public function previousUri()
    {
        if ($previousUrl = $this->previousUrl()) {
            return Uri::of($previousUrl);
        }

        throw new RuntimeException('Unable to generate URI instance for previous URL. No previous URL detected.');
    }

    /**
     * Get the previous URL from the session.
     *
     * @return string|null
     */
    public function previousUrl()
    {
        return $this->get('_previous.url');
    }

    /**
     * Set the "previous" URL in the session.
     *
     * @param  string  $url
     * @return void
     */
    public function setPreviousUrl($url)
    {
        $this->put('_previous.url', $url);
    }

    /**
     * Get the previous route name from the session.
     *
     * @return string|null
     */
    public function previousRoute()
    {
        return $this->get('_previous.route');
    }

    /**
     * Set the "previous" route name in the session.
     *
     * @param  string|null  $route
     * @return void
     */
    public function setPreviousRoute($route)
    {
        $this->put('_previous.route', $route);
    }

    /**
     * Specify that the user has confirmed their password.
     *
     * @return void
     */
    public function passwordConfirmed()
    {
        $this->put('auth.password_confirmed_at', Date::now()->unix());
    }

    /**
     * Get the underlying session handler implementation.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set the underlying session handler implementation.
     *
     * @param  \SessionHandlerInterface  $handler
     * @return \SessionHandlerInterface
     */
    public function setHandler(SessionHandlerInterface $handler)
    {
        return $this->handler = $handler;
    }

    /**
     * Determine if the session handler needs a request.
     *
     * @return bool
     */
    public function handlerNeedsRequest()
    {
        return $this->handler instanceof CookieSessionHandler;
    }

    /**
     * Set the request on the handler instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function setRequestOnHandler($request)
    {
        if ($this->handlerNeedsRequest()) {
            $this->handler->setRequest($request);
        }
    }
}
