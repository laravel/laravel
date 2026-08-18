<?php
namespace App\Enums;
enum Role:string { case PlatformAdmin='platform_admin'; case CompanyAdmin='company_admin'; case Supervisor='supervisor'; case CollectionEmployee='collection_employee'; case DeliveryEmployee='delivery_employee'; }
