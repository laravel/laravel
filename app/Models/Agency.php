<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'contact_email',
        'website',
        'tax_number',
        'commercial_register',
        'address',
        'logo_path',
        'default_currency',
        'theme_color',
        'agency_language',
        'payment_settings',
        'notification_settings',
        'social_media_instagram',
        'social_media_twitter',
        'social_media_facebook',
        'social_media_linkedin',
    ];

    protected $casts = [
        'payment_settings' => 'array',
        'notification_settings' => 'array',
        'email_settings' => 'array',
        'commission_settings' => 'array',
        'auto_convert_prices' => 'boolean',
    ];

    /**
     * Get the users for the agency.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the services for the agency.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the requests for the agency.
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Get the subagents for the agency.
     */
    public function subagents()
    {
        return $this->users()->where('user_type', 'subagent');
    }

    /**
     * Get the customers for the agency.
     */
    public function customers()
    {
        return $this->users()->where('user_type', 'customer');
    }

    /**
     * Get the managers for the agency.
     */
    public function managers()
    {
        return $this->users()->where('user_type', 'agency');
    }

    /**
     * Get default notification settings
     */
    public function getDefaultNotificationSettings()
    {
        return [
            'enable_email_notifications' => true,
            'enable_system_notifications' => true,
            'notify_on_new_request' => true,
            'notify_on_new_quote' => true,
            'notify_on_status_change' => true,
            'daily_summary' => false,
            'notify_customers' => true,
            'notify_subagents' => true,
        ];
    }

    /**
     * Get default email settings
     */
    public function getDefaultEmailSettings()
    {
        return [
            'sender_name' => $this->name,
            'sender_address' => $this->contact_email ?? 'no-reply@example.com',
            'template' => 'default',
            'signature' => "مع أطيب التحيات،\n{$this->name}",
            'footer_text' => "© " . date('Y') . " {$this->name}. جميع الحقوق محفوظة.",
        ];
    }

    /**
     * Get default commission settings
     */
    public function getDefaultCommissionSettings()
    {
        return [
            'minimum_amount' => 0,
            'calculation_method' => 'percentage',
            'auto_calculate' => true,
            'apply_tax' => false,
            'tax_rate' => 15,
        ];
    }

    /**
     * Get notification settings with defaults
     */
    public function getNotificationSettingsAttribute($value)
    {
        $settings = json_decode($value, true) ?: [];
        return array_merge($this->getDefaultNotificationSettings(), $settings);
    }

    /**
     * Get email settings with defaults
     */
    public function getEmailSettingsAttribute($value)
    {
        $settings = json_decode($value, true) ?: [];
        return array_merge($this->getDefaultEmailSettings(), $settings);
    }

    /**
     * Get commission settings with defaults
     */
    public function getCommissionSettingsAttribute($value)
    {
        $settings = json_decode($value, true) ?: [];
        return array_merge($this->getDefaultCommissionSettings(), $settings);
    }
}
