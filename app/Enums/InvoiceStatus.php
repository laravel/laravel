<?php
namespace App\Enums;
enum InvoiceStatus:string { case Unpaid='unpaid'; case PartiallyPaid='partially_paid'; case FullyPaid='fully_paid'; case AdvancePaid='advance_paid'; }
