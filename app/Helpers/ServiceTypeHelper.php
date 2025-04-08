<?php

namespace App\Helpers;

class ServiceTypeHelper
{
    /**
     * أنواع الخدمات المتاحة في النظام
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'security_approval' => 'موافقات أمنية',
            'transportation' => 'نقل بري',
            'hajj_umrah' => 'حج وعمرة',
            'flight' => 'تذاكر طيران',
            'passport' => 'إصدار جوازات',
            'visa' => 'تأشيرات',
            'tourism' => 'سياحة وسفر',
            'other' => 'خدمات أخرى',
        ];
    }

    /**
     * الحصول على الاسم المحلي لنوع الخدمة
     *
     * @param string $type
     * @return string
     */
    public static function getLocalizedType($type)
    {
        $types = self::getTypes();
        return $types[$type] ?? $type;
    }
}
