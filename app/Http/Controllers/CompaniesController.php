<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Companies;

class CompaniesController extends Controller
{


    public function get_company_data_by_id(Request $request){


        $id= $request->input('id');
        $company = Companies::find($id);

        return response()->json(['data'=>$company,'status'=>1]);

    }


    public function delete_company_by_id(Request $request){

        $id = $request->input('id');
        $company = Companies::find($id);
        $company->delete();



        return response()->json(['delete_status'=>1]);

    }


    public function edit_company_details(Request $request){

        $company = Companies::find($request->input('id'));
        $company->name = $request->input('companyName');
        if(isset($request->productType) && !empty($request->productType))
        // if($request->input('productType') != 'Select Product Category')
        {
            $company->category_id = $request->input('category_id');
            $company->product_type = $request->input('productType');
        }
        $company->address = $request->input('address');
        $company->email = $request->input('email');
        $company->phone = $request->input('phone');
        $company->country = $request->input('country');
        $company->save();



        return response()->json(['status'=>1]);


    }


   
    public function add_company_form_datatable(Request $request){

        $companyName = $request->input('companyName');
        $productType = $request->input('productType');
        $category_id = $request->input('category_id');

        $address = $request->input('address');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $country = $request->input('country');

        $companies = new Companies();
        $companies->name = $companyName;
        $companies->product_type = $productType;
        $companies->category_id = $category_id;
        $companies->address = $address;
        $companies->email = $email;
        $companies->phone = $phone;
        $companies->country = $country;
        $companies->save();



 


        return response()->json(['status'=>1]);
    }
    
}



