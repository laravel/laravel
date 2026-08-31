<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Exception;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Contracts\Notifications\Factory as NotificationFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

class NotificationFake implements Fake, NotificationDispatcher, NotificationFactory
{
    use Macroable, ReflectsClosures;

    /**
     * All of the notifications that have been sent.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * Locale used when sending notifications.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Indicates if notifications should be serialized and restored when pushed to the queue.
     *
     * @var bool
     */
    protected $serializeAndRestore = false;

    /**
     * Assert if a notification was sent on-demand based on a truth-test callback.
     *
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertSentOnDemand($notification, $callback = null)
    {
        $this->assertSentTo(new AnonymousNotifiable, $notification, $callback);
    }

    /**
     * Assert if a notification was sent based on a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertSentTo($notifiable, $notification, $callback = null)
    {
        if (is_array($notifiable) || $notifiable instanceof Collection) {
            if (count($notifiable) === 0) {
                throw new Exception('No notifiable given.');
            }

            foreach ($notifiable as $singleNotifiable) {
                $this->assertSentTo($singleNotifiable, $notification, $callback);
            }

            return;
        }

        if ($notification instanceof Closure) {
            [$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
        }

        if (is_numeric($callback)) {
            return $this->assertSentToTimes($notifiable, $notification, $callback);
        }

        PHPUnit::assertTrue(
            $this->sent($notifiable, $notification, $callback)->count() > 0,
            "The expected [{$notification}] notification was not sent."
        );
    }

    /**
     * Assert if a notification was sent on-demand a number of times.
     *
     * @param  string  $notification
     * @param  int  $times
     * @return void
     */
    public function assertSentOnDemandTimes($notification, $times = 1)
    {
        $this->assertSentToTimes(new AnonymousNotifiable, $notification, $times);
    }

    /**
     * Assert if a notification was sent a number of times.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @param  int  $times
     * @return void
     */
    public function assertSentToTimes($notifiable, $notification, $times = 1)
    {
        $count = $this->sent($notifiable, $notification)->count();

        PHPUnit::assertSame(
            $times, $count,
            "Expected [{$notification}] to be sent {$times} times, but was sent {$count} times."
        );
    }

    /**
     * Determine if a notification was sent based on a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertNotSentTo($notifiable, $notification, $callback = null)
    {
        if (is_array($notifiable) || $notifiable instanceof Collection) {
            if (count($notifiable) === 0) {
                throw new Exception('No notifiable given.');
            }

            foreach ($notifiable as $singleNotifiable) {
                $this->assertNotSentTo($singleNotifiable, $notification, $callback);
            }

            return;
        }

        if ($notification instanceof Closure) {
            [$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
        }

        PHPUnit::assertCount(
            0, $this->sent($notifiable, $notification, $callback),
            "The unexpected [{$notification}] notification was sent."
        );
    }

    /**
     * Assert that no notifications were sent.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        $notificationNames = (new Collection($this->notifications))
            ->map(fn ($notifiableModels) => (new Collection($notifiableModels))
                ->map(fn ($notifiables) => (new Collection($notifiables))->keys())
            )
            ->flatten()->join("\n- ");

        PHPUnit::assertEmpty($this->notifications, "The following notifications were sent unexpectedly:\n\n- $notificationNames\n");
    }

    /**
     * Assert that no notifications were sent to the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return void
     *
     * @throws \Exception
     */
    public function assertNothingSentTo($notifiable)
    {
        if (is_array($notifiable) || $notifiable instanceof Collection) {
            if (count($notifiable) === 0) {
                throw new Exception('No notifiable given.');
            }

            foreach ($notifiable as $singleNotifiable) {
                $this->assertNothingSentTo($singleNotifiable);
            }

            return;
        }

        PHPUnit::assertEmpty(
            $this->notifications[get_class($notifiable)][$notifiable->getKey() ?? ''] ?? [],
            'Notifications were sent unexpectedly.',
        );
    }

    /**
     * Assert the total amount of times a notification was sent.
     *
     * @param  string  $notification
     * @param  int  $expectedCount
     * @return void
     */
    public function assertSentTimes($notification, $expectedCount)
    {
        $actualCount = (new Collection($this->notifications))
            ->flatten(1)
            ->reduce(fn ($count, $sent) => $count + count($sent[$notification] ?? []), 0);

        PHPUnit::assertSame(
            $expectedCount, $actualCount,
            sprintf(
                "Expected [{$notification}] to be sent {$expectedCount} %s, but was sent {$actualCount} %s.",
                Str::plural('time', $expectedCount),
                Str::plural('time', $actualCount)
            )
        );
    }

    /**
     * Assert the total count of notification that were sent.
     *
     * @param  int  $expectedCount
     * @return void
     */
    public function assertCount($expectedCount)
    {
        $actualCount = (new Collection($this->notifications))->flatten(3)->count();

        PHPUnit::assertSame(
            $expectedCount, $actualCount,
            "Expected {$expectedCount} notifications to be sent, but {$actualCount} were sent."
        );
    }

    /**
     * Get all of the notifications matching a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function sent($notifiable, $notification, $callback = null)
    {
        if (! $this->hasSent($notifiable, $notification)) {
            return new Collection;
        }

        $callback = $callback ?: fn () => true;

        $notifications = new Collection($this->notificationsFor($notifiable, $notification));

        return $notifications->filter(
            fn ($arguments) => $callback(...array_values($arguments))
        )->pluck('notification');
    }

    /**
     * Determine if there are more notifications left to inspect.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @return bool
     */
    public function hasSent($notifiable, $notification)
    {
        return ! empty($this->notificationsFor($notifiable, $notification));
    }

    /**
     * Get all of the notifications for a notifiable entity by type.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @return array
     */
    protected function notificationsFor($notifiable, $notification)
    {
        return $this->notifications[get_class($notifiable)][(string) $notifiable->getKey()][$notification] ?? [];
    }

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $this->sendNow($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, ?array $channels = null)
    {
        if (! $notifiables instanceof Collection && ! is_array($notifiables)) {
            $notifiables = [$notifiables];
        }

        foreach ($notifiables as $notifiable) {
            if (! $notification->id) {
                $notification->id = (string) Str::uuid();
            }

            $notifiableChannels = $channels ?: $notification->via($notifiable);

            if (method_exists($notification, 'shouldSend')) {
                $notifiableChannels = array_filter(
                    $notifiableChannels,
                    fn ($channel) => $notification->shouldSend($notifiable, $channel) !== false
                );
            }

            if (empty($notifiableChannels)) {
                continue;
            }

            $this->notifications[get_class($notifiable)][(string) $notifiable->getKey()][get_class($notification)][] = [
                'notification' => $this->serializeAndRestore && $notification instanceof ShouldQueue
                    ? $this->serializeAndRestoreNotification($notification)
                    : $notification,
                'channels' => $notifiableChannels,
                'notifiable' => $notifiable,
                'locale' => $notification->locale ?? $this->locale ?? value(function () use ($notifiable) {
                    if ($notifiable instanceof HasLocalePreference) {
                        return $notifiable->preferredLocale();
                    }
                }),
            ];
        }
    }

    /**
     * Get a channel instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        //
    }

    /**
     * Set the locale of notifications.
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
     * Specify if notification should be serialized and restored when being "pushed" to the queue.
     *
     * @param  bool  $serializeAndRestore
     * @return $this
     */
    public function serializeAndRestore(bool $serializeAndRestore = true)
    {
        $this->serializeAndRestore = $serializeAndRestore;

        return $this;
    }

    /**
     * Serialize and unserialize the notification to simulate the queueing process.
     *
     * @param  mixed  $notification
     * @return mixed
     */
    protected function serializeAndRestoreNotification($notification)
    {
        return unserialize(serialize($notification));
    }

    /**
     * Get the notifications that have been sent.
     *
     * @return array
     */
    public function sentNotifications()
    {
        return $this->notifications;
    }
}
