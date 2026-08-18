<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller; use App\Models\{AuditLog,CashHandover,Customer,DailyClosing,Delivery,Device,Invoice,NotificationLog,Payment,PaymentCorrection,User}; use Illuminate\Http\Request;
class OperationsController extends Controller {
 private function page(string $title,array $columns,$rows){return view('admin.list',compact('title','columns','rows'));}
 public function employees(){return $this->page('Employees',['name','email','phone','role','is_active'],User::where('company_id',auth()->user()->company_id)->latest()->paginate(50));}
 public function deliveries(){return $this->page('Daily deliveries',['delivery_date','customer.name','meal_option','quantity','status','recorder.name','confirmed_at'],Delivery::with(['customer','recorder'])->latest('delivery_date')->paginate(50));}
 public function invoices(){return $this->page('Invoices',['invoice_number','customer.name','period_start','period_end','total_payable','amount_paid','balance','status'],Invoice::with('customer')->latest()->paginate(50));}
 public function payments(){return $this->page('Payments',['receipt.receipt_number','customer.name','amount','mode','paid_at','employee.name','reference'],Payment::with(['receipt','customer','employee'])->latest('paid_at')->paginate(50));}
 public function balances(){return $this->page('Outstanding balances',['customer_code','name','building.name','room.number','outstanding_balance'],Customer::with(['building','room'])->withSum('invoices as outstanding_balance','balance')->orderByDesc('outstanding_balance')->paginate(50));}
 public function handovers(){return $this->page('Cash handovers',['employee.name','amount_submitted','verified_amount','variance','status','submitted_at','verified_at'],CashHandover::with('employee')->latest()->paginate(50));}
 public function corrections(){return $this->page('Payment corrections',['payment.receipt.receipt_number','requester.name','corrected_amount','corrected_mode','reason','status','reviewed_at'],PaymentCorrection::with(['payment.receipt','requester'])->latest()->paginate(50));}
 public function closings(){return $this->page('Daily closings',['closing_date','employee.name','cash_total','card_total','receipt_count','cash_submitted','pending_cash','variance'],DailyClosing::with('employee')->latest('closing_date')->paginate(50));}
 public function audit(){return $this->page('Audit and correction log',['created_at','user_id','action','auditable_type','auditable_id','ip_address'],AuditLog::latest()->paginate(100));}
 public function devices(){return $this->page('Tablet devices',['name','device_key','platform','approved_at','disabled_at','last_seen_at'],Device::latest()->paginate(50));}
 public function notifications(){return $this->page('WhatsApp and reminders',['created_at','type','channel','recipient','subject','status','sent_at'],NotificationLog::latest()->paginate(50));}
 public function reports(){return view('reports.index');} public function settings(){return view('admin.settings',['company'=>auth()->user()->company]);} public function updateSettings(Request $r){$d=$r->validate(['currency'=>'required|string|size:3','timezone'=>'required|string|max:60','delivery_cutoff_time'=>'required|date_format:H:i','invoice_due_days'=>'required|integer|min:0|max:365','receipt_prefix'=>'nullable|max:20']);auth()->user()->company->update($d);return back()->with('status','Settings saved.');}
}
