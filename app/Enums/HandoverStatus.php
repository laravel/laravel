<?php
namespace App\Enums;
enum HandoverStatus:string { case NotSubmitted='not_submitted'; case PartiallySubmitted='partially_submitted'; case Submitted='submitted'; case Verified='verified'; case Rejected='rejected'; }
