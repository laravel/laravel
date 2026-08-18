<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\{Company,Invoice,MealPause,NotificationLog};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')->everyMinute();

Artisan::command('mealflow:reminders', function () {
    Company::where('is_active',true)->each(function($company){
        Invoice::withoutGlobalScopes()->where('company_id',$company->id)->whereIn('status',['unpaid','partially_paid'])->whereDate('due_date','<',today())->with('customer')->chunkById(100,function($invoices)use($company){foreach($invoices as $invoice){$subject="Overdue invoice {$invoice->invoice_number}";NotificationLog::withoutGlobalScopes()->firstOrCreate(['company_id'=>$company->id,'type'=>'invoice_overdue','subject'=>$subject,'created_at'=>now()->startOfDay()],['customer_id'=>$invoice->customer_id,'channel'=>'in_app','recipient'=>$invoice->customer?->phone,'message'=>"AED {$invoice->balance} remains due.",'status'=>'pending','metadata'=>['invoice_uuid'=>$invoice->uuid]]);}});
        MealPause::withoutGlobalScopes()->where('company_id',$company->id)->whereDate('resumes_on',today())->with('customer')->each(function($pause)use($company){$subject="Meal service resumes for {$pause->customer->name}";NotificationLog::withoutGlobalScopes()->firstOrCreate(['company_id'=>$company->id,'type'=>'pause_resume','subject'=>$subject,'created_at'=>now()->startOfDay()],['customer_id'=>$pause->customer_id,'channel'=>'in_app','recipient'=>$pause->customer->phone,'message'=>'Meal service is scheduled to resume today.','status'=>'pending']);});
    });
    $this->info('Reminder records generated.');
})->purpose('Generate overdue invoice and meal-resume reminders');
Schedule::command('mealflow:reminders')->dailyAt('06:00')->withoutOverlapping();
Schedule::command('mealflow:backup')->dailyAt('02:00')->withoutOverlapping();
