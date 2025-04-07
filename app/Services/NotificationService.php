<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;

class NotificationService
{
    /**
     * Crear una notificación para un usuario
     */
    public static function notify(User $user, string $title, string $message, string $type = 'info', string $actionUrl = null, array $data = [])
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Notificar a un usuario de un nuevo pedido
     */
    public static function notifyNewRequest(ServiceRequest $request)
    {
        // Notificar a la agencia
        $agency = User::where('id', $request->agency_id)->first();
        if ($agency) {
            self::notify(
                $agency,
                'طلب جديد',
                'تم إنشاء طلب جديد من قبل العميل ' . $request->customer->name,
                'info',
                route('agency.requests.show', $request),
                ['request_id' => $request->id]
            );
        }
        
        // Notificar a los سبوكلاء المرتبطين بهذه الخدمة
        $subagents = $request->service->subagents;
        foreach ($subagents as $subagent) {
            self::notify(
                $subagent,
                'طلب متاح جديد',
                'هناك طلب جديد متاح لتقديم عرض سعر: ' . $request->service->name,
                'info',
                route('subagent.requests.show', $request),
                ['request_id' => $request->id]
            );
        }
    }

    /**
     * Notificar عند تقديم عرض سعر جديد
     */
    public static function notifyNewQuote(Quote $quote)
    {
        // Notificar للوكالة
        $agency = User::where('id', $quote->request->agency_id)->first();
        if ($agency) {
            self::notify(
                $agency,
                'عرض سعر جديد',
                'تم تقديم عرض سعر جديد من قبل ' . $quote->subagent->name,
                'info',
                route('agency.quotes.show', $quote),
                ['quote_id' => $quote->id]
            );
        }
    }

    /**
     * Notificar عند الموافقة على عرض السعر
     */
    public static function notifyQuoteApproved(Quote $quote)
    {
        // Notificar للسبوكيل
        self::notify(
            $quote->subagent,
            'تمت الموافقة على عرضك',
            'تمت الموافقة على عرض السعر الخاص بك للطلب رقم #' . $quote->request_id,
            'success',
            route('subagent.quotes.show', $quote),
            ['quote_id' => $quote->id]
        );
        
        // Notificar للعميل
        self::notify(
            $quote->request->customer,
            'عرض سعر متاح',
            'يوجد عرض سعر جديد متاح للطلب رقم #' . $quote->request_id,
            'info',
            route('customer.quotes.show', $quote),
            ['quote_id' => $quote->id]
        );
    }

    /**
     * Notificar عند رفض عرض السعر
     */
    public static function notifyQuoteRejected(Quote $quote)
    {
        self::notify(
            $quote->subagent,
            'تم رفض عرضك',
            'تم رفض عرض السعر الخاص بك للطلب رقم #' . $quote->request_id,
            'danger',
            route('subagent.quotes.show', $quote),
            ['quote_id' => $quote->id]
        );
    }

    /**
     * Notificar عند قبول العميل لعرض السعر
     */
    public static function notifyQuoteAcceptedByCustomer(Quote $quote)
    {
        // Notificar للوكالة
        $agency = User::where('id', $quote->request->agency_id)->first();
        if ($agency) {
            self::notify(
                $agency,
                'تم قبول عرض السعر',
                'تم قبول عرض السعر من قبل العميل ' . $quote->request->customer->name,
                'success',
                route('agency.quotes.show', $quote),
                ['quote_id' => $quote->id]
            );
        }
        
        // Notificar للسبوكيل
        self::notify(
            $quote->subagent,
            'تم قبول عرضك',
            'تم قبول عرض السعر الخاص بك من قبل العميل ' . $quote->request->customer->name,
            'success',
            route('subagent.quotes.show', $quote),
            ['quote_id' => $quote->id]
        );
    }
}
