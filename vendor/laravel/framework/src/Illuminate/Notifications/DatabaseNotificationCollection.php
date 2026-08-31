<?php

namespace Illuminate\Notifications;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @template TKey of array-key
 * @template TModel of DatabaseNotification
 *
 * @extends \Illuminate\Database\Eloquent\Collection<TKey, TModel>
 */
class DatabaseNotificationCollection extends EloquentCollection
{
    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->each->markAsRead();
    }

    /**
     * Mark all notifications as unread.
     *
     * @return void
     */
    public function markAsUnread()
    {
        $this->each->markAsUnread();
    }
}
