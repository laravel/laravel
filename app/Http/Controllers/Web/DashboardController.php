<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller; use App\Models\{Building,CashHandover,Company,Customer,Delivery,Invoice,Payment};
class DashboardController extends Controller { public function __invoke(){
 if(auth()->user()->isPlatformAdmin()){$data=['companies'=>Company::count(),'active_companies'=>Company::where('is_active',true)->count()];return view('dashboard',compact('data'));}
 $data=['customers'=>Customer::where('is_active',true)->count(),'buildings'=>Building::count(),
  'morning_meals_today'=>Delivery::whereDate('delivery_date',today())->where('status','delivered')->where('meal_option','morning')->sum('quantity'),
  'evening_meals_today'=>Delivery::whereDate('delivery_date',today())->where('status','delivered')->where('meal_option','evening')->sum('quantity'),
  'monthly_billed'=>Invoice::whereBetween('period_start',[today()->startOfMonth(),today()->endOfMonth()])->sum('current_charges'),
  'received'=>Payment::whereNull('reverses_payment_id')->sum('amount'),'outstanding'=>Invoice::sum('balance'),
  'cash_today'=>Payment::where('mode','cash')->whereDate('paid_at',today())->sum('amount'),
  'card_today'=>Payment::where('mode','card')->whereDate('paid_at',today())->sum('amount'),
  'pending_handovers'=>CashHandover::whereNotIn('status',['verified','rejected'])->sum('amount_submitted')];
 $employeeCollection=Payment::whereNull('reverses_payment_id')->whereDate('paid_at',today())->with('employee:id,name')->selectRaw('employee_id,SUM(amount) total')->groupBy('employee_id')->get();
 $buildingOutstanding=Building::query()->select('buildings.id','buildings.name')->selectRaw('COALESCE(SUM(invoices.balance),0) as outstanding')->leftJoin('customers','customers.building_id','=','buildings.id')->leftJoin('invoices','invoices.customer_id','=','customers.id')->groupBy('buildings.id','buildings.name')->orderByDesc('outstanding')->get();
 return view('dashboard',compact('data','employeeCollection','buildingOutstanding'));
} }
