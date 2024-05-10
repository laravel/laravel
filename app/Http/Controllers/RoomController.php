<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexV2()
    {
        $data=Room::get();
        return view('room.index',['data'=>$data]);
        //return response()->json($data);
    }

    public function index()
    {
        $data=Room::all();
       return view('room.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roomtypes=RoomType::all();
        return view('room.create',['roomtypes'=>$roomtypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        $data=new Room;
        $data->room_type_id=$request->rt_id;
        $data->title=$request->title;
        $data->description=$request->description;
           
        $data->save();

        return redirect('admin/rooms')->with('success','Data has been added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Room::find($id);
        $data->save();
        return view('room.show',['data'=>$data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $roomtypes=RoomType::all();
        $data=Room::find($id);
        //dd($data);
        return view('room.edit',['data'=>$data,'roomtypes'=>$roomtypes]);
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
        $data=Room::find($id);
        $data->room_type_id=$request->rt_id;
        $data->title=$request->title;
        $data->save();

        return redirect('admin/rooms/'.$id.'/edit')->with('success','Data has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       Room::where('id',$id)->delete();
       return redirect('admin/rooms')->with('success','Data has been deleted.');
    }
}
