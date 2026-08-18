<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\{Company,Customer,Invoice,MealPause,NotificationLog};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')->everyMinute();

Artisan::command('mealflow:reminders', function () {
    $defaultLocale=app()->getLocale();
    Company::where('is_active',true)->each(function($company)use($defaultLocale){
        app()->setLocale(in_array($company->locale,['en','ur','ar'])?$company->locale:$defaultLocale);
        Invoice::withoutGlobalScopes()->where('company_id',$company->id)->whereIn('status',['unpaid','partially_paid'])->whereDate('due_date','<',today())->with('customer')->chunkById(100,function($invoices)use($company){foreach($invoices as $invoice){$subject="Overdue invoice {$invoice->invoice_number}";NotificationLog::withoutGlobalScopes()->firstOrCreate(['company_id'=>$company->id,'type'=>'invoice_overdue','subject'=>$subject,'created_at'=>now()->startOfDay()],['customer_id'=>$invoice->customer_id,'channel'=>'in_app','recipient'=>$invoice->customer?->phone,'message'=>__('messages.invoice_overdue',['invoice'=>$invoice->invoice_number,'currency'=>$company->currency,'balance'=>number_format((float)$invoice->balance,2)]),'status'=>'pending','metadata'=>['invoice_uuid'=>$invoice->uuid]]);}});
        MealPause::withoutGlobalScopes()->where('company_id',$company->id)->whereDate('resumes_on',today())->with('customer')->each(function($pause)use($company){$subject="Meal service resumes for {$pause->customer->name}";NotificationLog::withoutGlobalScopes()->firstOrCreate(['company_id'=>$company->id,'type'=>'pause_resume','subject'=>$subject,'created_at'=>now()->startOfDay()],['customer_id'=>$pause->customer_id,'channel'=>'in_app','recipient'=>$pause->customer->phone,'message'=>__('messages.meal_resume'),'status'=>'pending']);});
        if($company->low_balance_threshold){Customer::withoutGlobalScopes()->where('company_id',$company->id)->where('is_active',true)->withSum('invoices as outstanding','balance')->get()->filter(fn($c)=>(float)($c->outstanding??0)>=(float)$company->low_balance_threshold)->each(function($customer)use($company){$subject="Low balance: {$customer->name}";NotificationLog::withoutGlobalScopes()->firstOrCreate(['company_id'=>$company->id,'type'=>'low_balance','subject'=>$subject,'created_at'=>now()->startOfDay()],['customer_id'=>$customer->id,'channel'=>'in_app','recipient'=>$customer->phone,'message'=>__('messages.low_balance',['currency'=>$company->currency,'balance'=>number_format((float)$customer->outstanding,2)]),'status'=>'pending']);});}
    });
    app()->setLocale($defaultLocale);
    $this->info('Reminder records generated.');
})->purpose('Generate overdue invoice, low-balance and meal-resume reminders');
Schedule::command('mealflow:reminders')->dailyAt('06:00')->withoutOverlapping();
Schedule::command('mealflow:backup')->dailyAt('02:00')->withoutOverlapping();
