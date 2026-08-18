<?php
namespace Tests\Feature;
use App\Enums\{InvoiceStatus,PaymentMode,Role}; use App\Models\{Building,Company,Customer,Invoice,MealPlan,Room,User}; use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Http\UploadedFile; use Illuminate\Support\Facades\File; use Illuminate\Support\Str; use Laravel\Sanctum\Sanctum; use Tests\TestCase;
class BackendCompletionTest extends TestCase { use RefreshDatabase;
 private function setupTenant():array{$company=Company::create(['name'=>'Complete Co','code'=>'CMPL']);$admin=User::factory()->create(['company_id'=>$company->id,'role'=>Role::CompanyAdmin,'password'=>'Testing!12345']);$building=Building::withoutGlobalScopes()->create(['company_id'=>$company->id,'name'=>'Tower A','code'=>'TWA']);$room=Room::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'number'=>'201','capacity'=>4]);$customer=Customer::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'room_id'=>$room->id,'customer_code'=>'W1','name'=>'Worker One','passport_number'=>'PZ1','start_date'=>today()]);return compact('company','admin','building','room','customer');}

 public function test_login_binds_token_to_device_and_disabling_the_device_blocks_further_requests():void{
  $x=$this->setupTenant();
  $login=$this->postJson('/api/v1/auth/login',['email'=>$x['admin']->email,'password'=>'Testing!12345','device_name'=>'Front Desk Tablet','device_key'=>'TAB-001','platform'=>'android'])->assertOk();
  $token=$login->json('data.token');$deviceUuid=$login->json('data.device.uuid');
  $this->assertDatabaseHas('devices',['device_key'=>'TAB-001','company_id'=>$x['company']->id]);
  $this->withHeader('Authorization',"Bearer $token")->getJson('/api/v1/me')->assertOk();
  $this->withHeader('Authorization',"Bearer $token")->patchJson("/api/v1/devices/$deviceUuid",['action'=>'disable'])->assertOk();
  $this->withHeader('Authorization',"Bearer $token")->getJson('/api/v1/me')->assertForbidden();
 }

 public function test_customer_can_be_looked_up_by_qr_token():void{
  $x=$this->setupTenant();Sanctum::actingAs($x['admin']);
  $this->assertNotEmpty($x['customer']->qr_token);
  $this->getJson('/api/v1/customers/qr/'.$x['customer']->qr_token)->assertOk()->assertJsonPath('data.name','Worker One');
 }

 public function test_building_summary_aggregates_workers_meals_and_balances():void{
  $x=$this->setupTenant();Sanctum::actingAs($x['admin']);
  $this->postJson('/api/v1/deliveries',['uuid'=>(string)Str::uuid(),'customer_id'=>$x['customer']->uuid,'delivery_date'=>today()->format('Y-m-d'),'meal_option'=>'morning','status'=>'delivered','idempotency_key'=>'bs-1'])->assertCreated();
  $this->getJson('/api/v1/buildings/'.$x['building']->uuid.'/summary')->assertOk()->assertJsonPath('data.total_workers',1)->assertJsonPath('data.meals_today',1);
 }

 public function test_bulk_pause_and_bulk_plan_apply_to_selected_customers():void{
  $x=$this->setupTenant();
  $second=Customer::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'building_id'=>$x['building']->id,'room_id'=>$x['room']->id,'customer_code'=>'W2','name'=>'Worker Two','passport_number'=>'PZ2','start_date'=>today()]);
  $plan=MealPlan::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'name'=>'Evening Only','meal_option'=>'evening','billing_type'=>'fixed_monthly','monthly_rate'=>200]);
  Sanctum::actingAs($x['admin']);
  $this->postJson('/api/v1/customers/bulk-pause',['customer_ids'=>[$x['customer']->uuid,$second->uuid],'starts_on'=>today()->format('Y-m-d'),'reason'=>'Site closure'])->assertOk()->assertJsonPath('data.processed',2);
  $this->assertDatabaseCount('meal_pauses',2);
  $this->postJson('/api/v1/customers/bulk-plan',['customer_ids'=>[$x['customer']->uuid,$second->uuid],'meal_plan_id'=>$plan->uuid,'starts_on'=>today()->format('Y-m-d')])->assertOk()->assertJsonPath('data.processed',2);
  $this->assertDatabaseCount('customer_plans',2);
 }

 public function test_receipt_history_and_admin_announcement():void{
  $x=$this->setupTenant();
  $invoice=Invoice::withoutGlobalScopes()->create(['company_id'=>$x['company']->id,'customer_id'=>$x['customer']->id,'invoice_number'=>'CMPL-I1','period_start'=>today()->startOfMonth(),'period_end'=>today()->endOfMonth(),'current_charges'=>50,'total_payable'=>50,'balance'=>50,'status'=>InvoiceStatus::Unpaid]);
  Sanctum::actingAs($x['admin']);
  $this->postJson('/api/v1/payments',['invoice_id'=>$invoice->uuid,'amount'=>'50.00','mode'=>PaymentMode::Cash->value,'idempotency_key'=>'hist-1'])->assertCreated();
  $this->getJson('/api/v1/customers/'.$x['customer']->uuid.'/receipts')->assertOk()->assertJsonCount(1,'data');
  $this->postJson('/api/v1/notifications/announce',['subject'=>'Holiday schedule','message'=>'No delivery on public holiday.'])->assertCreated();
  $other=User::factory()->create(['company_id'=>$x['company']->id,'role'=>Role::DeliveryEmployee]);
  Sanctum::actingAs($other);
  $this->getJson('/api/v1/notifications')->assertOk()->assertJsonFragment(['subject'=>'Holiday schedule']);
 }

 public function test_new_report_types_return_expected_headers():void{
  $x=$this->setupTenant();Sanctum::actingAs($x['admin']);
  $this->getJson('/api/v1/reports/building-outstanding')->assertOk()->assertJsonPath('data.head.0','Building');
  $this->getJson('/api/v1/reports/payment-status')->assertOk()->assertJsonPath('data.head.0','Customer');
  $this->getJson('/api/v1/reports/revenue-summary')->assertOk()->assertJsonPath('data.head.0','Date');
  $this->getJson('/api/v1/reports/audit-log')->assertOk()->assertJsonPath('data.head.0','Date');
  $this->getJson('/api/v1/reports/building-meals')->assertOk()->assertJsonPath('data.head.0','Building');
  $this->getJson('/api/v1/reports/customer-statement?customer='.$x['customer']->uuid)->assertOk()->assertJsonPath('data.head.0','Date');
 }

 public function test_customer_import_persists_start_date_not_activation_date():void{
  $x=$this->setupTenant();Sanctum::actingAs($x['admin']);
  $csv="building_code,room_number,customer_code,name,passport_number,phone,start_date\n{$x['building']->code},{$x['room']->number},C-IMPORT,Imported Worker,P-IMPORT,971500000099,2026-08-01\n";
  $file=UploadedFile::fake()->createWithContent('customers.csv',$csv);
  $this->postJson('/api/v1/imports',['type'=>'customers','file'=>$file])->assertCreated()->assertJsonPath('data.successful_rows',1)->assertJsonPath('data.failed_rows',0);
  $imported=Customer::withoutGlobalScopes()->where('customer_code','C-IMPORT')->firstOrFail();
  $this->assertSame('2026-08-01',$imported->start_date->toDateString());
 }

 public function test_backup_command_creates_a_dated_copy_of_the_database_file():void{
  $dbPath=storage_path('framework/testing/backup-source.sqlite');
  File::ensureDirectoryExists(dirname($dbPath));File::put($dbPath,'sqlite-fixture');
  config(['database.connections.sqlite.database'=>$dbPath]);
  $backupDir=storage_path('app/backups');File::deleteDirectory($backupDir);
  $this->artisan('mealflow:backup')->assertExitCode(0);
  $this->assertNotEmpty(glob($backupDir.'/backup-*.sqlite'));
  File::deleteDirectory($backupDir);File::delete($dbPath);
 }
}
