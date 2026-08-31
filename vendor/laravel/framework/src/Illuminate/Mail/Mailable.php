<?php

namespace Illuminate\Mail;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Mail\Attachable;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Queue\Factory as Queue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Support\Collection;
use Illuminate\Support\EncodedHtmlString;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Testing\Constraints\SeeInOrder;
use PHPUnit\Framework\Assert as PHPUnit;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mime\Address;

class Mailable implements MailableContract, Renderable
{
    use Conditionable, ForwardsCalls, Localizable, Tappable, Macroable {
        __call as macroCall;
    }

    /**
     * The locale of the message.
     *
     * @var string
     */
    public $locale;

    /**
     * The person the message is from.
     *
     * @var array
     */
    public $from = [];

    /**
     * The "to" recipients of the message.
     *
     * @var array
     */
    public $to = [];

    /**
     * The "cc" recipients of the message.
     *
     * @var array
     */
    public $cc = [];

    /**
     * The "bcc" recipients of the message.
     *
     * @var array
     */
    public $bcc = [];

    /**
     * The "reply to" recipients of the message.
     *
     * @var array
     */
    public $replyTo = [];

    /**
     * The subject of the message.
     *
     * @var string
     */
    public $subject;

    /**
     * The Markdown template for the message (if applicable).
     *
     * @var string
     */
    public $markdown;

    /**
     * The HTML to use for the message.
     *
     * @var string
     */
    protected $html;

    /**
     * The view to use for the message.
     *
     * @var string
     */
    public $view;

    /**
     * The plain text view to use for the message.
     *
     * @var string
     */
    public $textView;

    /**
     * The view data for the message.
     *
     * @var array
     */
    public $viewData = [];

    /**
     * The attachments for the message.
     *
     * @var array
     */
    public $attachments = [];

    /**
     * The raw attachments for the message.
     *
     * @var array
     */
    public $rawAttachments = [];

    /**
     * The attachments from a storage disk.
     *
     * @var array
     */
    public $diskAttachments = [];

    /**
     * The tags for the message.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * The metadata for the message.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * The callbacks for the message.
     *
     * @var array
     */
    public $callbacks = [];

    /**
     * The name of the theme that should be used when formatting the message.
     *
     * @var string|null
     */
    public $theme;

    /**
     * The name of the mailer that should send the message.
     *
     * @var string
     */
    public $mailer;

    /**
     * The rendered mailable views for testing / assertions.
     *
     * @var array
     */
    protected $assertionableRenderStrings;

    /**
     * The callback that should be invoked while building the view data.
     *
     * @var callable
     */
    public static $viewDataCallback;

    /**
     * Send the message using the given mailer.
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function send($mailer)
    {
        return $this->withLocale($this->locale, function () use ($mailer) {
            $this->prepareMailableForDelivery();

            $mailer = $mailer instanceof MailFactory
                ? $mailer->mailer($this->mailer)
                : $mailer;

            return $mailer->send($this->buildView(), $this->buildViewData(), function ($message) {
                $this->buildFrom($message)
                    ->buildRecipients($message)
                    ->buildSubject($message)
                    ->buildTags($message)
                    ->buildMetadata($message)
                    ->runCallbacks($message)
                    ->buildAttachments($message);
            });
        });
    }

    /**
     * Queue the message for sending.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     * @return mixed
     */
    public function queue(Queue $queue)
    {
        if (isset($this->delay)) {
            return $this->later($this->delay, $queue);
        }

        $connection = property_exists($this, 'connection') ? $this->connection : null;

        $queueName = property_exists($this, 'queue') ? $this->queue : null;

        return $queue->connection($connection)->pushOn(
            $queueName ?: null, $this->newQueuedJob()
        );
    }

    /**
     * Deliver the queued message after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     * @return mixed
     */
    public function later($delay, Queue $queue)
    {
        $connection = property_exists($this, 'connection') ? $this->connection : null;

        $queueName = property_exists($this, 'queue') ? $this->queue : null;

        $job = $this->newQueuedJob();

        $job->delay($delay);

        return $queue->connection($connection)->laterOn(
            $queueName ?: null, $delay, $job
        );
    }

    /**
     * Make the queued mailable job instance.
     *
     * @return mixed
     */
    protected function newQueuedJob()
    {
        $messageGroup = $this->messageGroup ?? (method_exists($this, 'messageGroup') ? $this->messageGroup() : null);

        $deduplicator = $this->deduplicator ?? (method_exists($this, 'deduplicationId') ? $this->deduplicationId(...) : null);

        return Container::getInstance()->make(SendQueuedMailable::class, ['mailable' => $this])
            ->onGroup($messageGroup)
            ->withDeduplicator($deduplicator)
            ->through(array_merge(
                method_exists($this, 'middleware') ? $this->middleware() : [],
                $this->middleware ?? []
            ));
    }

    /**
     * Render the mailable into a view.
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function render()
    {
        return $this->withLocale($this->locale, function () {
            $this->prepareMailableForDelivery();

            return Container::getInstance()->make('mailer')->render(
                $this->buildView(), $this->buildViewData()
            );
        });
    }

    /**
     * Build the view for the message.
     *
     * @return array|string
     *
     * @throws \ReflectionException
     */
    protected function buildView()
    {
        if (isset($this->html)) {
            return array_filter([
                'html' => new HtmlString($this->html),
                'text' => $this->textView ?? null,
            ]);
        }

        if (isset($this->markdown)) {
            return $this->buildMarkdownView();
        }

        if (isset($this->view, $this->textView)) {
            return [$this->view, $this->textView];
        } elseif (isset($this->textView)) {
            return ['text' => $this->textView];
        }

        return $this->view;
    }

    /**
     * Build the Markdown view for the message.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function buildMarkdownView()
    {
        $data = $this->buildViewData();

        return [
            'html' => $this->buildMarkdownHtml($data),
            'text' => $this->buildMarkdownText($data),
        ];
    }

    /**
     * Build the view data for the message.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function buildViewData()
    {
        $data = $this->viewData;

        if (static::$viewDataCallback) {
            $data = array_merge($data, call_user_func(static::$viewDataCallback, $this));
        }

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isInitialized($this) && $property->getDeclaringClass()->getName() !== self::class) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }

        return array_merge($data, $this->additionalMessageData());
    }

    /**
     * Get additional meta-data to pass along with the view data.
     *
     * @return array<string, mixed>
     */
    protected function additionalMessageData(): array
    {
        return [
            '__laravel_mailable' => get_class($this),
        ];
    }

    /**
     * Build the HTML view for a Markdown message.
     *
     * @param  array  $viewData
     * @return \Closure
     */
    protected function buildMarkdownHtml($viewData)
    {
        return fn ($data) => $this->markdownRenderer()->render(
            $this->markdown,
            array_merge($data, $viewData),
        );
    }

    /**
     * Build the text view for a Markdown message.
     *
     * @param  array  $viewData
     * @return \Closure
     */
    protected function buildMarkdownText($viewData)
    {
        return function ($data) use ($viewData) {
            if (isset($data['message'])) {
                $data = array_merge($data, [
                    'message' => new TextMessage($data['message']),
                ]);
            }

            return $this->textView ?? $this->markdownRenderer()->renderText(
                $this->markdown,
                array_merge($data, $viewData)
            );
        };
    }

    /**
     * Resolves a Markdown instance with the mail's theme.
     *
     * @return \Illuminate\Mail\Markdown
     */
    protected function markdownRenderer()
    {
        return tap(Container::getInstance()->make(Markdown::class), function ($markdown) {
            $markdown->theme($this->theme ?: Container::getInstance()->get(ConfigRepository::class)->get(
                'mail.markdown.theme', 'default')
            );
        });
    }

    /**
     * Add the sender to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildFrom($message)
    {
        if (! empty($this->from)) {
            $message->from($this->from[0]['address'], $this->from[0]['name']);
        }

        return $this;
    }

    /**
     * Add all of the recipients to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildRecipients($message)
    {
        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            foreach ($this->{$type} as $recipient) {
                $message->{$type}($recipient['address'], $recipient['name']);
            }
        }

        return $this;
    }

    /**
     * Set the subject for the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildSubject($message)
    {
        if ($this->subject) {
            $message->subject($this->subject);
        } else {
            $message->subject(Str::title(Str::snake(class_basename($this), ' ')));
        }

        return $this;
    }

    /**
     * Add all of the attachments to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildAttachments($message)
    {
        foreach ($this->attachments as $attachment) {
            $message->attach($attachment['file'], $attachment['options']);
        }

        foreach ($this->rawAttachments as $attachment) {
            $message->attachData(
                $attachment['data'], $attachment['name'], $attachment['options']
            );
        }

        $this->buildDiskAttachments($message);

        return $this;
    }

    /**
     * Add all of the disk attachments to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return void
     */
    protected function buildDiskAttachments($message)
    {
        foreach ($this->diskAttachments as $attachment) {
            $storage = Container::getInstance()->make(
                FilesystemFactory::class
            )->disk($attachment['disk']);

            $message->attachData(
                $storage->get($attachment['path']),
                $attachment['name'] ?? basename($attachment['path']),
                array_merge(['mime' => $storage->mimeType($attachment['path'])], $attachment['options'])
            );
        }
    }

    /**
     * Add all defined tags to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildTags($message)
    {
        if ($this->tags) {
            foreach ($this->tags as $tag) {
                $message->getHeaders()->add(new TagHeader($tag));
            }
        }

        return $this;
    }

    /**
     * Add all defined metadata to the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function buildMetadata($message)
    {
        if ($this->metadata) {
            foreach ($this->metadata as $key => $value) {
                $message->getHeaders()->add(new MetadataHeader($key, $value));
            }
        }

        return $this;
    }

    /**
     * Run the callbacks for the message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @return $this
     */
    protected function runCallbacks($message)
    {
        foreach ($this->callbacks as $callback) {
            $callback($message->getSymfonyMessage());
        }

        return $this;
    }

    /**
     * Set the locale of the message.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Set the priority of this message.
     *
     * The value is an integer where 1 is the highest priority and 5 is the lowest.
     *
     * @param  int  $level
     * @return $this
     */
    public function priority($level = 3)
    {
        $this->callbacks[] = function ($message) use ($level) {
            $message->priority($level);
        };

        return $this;
    }

    /**
     * Set the sender of the message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        return $this->setAddress($address, $name, 'from');
    }

    /**
     * Determine if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasFrom($address, $name = null)
    {
        return $this->hasRecipient($address, $name, 'from');
    }

    /**
     * Set the recipients of the message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function to($address, $name = null)
    {
        if (! $this->locale && $address instanceof HasLocalePreference) {
            $this->locale($address->preferredLocale());
        }

        return $this->setAddress($address, $name, 'to');
    }

    /**
     * Determine if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasTo($address, $name = null)
    {
        return $this->hasRecipient($address, $name, 'to');
    }

    /**
     * Set the recipients of the message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function cc($address, $name = null)
    {
        return $this->setAddress($address, $name, 'cc');
    }

    /**
     * Determine if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasCc($address, $name = null)
    {
        return $this->hasRecipient($address, $name, 'cc');
    }

    /**
     * Set the recipients of the message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function bcc($address, $name = null)
    {
        return $this->setAddress($address, $name, 'bcc');
    }

    /**
     * Determine if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasBcc($address, $name = null)
    {
        return $this->hasRecipient($address, $name, 'bcc');
    }

    /**
     * Set the "reply to" address of the message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function replyTo($address, $name = null)
    {
        return $this->setAddress($address, $name, 'replyTo');
    }

    /**
     * Determine if the given replyTo is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return bool
     */
    public function hasReplyTo($address, $name = null)
    {
        return $this->hasRecipient($address, $name, 'replyTo');
    }

    /**
     * Set the recipients of the message.
     *
     * All recipients are stored internally as [['name' => ?, 'address' => ?]]
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @param  string  $property
     * @return $this
     */
    protected function setAddress($address, $name = null, $property = 'to')
    {
        if (empty($address)) {
            return $this;
        }

        foreach ($this->addressesToArray($address, $name) as $recipient) {
            $recipient = $this->normalizeRecipient($recipient);

            $this->{$property}[] = [
                'name' => $recipient->name ?? null,
                'address' => $recipient->email,
            ];
        }

        $this->{$property} = (new Collection($this->{$property}))
            ->reverse()
            ->unique('address')
            ->reverse()
            ->values()
            ->all();

        return $this;
    }

    /**
     * Convert the given recipient arguments to an array.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return array
     */
    protected function addressesToArray($address, $name)
    {
        if (! is_array($address) && ! $address instanceof Collection) {
            $address = is_string($name) ? [['name' => $name, 'email' => $address]] : [$address];
        }

        return $address;
    }

    /**
     * Convert the given recipient into an object.
     *
     * @param  mixed  $recipient
     * @return object
     */
    protected function normalizeRecipient($recipient)
    {
        if (is_array($recipient)) {
            if (array_values($recipient) === $recipient) {
                return (object) array_map(function ($email) {
                    return compact('email');
                }, $recipient);
            }

            return (object) $recipient;
        } elseif (is_string($recipient)) {
            return (object) ['email' => $recipient];
        } elseif ($recipient instanceof Address) {
            return (object) ['email' => $recipient->getAddress(), 'name' => $recipient->getName()];
        } elseif ($recipient instanceof Mailables\Address) {
            return (object) ['email' => $recipient->address, 'name' => $recipient->name];
        }

        return $recipient;
    }

    /**
     * Determine if the given recipient is set on the mailable.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @param  string  $property
     * @return bool
     */
    protected function hasRecipient($address, $name = null, $property = 'to')
    {
        if (empty($address)) {
            return false;
        }

        $expected = $this->normalizeRecipient(
            $this->addressesToArray($address, $name)[0]
        );

        $expected = [
            'name' => $expected->name ?? null,
            'address' => $expected->email,
        ];

        if ($this->hasEnvelopeRecipient($expected['address'], $expected['name'], $property)) {
            return true;
        }

        return (new Collection($this->{$property}))->contains(function ($actual) use ($expected) {
            if (! isset($expected['name'])) {
                return $actual['address'] == $expected['address'];
            }

            return $actual == $expected;
        });
    }

    /**
     * Determine if the mailable "envelope" method defines a recipient.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @param  string  $property
     * @return bool
     */
    private function hasEnvelopeRecipient($address, $name, $property)
    {
        return method_exists($this, 'envelope') && match ($property) {
            'from' => $this->envelope()->isFrom($address, $name),
            'to' => $this->envelope()->hasTo($address, $name),
            'cc' => $this->envelope()->hasCc($address, $name),
            'bcc' => $this->envelope()->hasBcc($address, $name),
            'replyTo' => $this->envelope()->hasReplyTo($address, $name),
        };
    }

    /**
     * Set the subject of the message.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Determine if the mailable has the given subject.
     *
     * @param  string  $subject
     * @return bool
     */
    public function hasSubject($subject)
    {
        return $this->subject === $subject ||
               (method_exists($this, 'envelope') && $this->envelope()->hasSubject($subject));
    }

    /**
     * Set the Markdown template for the message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function markdown($view, array $data = [])
    {
        $this->markdown = $view;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    /**
     * Set the view and view data for the message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function view($view, array $data = [])
    {
        $this->view = $view;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    /**
     * Set the rendered HTML content for the message.
     *
     * @param  string  $html
     * @return $this
     */
    public function html($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the plain text view for the message.
     *
     * @param  string  $textView
     * @param  array  $data
     * @return $this
     */
    public function text($textView, array $data = [])
    {
        $this->textView = $textView;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    /**
     * Set the view data for the message.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    /**
     * Attach a file to the message.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @param  array  $options
     * @return $this
     */
    public function attach($file, array $options = [])
    {
        if ($file instanceof Attachable) {
            $file = $file->toMailAttachment();
        }

        if ($file instanceof Attachment) {
            return $file->attachTo($this, $options);
        }

        $this->attachments = (new Collection($this->attachments))
            ->push(compact('file', 'options'))
            ->unique('file')
            ->all();

        return $this;
    }

    /**
     * Attach multiple files to the message.
     *
     * @param  array  $files
     * @return $this
     */
    public function attachMany($files)
    {
        foreach ($files as $file => $options) {
            if (is_int($file)) {
                $this->attach($options);
            } else {
                $this->attach($file, $options);
            }
        }

        return $this;
    }

    /**
     * Determine if the mailable has the given attachment.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @param  array  $options
     * @return bool
     */
    public function hasAttachment($file, array $options = [])
    {
        if ($file instanceof Attachable) {
            $file = $file->toMailAttachment();
        }

        if ($file instanceof Attachment && $this->hasEnvelopeAttachment($file, $options)) {
            return true;
        }

        if ($file instanceof Attachment) {
            $parts = $file->attachWith(
                fn ($path) => [$path, [
                    'as' => $options['as'] ?? $file->as,
                    'mime' => $options['mime'] ?? $file->mime,
                ]],
                fn ($data) => $this->hasAttachedData($data(), $options['as'] ?? $file->as, ['mime' => $options['mime'] ?? $file->mime])
            );

            if ($parts === true) {
                return true;
            }

            [$file, $options] = $parts === false
                ? [null, []]
                : $parts;
        }

        return (new Collection($this->attachments))->contains(
            fn ($attachment) => $attachment['file'] === $file && array_filter($attachment['options']) === array_filter($options)
        );
    }

    /**
     * Determine if the mailable has the given envelope attachment.
     *
     * @param  \Illuminate\Mail\Attachment  $attachment
     * @param  array  $options
     * @return bool
     */
    private function hasEnvelopeAttachment($attachment, $options = [])
    {
        if (! method_exists($this, 'envelope')) {
            return false;
        }

        $attachments = $this->attachments();

        return (new Collection(is_object($attachments) ? [$attachments] : $attachments))
            ->map(fn ($attached) => $attached instanceof Attachable ? $attached->toMailAttachment() : $attached)
            ->contains(fn ($attached) => $attached->isEquivalent($attachment, $options));
    }

    /**
     * Attach a file to the message from storage.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return $this
     */
    public function attachFromStorage($path, $name = null, array $options = [])
    {
        return $this->attachFromStorageDisk(null, $path, $name, $options);
    }

    /**
     * Attach a file to the message from storage.
     *
     * @param  string  $disk
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return $this
     */
    public function attachFromStorageDisk($disk, $path, $name = null, array $options = [])
    {
        $this->diskAttachments = (new Collection($this->diskAttachments))->push([
            'disk' => $disk,
            'path' => $path,
            'name' => $name ?? basename($path),
            'options' => $options,
        ])
            ->unique(fn ($file) => $file['name'].$file['disk'].$file['path'])
            ->all();

        return $this;
    }

    /**
     * Determine if the mailable has the given attachment from storage.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return bool
     */
    public function hasAttachmentFromStorage($path, $name = null, array $options = [])
    {
        return $this->hasAttachmentFromStorageDisk(null, $path, $name, $options);
    }

    /**
     * Determine if the mailable has the given attachment from a specific storage disk.
     *
     * @param  string  $disk
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return bool
     */
    public function hasAttachmentFromStorageDisk($disk, $path, $name = null, array $options = [])
    {
        return (new Collection($this->diskAttachments))->contains(
            fn ($attachment) => $attachment['disk'] === $disk
                && $attachment['path'] === $path
                && $attachment['name'] === ($name ?? basename($path))
                && $attachment['options'] === $options
        );
    }

    /**
     * Attach in-memory data as an attachment.
     *
     * @param  string  $data
     * @param  string  $name
     * @param  array  $options
     * @return $this
     */
    public function attachData($data, $name, array $options = [])
    {
        $this->rawAttachments = (new Collection($this->rawAttachments))
            ->push(compact('data', 'name', 'options'))
            ->unique(fn ($file) => $file['name'].$file['data'])
            ->all();

        return $this;
    }

    /**
     * Determine if the mailable has the given data as an attachment.
     *
     * @param  string  $data
     * @param  string  $name
     * @param  array  $options
     * @return bool
     */
    public function hasAttachedData($data, $name, array $options = [])
    {
        return (new Collection($this->rawAttachments))->contains(
            fn ($attachment) => $attachment['data'] === $data
                && $attachment['name'] === $name
                && array_filter($attachment['options']) === array_filter($options)
        );
    }

    /**
     * Add a tag header to the message when supported by the underlying transport.
     *
     * @param  string  $value
     * @return $this
     */
    public function tag($value)
    {
        $this->tags[] = $value;

        return $this;
    }

    /**
     * Determine if the mailable has the given tag.
     *
     * @param  string  $value
     * @return bool
     */
    public function hasTag($value)
    {
        return in_array($value, $this->tags) ||
               (method_exists($this, 'envelope') && in_array($value, $this->envelope()->tags));
    }

    /**
     * Add a metadata header to the message when supported by the underlying transport.
     *
     * @param  array|string  $key
     * @param  string|null  $value
     * @return $this
     */
    public function metadata($key, $value = null)
    {
        if (is_array($key)) {
            $this->metadata = array_merge($this->metadata, $key);
        } else {
            $this->metadata[$key] = $value;
        }

        return $this;
    }

    /**
     * Determine if the mailable has the given metadata.
     *
     * @param  string  $key
     * @param  string  $value
     * @return bool
     */
    public function hasMetadata($key, $value)
    {
        return (isset($this->metadata[$key]) && $this->metadata[$key] === $value) ||
               (method_exists($this, 'envelope') && $this->envelope()->hasMetadata($key, $value));
    }

    /**
     * Assert that the mailable is from the given address.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertFrom($address, $name = null)
    {
        $this->renderForAssertions();

        $expected = $this->formatAssertionRecipient($address, $name);
        $actual = $this->formatActualRecipients($this->from);

        PHPUnit::assertTrue(
            $this->hasFrom($address, $name),
            "Email was not from expected address.\nExpected: [{$expected}]\nActual: [{$actual}]"
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given recipient.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertTo($address, $name = null)
    {
        $this->renderForAssertions();

        $expected = $this->formatAssertionRecipient($address, $name);
        $actual = $this->formatActualRecipients($this->to);

        PHPUnit::assertTrue(
            $this->hasTo($address, $name),
            "Did not see expected recipient in email 'to' recipients.\nExpected: [{$expected}]\nActual: [{$actual}]"
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given recipient.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertHasTo($address, $name = null)
    {
        return $this->assertTo($address, $name);
    }

    /**
     * Assert that the mailable has the given recipient.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertHasCc($address, $name = null)
    {
        $this->renderForAssertions();

        $expected = $this->formatAssertionRecipient($address, $name);
        $actual = $this->formatActualRecipients($this->cc);

        PHPUnit::assertTrue(
            $this->hasCc($address, $name),
            "Did not see expected recipient in email 'cc' recipients.\nExpected: [{$expected}]\nActual: [{$actual}]"
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given recipient.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertHasBcc($address, $name = null)
    {
        $this->renderForAssertions();

        $expected = $this->formatAssertionRecipient($address, $name);
        $actual = $this->formatActualRecipients($this->bcc);

        PHPUnit::assertTrue(
            $this->hasBcc($address, $name),
            "Did not see expected recipient in email 'bcc' recipients.\nExpected: [{$expected}]\nActual: [{$actual}]"
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given "reply to" address.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function assertHasReplyTo($address, $name = null)
    {
        $this->renderForAssertions();

        $expected = $this->formatAssertionRecipient($address, $name);
        $actual = $this->formatActualRecipients($this->replyTo);

        PHPUnit::assertTrue(
            $this->hasReplyTo($address, $name),
            "Did not see expected address as email 'reply to' recipient.\nExpected: [{$expected}]\nActual: [{$actual}]"
        );

        return $this;
    }

    /**
     * Format the mailable recipient for display in an assertion message.
     *
     * @param  object|array|string  $address
     * @param  string|null  $name
     * @return string
     */
    private function formatAssertionRecipient($address, $name = null)
    {
        if (! is_string($address)) {
            $address = json_encode($address);
        }

        if (filled($name)) {
            $address .= ' ('.$name.')';
        }

        return $address;
    }

    /**
     * Format actual recipients for display in assertion messages.
     *
     * @param  array  $recipients
     * @return string
     */
    private function formatActualRecipients($recipients)
    {
        if (empty($recipients)) {
            return 'none';
        }

        return (new Collection($recipients))->map(function ($recipient) {
            $formatted = $recipient['address'];
            if (! empty($recipient['name'])) {
                $formatted .= ' ('.$recipient['name'].')';
            }

            return $formatted;
        })->implode(', ');
    }

    /**
     * Assert that the mailable has the given subject.
     *
     * @param  string  $subject
     * @return $this
     */
    public function assertHasSubject($subject)
    {
        $this->renderForAssertions();

        $actualSubject = $this->subject ?: (method_exists($this, 'envelope') ? $this->envelope()->subject : null) ?: Str::title(Str::snake(class_basename($this), ' '));

        PHPUnit::assertTrue(
            $this->hasSubject($subject),
            "Email subject does not match expected value.\nExpected: [{$subject}]\nActual: [{$actualSubject}]"
        );

        return $this;
    }

    /**
     * Assert that the given text is present in the HTML email body.
     *
     * @param  string  $string
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeInHtml($string, $escape = true)
    {
        $string = $escape ? EncodedHtmlString::convert($string, withQuote: true) : $string;

        [$html] = $this->renderForAssertions();

        PHPUnit::assertStringContainsString(
            $string,
            $html,
            "Did not see expected text [{$string}] within email body."
        );

        return $this;
    }

    /**
     * Assert that the given text is not present in the HTML email body.
     *
     * @param  string  $string
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSeeInHtml($string, $escape = true)
    {
        $string = $escape ? EncodedHtmlString::convert($string, withQuote: true) : $string;

        [$html] = $this->renderForAssertions();

        PHPUnit::assertStringNotContainsString(
            $string,
            $html,
            "Saw unexpected text [{$string}] within email body."
        );

        return $this;
    }

    /**
     * Assert that the given text strings are present in order in the HTML email body.
     *
     * @param  array  $strings
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeInOrderInHtml($strings, $escape = true)
    {
        $strings = $escape ? array_map(function ($string) {
            return EncodedHtmlString::convert($string, withQuote: true);
        }, $strings) : $strings;

        [$html] = $this->renderForAssertions();

        PHPUnit::assertThat($strings, new SeeInOrder($html));

        return $this;
    }

    /**
     * Assert that the given text is present in the plain-text email body.
     *
     * @param  string  $string
     * @return $this
     */
    public function assertSeeInText($string)
    {
        [, $text] = $this->renderForAssertions();

        PHPUnit::assertStringContainsString(
            $string,
            $text,
            "Did not see expected text [{$string}] within text email body."
        );

        return $this;
    }

    /**
     * Assert that the given text is not present in the plain-text email body.
     *
     * @param  string  $string
     * @return $this
     */
    public function assertDontSeeInText($string)
    {
        [, $text] = $this->renderForAssertions();

        PHPUnit::assertStringNotContainsString(
            $string,
            $text,
            "Saw unexpected text [{$string}] within text email body."
        );

        return $this;
    }

    /**
     * Assert that the given text strings are present in order in the plain-text email body.
     *
     * @param  array  $strings
     * @return $this
     */
    public function assertSeeInOrderInText($strings)
    {
        [, $text] = $this->renderForAssertions();

        PHPUnit::assertThat($strings, new SeeInOrder($text));

        return $this;
    }

    /**
     * Assert the mailable has the given attachment.
     *
     * @param  string|\Illuminate\Contracts\Mail\Attachable|\Illuminate\Mail\Attachment  $file
     * @param  array  $options
     * @return $this
     */
    public function assertHasAttachment($file, array $options = [])
    {
        $this->renderForAssertions();

        PHPUnit::assertTrue(
            $this->hasAttachment($file, $options),
            'Did not find the expected attachment.'
        );

        return $this;
    }

    /**
     * Assert the mailable has the given data as an attachment.
     *
     * @param  string  $data
     * @param  string  $name
     * @param  array  $options
     * @return $this
     */
    public function assertHasAttachedData($data, $name, array $options = [])
    {
        $this->renderForAssertions();

        PHPUnit::assertTrue(
            $this->hasAttachedData($data, $name, $options),
            'Did not find the expected attachment.'
        );

        return $this;
    }

    /**
     * Assert the mailable has the given attachment from storage.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return $this
     */
    public function assertHasAttachmentFromStorage($path, $name = null, array $options = [])
    {
        $this->renderForAssertions();

        PHPUnit::assertTrue(
            $this->hasAttachmentFromStorage($path, $name, $options),
            'Did not find the expected attachment.'
        );

        return $this;
    }

    /**
     * Assert the mailable has the given attachment from a specific storage disk.
     *
     * @param  string  $disk
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $options
     * @return $this
     */
    public function assertHasAttachmentFromStorageDisk($disk, $path, $name = null, array $options = [])
    {
        $this->renderForAssertions();

        PHPUnit::assertTrue(
            $this->hasAttachmentFromStorageDisk($disk, $path, $name, $options),
            'Did not find the expected attachment.'
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given tag.
     *
     * @param  string  $tag
     * @return $this
     */
    public function assertHasTag($tag)
    {
        $this->renderForAssertions();

        $actualTags = method_exists($this, 'envelope') ? array_merge($this->tags, $this->envelope()->tags) : $this->tags;
        $actualTagsString = empty($actualTags) ? 'none' : implode(', ', $actualTags);

        PHPUnit::assertTrue(
            $this->hasTag($tag),
            "Did not see expected tag in email tags.\nExpected: [{$tag}]\nActual: [{$actualTagsString}]"
        );

        return $this;
    }

    /**
     * Assert that the mailable has the given metadata.
     *
     * @param  string  $key
     * @param  string  $value
     * @return $this
     */
    public function assertHasMetadata($key, $value)
    {
        $this->renderForAssertions();

        $actualMetadata = method_exists($this, 'envelope') ? array_merge($this->metadata, $this->envelope()->metadata) : $this->metadata;
        $actualValue = $actualMetadata[$key] ?? null;
        $actualString = $actualValue !== null ? "[{$key}] => [{$actualValue}]" : "key [{$key}] not found";

        PHPUnit::assertTrue(
            $this->hasMetadata($key, $value),
            "Email metadata does not match expected value.\nExpected: [{$key}] => [{$value}]\nActual: {$actualString}"
        );

        return $this;
    }

    /**
     * Render the HTML and plain-text version of the mailable into views for assertions.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function renderForAssertions()
    {
        if ($this->assertionableRenderStrings) {
            return $this->assertionableRenderStrings;
        }

        return $this->assertionableRenderStrings = $this->withLocale($this->locale, function () {
            $this->prepareMailableForDelivery();

            $html = Container::getInstance()->make('mailer')->render(
                $view = $this->buildView(), $this->buildViewData()
            );

            if (is_array($view) && isset($view[1])) {
                $text = $view[1];
            }

            $text ??= $view['text'] ?? '';

            if (! empty($text) && ! $text instanceof Htmlable) {
                $text = Container::getInstance()->make('mailer')->render(
                    $text, $this->buildViewData()
                );
            }

            return [(string) $html, (string) $text];
        });
    }

    /**
     * Prepare the mailable instance for delivery.
     *
     * @return void
     */
    protected function prepareMailableForDelivery()
    {
        if (method_exists($this, 'build')) {
            Container::getInstance()->call([$this, 'build']);
        }

        $this->ensureHeadersAreHydrated();
        $this->ensureEnvelopeIsHydrated();
        $this->ensureContentIsHydrated();
        $this->ensureAttachmentsAreHydrated();
    }

    /**
     * Ensure the mailable's headers are hydrated from the "headers" method.
     *
     * @return void
     */
    private function ensureHeadersAreHydrated()
    {
        if (! method_exists($this, 'headers')) {
            return;
        }

        $headers = $this->headers();

        $this->withSymfonyMessage(function ($message) use ($headers) {
            if ($headers->messageId) {
                $message->getHeaders()->addIdHeader('Message-Id', $headers->messageId);
            }

            if (count($headers->references) > 0) {
                $message->getHeaders()->addTextHeader('References', $headers->referencesString());
            }

            foreach ($headers->text as $key => $value) {
                $message->getHeaders()->addTextHeader($key, $value);
            }
        });
    }

    /**
     * Ensure the mailable's "envelope" data is hydrated from the "envelope" method.
     *
     * @return void
     */
    private function ensureEnvelopeIsHydrated()
    {
        if (! method_exists($this, 'envelope')) {
            return;
        }

        $envelope = $this->envelope();

        if (isset($envelope->from)) {
            $this->from($envelope->from->address, $envelope->from->name);
        }

        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            foreach ($envelope->{$type} as $address) {
                $this->{$type}($address->address, $address->name);
            }
        }

        if ($envelope->subject) {
            $this->subject($envelope->subject);
        }

        foreach ($envelope->tags as $tag) {
            $this->tag($tag);
        }

        foreach ($envelope->metadata as $key => $value) {
            $this->metadata($key, $value);
        }

        foreach ($envelope->using as $callback) {
            $this->withSymfonyMessage($callback);
        }
    }

    /**
     * Ensure the mailable's content is hydrated from the "content" method.
     *
     * @return void
     */
    private function ensureContentIsHydrated()
    {
        if (! method_exists($this, 'content')) {
            return;
        }

        $content = $this->content();

        if ($content->view) {
            $this->view($content->view);
        }

        if ($content->html) {
            $this->view($content->html);
        }

        if ($content->text) {
            $this->text($content->text);
        }

        if ($content->markdown) {
            $this->markdown($content->markdown);
        }

        if ($content->htmlString) {
            $this->html($content->htmlString);
        }

        foreach ($content->with as $key => $value) {
            $this->with($key, $value);
        }
    }

    /**
     * Ensure the mailable's attachments are hydrated from the "attachments" method.
     *
     * @return void
     */
    private function ensureAttachmentsAreHydrated()
    {
        if (! method_exists($this, 'attachments')) {
            return;
        }

        $attachments = $this->attachments();

        (new Collection(is_object($attachments) ? [$attachments] : $attachments))
            ->each(function ($attachment) {
                $this->attach($attachment);
            });
    }

    /**
     * Determine if the mailable will be sent by the given mailer.
     *
     * @param  string  $mailer
     * @return bool
     */
    public function usesMailer($mailer)
    {
        return $this->mailer === $mailer;
    }

    /**
     * Set the name of the mailer that should send the message.
     *
     * @param  string  $mailer
     * @return $this
     */
    public function mailer($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * Register a callback to be called with the Symfony message instance.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function withSymfonyMessage($callback)
    {
        $this->callbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to be called while building the view data.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function buildViewDataUsing(callable $callback)
    {
        static::$viewDataCallback = $callback;
    }

    /**
     * Dynamically bind parameters to the message.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (str_starts_with($method, 'with')) {
            return $this->with(Str::camel(substr($method, 4)), $parameters[0]);
        }

        static::throwBadMethodCallException($method);
    }
}
