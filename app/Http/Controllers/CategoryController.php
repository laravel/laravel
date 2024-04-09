<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;


class CategoryController extends Controller
{

    public function showCatgeories()
    {

        $categories = Category::get()->all();
        // return response()->json(['categories' => $categories]);
        return view('categories', compact('categories'));
    }

    // admin crud methods

    public function Category_insert_form(Request $request)
    {

        $category = new Category();

        $category->name = $request->input('CategoryName');

        $image = $request->file('categoryimage');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('/storage/category_images/'), $filename);
        $category->image = $filename;

        $category->description = $request->input('description');

        $category->save();

        return response()->json(['category_status_insert' => 1]);
    }

    public function get_category_by_id(Request $request)
    {

        $id = $request->input('id');;

        $category = Category::find($id);

        return  response()->json(['data' => $category, 'status' => 1]);
    }


    public function edit_category_by_id(Request $request)
    {


        $id = $request->input('category_id_rec_form');

        $category = Category::find($id);
        $category->name = $request->input('CategoryName_edit');
        $category->description = $request->input('description_edit');

        if ($request->file('categoryimage_edit')) {


            $old_image_path = public_path('storage/category_images/') . $category->image;

            if (File::exists($old_image_path)) {
                File::delete($old_image_path);
            }

            $image = $request->file('categoryimage_edit');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('/storage/category_images/'), $filename);
            $category->image = $filename;
        }

        $category->save();

        return  response()->json(['status' => 1]);
    }


    public function category_delete_by_id(Request $request){

        $id = $request->input('id');

        $category = Category::find($id);

        $old_image_path = public_path('storage/category_images/') . $category->image;

        if (File::exists($old_image_path)) {
            File::delete($old_image_path);
        }

        $category->delete();
        

        return response()->json(['delete_category_status' =>1]);

    }

}
