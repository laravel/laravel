<?php
namespace App\Services;
use App\Enums\InvoiceStatus; use App\Models\{Company,Customer,Invoice,LedgerEntry,Payment,Receipt}; use Illuminate\Support\Arr; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class PaymentService {
 public function create(array $data, int $employeeId): Payment { return DB::transaction(function() use($data,$employeeId){
  if($existing=Payment::where('idempotency_key',$data['idempotency_key'])->first()) return $existing;
  $invoice=isset($data['invoice_id']) ? Invoice::where('uuid',$data['invoice_id'])->lockForUpdate()->firstOrFail() : null;
  if($invoice && isset($data['record_version']) && $invoice->version !== (int)$data['record_version']) throw ValidationException::withMessages(['record_version'=>'Invoice changed; supervisor review required.']);
  $customerId=$invoice?->customer_id ?? Customer::whereUuid($data['customer_id'])->firstOrFail()->id;
  $payment=Payment::create([...Arr::except($data,['invoice_id','customer_id','record_version']),'invoice_id'=>$invoice?->id,'customer_id'=>$customerId,'employee_id'=>$employeeId,'paid_at'=>$data['paid_at'] ?? now()]);
  if($invoice){ $invoice->amount_paid=round((float)$invoice->amount_paid+(float)$payment->amount,2); $invoice->balance=round((float)$invoice->total_payable-(float)$invoice->amount_paid,2); $invoice->status=$invoice->balance<0?InvoiceStatus::AdvancePaid:($invoice->balance==0?InvoiceStatus::FullyPaid:InvoiceStatus::PartiallyPaid); $invoice->version++; $invoice->save(); }
  $last=LedgerEntry::where('customer_id',$payment->customer_id)->lockForUpdate()->latest('id')->value('running_balance') ?? 0;
  LedgerEntry::create(['company_id'=>$payment->company_id,'customer_id'=>$payment->customer_id,'type'=>'payment','credit'=>$payment->amount,'running_balance'=>round((float)$last-(float)$payment->amount,2),'source_type'=>$payment->getMorphClass(),'source_id'=>$payment->id,'description'=>'Payment received']);
  $company=Company::lockForUpdate()->findOrFail($payment->company_id); $number=$company->code.'-R-'.str_pad((string)$company->next_receipt_number,7,'0',STR_PAD_LEFT); $company->increment('next_receipt_number');
  Receipt::create(['company_id'=>$payment->company_id,'payment_id'=>$payment->id,'receipt_number'=>$number]); AuditService::record('payment.created',$payment,[],['amount'=>$payment->amount,'mode'=>$payment->mode->value]); return $payment->load('receipt');
 }); }
}
