<?php
namespace Tests\Feature;
use App\Enums\{InvoiceStatus,PaymentMode,Role}; use App\Models\{Building,Company,Customer,Invoice,Room,User}; use Illuminate\Auth\Notifications\ResetPassword; use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken; use Illuminate\Support\Facades\{Hash,Notification,Password}; use Laravel\Sanctum\Sanctum; use Tests\TestCase;
class AdminPanelAndLocalizationTest extends TestCase { use RefreshDatabase;
 private function setupTenant():array{$company=Company::create(['name'=>'Panel Co','code'=>'PANEL']);$admin=User::factory()->create(['company_id'=>$company->id,'role'=>Role::CompanyAdmin]);$building=Building::withoutGlobalScopes()->create(['company_id'=>$company->id,'name'=>'Block 1','code'=>'B1']);$room=Room::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'number'=>'11','capacity'=>3]);$customer=Customer::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'room_id'=>$room->id,'customer_code'=>'P1','name'=>'Panel Worker','passport_number'=>'PP1','start_date'=>today()]);return compact('company','admin','building','room','customer');}

 public function test_forgot_password_flow_issues_a_working_reset_link():void{
  Notification::fake();$x=$this->setupTenant();$this->withoutMiddleware(ValidateCsrfToken::class);
  $this->post('/forgot-password',['email'=>$x['admin']->email])->assertRedirect();
  Notification::assertSentTo($x['admin'],ResetPassword::class);
  $token=Password::createToken($x['admin']);
  $this->post('/reset-password',['token'=>$token,'email'=>$x['admin']->email,'password'=>'BrandNewPass!123','password_confirmation'=>'BrandNewPass!123'])->assertRedirect('/login');
  $this->assertTrue(Hash::check('BrandNewPass!123',$x['admin']->fresh()->password));
 }

 public function test_admin_can_create_and_toggle_employees_through_the_web_panel():void{
  $x=$this->setupTenant();$this->withoutMiddleware(ValidateCsrfToken::class);
  $this->actingAs($x['admin'])->post('/employees',['name'=>'Field Collector','email'=>'collector@panel.test','phone'=>'971500000010','password'=>'VeryStrong!123','role'=>'collection_employee','building_ids'=>[$x['building']->id]])->assertRedirect();
  $employee=User::where('email','collector@panel.test')->firstOrFail();
  $this->assertTrue($employee->is_active);
  $this->assertDatabaseHas('building_user',['building_id'=>$x['building']->id,'user_id'=>$employee->id]);
  $this->actingAs($x['admin'])->post("/employees/{$employee->uuid}/toggle")->assertRedirect();
  $this->assertFalse($employee->fresh()->is_active);
 }

 public function test_admin_can_update_building_room_customer_and_plan_through_the_web_panel():void{
  $x=$this->setupTenant();$this->withoutMiddleware(ValidateCsrfToken::class);
  $this->actingAs($x['admin'])->post("/buildings/{$x['building']->uuid}",['name'=>'Block 1 Renamed','code'=>'B1','address'=>'New Address','is_active'=>'1'])->assertRedirect();
  $this->assertDatabaseHas('buildings',['id'=>$x['building']->id,'name'=>'Block 1 Renamed']);
  $this->actingAs($x['admin'])->post("/rooms/{$x['room']->uuid}",['number'=>'11A','capacity'=>5,'delivery_enabled'=>'1'])->assertRedirect();
  $this->assertDatabaseHas('rooms',['id'=>$x['room']->id,'number'=>'11A','capacity'=>5]);
  $this->actingAs($x['admin'])->post("/customers/{$x['customer']->uuid}",['name'=>'Renamed Worker','room_id'=>$x['room']->id,'is_active'=>'0'])->assertRedirect();
  $this->assertDatabaseHas('customers',['id'=>$x['customer']->id,'name'=>'Renamed Worker','is_active'=>false]);
 }

 public function test_low_balance_threshold_generates_a_notification():void{
  $x=$this->setupTenant();$x['company']->update(['low_balance_threshold'=>100]);
  Invoice::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'customer_id'=>$x['customer']->id,'invoice_number'=>'PANEL-I1','period_start'=>today()->startOfMonth(),'period_end'=>today()->endOfMonth(),'current_charges'=>150,'total_payable'=>150,'balance'=>150,'status'=>InvoiceStatus::Unpaid,'due_date'=>today()->addDays(5)]);
  $this->artisan('mealflow:reminders')->assertExitCode(0);
  $this->assertDatabaseHas('notification_logs',['company_id'=>$x['company']->id,'customer_id'=>$x['customer']->id,'type'=>'low_balance']);
 }

 public function test_reminder_and_receipt_messages_are_translated_for_the_companys_locale():void{
  $defaultLocale=app()->getLocale();
  $x=$this->setupTenant();$x['company']->update(['locale'=>'ur']);
  $invoice=Invoice::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'customer_id'=>$x['customer']->id,'invoice_number'=>'PANEL-I2','period_start'=>today()->subMonth()->startOfMonth(),'period_end'=>today()->subMonth()->endOfMonth(),'current_charges'=>80,'total_payable'=>80,'balance'=>80,'status'=>InvoiceStatus::Unpaid,'due_date'=>today()->subDays(3)]);
  $this->artisan('mealflow:reminders')->assertExitCode(0);
  $log=\App\Models\NotificationLog::withoutGlobalScopes()->where('type','invoice_overdue')->firstOrFail();
  $this->assertStringContainsString('انوائس',$log->message);
  $this->assertSame($defaultLocale,app()->getLocale());
  $payment=\App\Models\Payment::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'customer_id'=>$x['customer']->id,'invoice_id'=>$invoice->id,'employee_id'=>$x['admin']->id,'amount'=>80,'mode'=>PaymentMode::Cash,'paid_at'=>now(),'idempotency_key'=>'loc-1']);
  $receipt=\App\Models\Receipt::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'payment_id'=>$payment->id,'receipt_number'=>'PANEL-R1']);
  Sanctum::actingAs($x['admin']);
  $url=$this->postJson("/api/v1/receipts/{$receipt->uuid}/share")->assertOk()->json('data.url');
  $this->assertStringContainsString(rawurlencode('شکریہ'),$url);
 }
}
