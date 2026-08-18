<?php
namespace App\Enums;
enum DeliveryStatus:string { case Delivered='delivered'; case NotDelivered='not_delivered'; case CustomerAbsent='customer_absent'; case Paused='paused'; case Cancelled='cancelled'; }
