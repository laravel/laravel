<?php
namespace App\Http\Controllers;
use App\Models\customers;
use Illuminate\Http\Request;
class CustomersController extends Controller
{
    function customers_add(Request $request){
        $insert = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email'=> $request->email,
            'password'=>$request->password,
            'phone'=>$request->phone,
            
        ];
        $add = customers::create($insert);
        if($add){
            $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Record created succesfully!'
            ];
            return $response;
        }else{
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Record created failed!'
            ];
            return $response;
        }
    } 

    function customers_view(Request $request){
        return customers::find($request->id);
    } 

    function customers_delete(Request $request){
        $delete =  customers::destroy($request->id);
        if($delete){
            $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Record deleted succesfully!'
            ];
            return $response;
        }else{
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Record deleted failed!'
            ];
            return $response;
        }
    } 

    function customers_edit(Request $request){
        $update = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email'=> $request->email,
            'password'=>$request->password,
            'phone'=>$request->phone,
        ];
        $edit = customers::where('id', $request->customer_id)->update($update);
        if($edit){
            $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Record updated succesfully!'
            ];
            return $response;
        }else{
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Record updated failed!'
            ];
            return $response;
        }
    } 

    function customers_list(){
        return customers::all();
    }
} 