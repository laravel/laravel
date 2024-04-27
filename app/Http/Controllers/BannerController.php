<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Banner::all();
        return view('banner.index', ['data' => $data]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('banner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'banner_src' => 'required|image',
            'alt_text' => 'required',
        ]);

        if ($request->hasFile('banner_src')) {
            $imgPath = $request->file('banner_src')->store('/app/public/img');
        } else {
            $imgPath = null;
        }
        $data = new Banner;
        $data->banner_src = $imgPath;
        $data->alt_text = $request->alt_text;
        $data->publish_status = $request->publish_status;
        $data->save();
        return redirect('admin/banner')->with('success', 'Data has been added.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Banner::find($id);
        return view('banner.show', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Banner::find($id);
        return view('banner.edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'prev_photo' => 'required',
            'alt_text' => 'required',
        ]);

        if ($request->hasFile('banner_src')) {
            $imgPath = $request->file('banner_src')->store('public/imgs');
        } else {
            $imgPath = $request->prev_photo;
        }

        $data = Banner::find($id);
        $data->banner_src = $imgPath;
        $data->alt_text = $request->alt_text;
        $data->publish_status = $request->publish_status;
        $data->save();



        return redirect('admin/banner/')->with('success', 'Data has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Banner::where('id', $id)->delete();
        return redirect('admin/banner')->with('success', 'Data has been deleted.');
    }
}
