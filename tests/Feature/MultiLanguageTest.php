<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiLanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار تغيير لغة التطبيق.
     */
    public function test_can_change_application_language()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->post('/change-language', ['locale' => 'en']);
        
        $response->assertStatus(302);
        $this->assertEquals('en', session('locale'));
        
        $response = $this->actingAs($user)
                         ->post('/change-language', ['locale' => 'ar']);
        
        $response->assertStatus(302);
        $this->assertEquals('ar', session('locale'));
    }
    
    /**
     * اختبار عرض الترجمات الصحيحة.
     */
    public function test_displays_correct_translations()
    {
        $user = User::factory()->create();
        
        // اختبار اللغة العربية
        $response = $this->actingAs($user)
                         ->withSession(['locale' => 'ar'])
                         ->get('/dashboard');
        
        $response->assertSee('لوحة التحكم');
        
        // اختبار اللغة الإنجليزية
        $response = $this->actingAs($user)
                         ->withSession(['locale' => 'en'])
                         ->get('/dashboard');
        
        $response->assertSee('Dashboard');
    }
}
