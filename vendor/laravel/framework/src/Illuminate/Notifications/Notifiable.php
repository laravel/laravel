<?php

namespace Illuminate\Notifications;

trait Notifiable
{
    use HasDatabaseNotifications, RoutesNotifications;
}
