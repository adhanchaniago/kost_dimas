<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\Location;
use App\RoomDetail;

class GuestsController extends Controller
{
    public function __construct()
     {
         $this->middleware('auth');
     }
    
    public function index(){
        $guests = Guest::where('deleted_at',NULL)->where('exit_date',NULL)->orderBy('id','asc')->paginate(30);
        $locations = Location::where('deleted_at',NULL)->get();
        return view('Guests.index')->with('guests',$guests)->with('locations',$locations);
    }

    public function create(Request $request){
        $location = Location::find($request->input('room_location'));
        $room_details = RoomDetail::where('room_location',$location->id)->get();
        return view('Guests.create')->with('location',$location)->with('room_details',$room_details);
    }

    public function get_room(){
        $locations = Location::where('deleted_at',NULL)->get();
        return view('Guests.precreate')->with('locations',$locations);
    }

    public function store(Request $request){
        $this->validate($request,[
          'name' => 'required',
          'entry_date' => 'required',
          'room_number' => 'required',
          'room_location' => 'required'
        ]);
        //availability check
        // $room_location = $request->input('room_location');
        // $room_number = $request->input('room_number');

        // if (Guest::where('room_location',$room_location)->where('room_number',$room_number)->exists()){
        //     return redirect('Guests.create')->with('error','Room is not Vacant');
        // }

        $file = $request->file('id_path');
   
        //Display File Name
        // echo 'File Name: '.$file->getClientOriginalName();
        // echo '<br>';
    
        // //Display File Extension
        // echo 'File Extension: '.$file->getClientOriginalExtension();
        // echo '<br>';
    
        // //Display File Real Path
        // echo 'File Real Path: '.$file->getRealPath();
        // echo '<br>';
    
        // //Display File Size
        // echo 'File Size: '.$file->getSize();
        // echo '<br>';
    
        // //Display File Mime Type
        // echo 'File Mime Type: '.$file->getMimeType();
    
        //Move Uploaded File
        $destinationPath = 'uploads';
        $path = $destinationPath.'/'.$file->getClientOriginalName();

        $file->move($destinationPath,$file->getClientOriginalName());
        
        $guest = new Guest;
        $guest->name = $request->input('name');
        $guest->entry_date = $request->input('entry_date');
        $guest->id_path = $path;
        $guest->room_location = $request->input('room_location');
        $guest->room_number = $request->input('room_number');
        $guest->description = $request->input('description');
        $guest->nationality = $request->input('nationality');
        $guest->room_type = $request->input('room_type');
        $guest->save();

        return redirect('/guests')->with('success','Guest Successfully Added');
    }

    public function show($id){
        $guest = Guest::find($id);

        if($guest!=NULL){
            $deldate = $guest->deleted_at;
            
            if($deldate != NULL){
                return redirect('/guests')->with('error','The data has been deleted');
            }
            else{
                return view('guests.show')->with('guest',$guest);
            }

        }
        else{
            return redirect('/guests')->with('error','The data does not exist');
        }
    }

    public function edit($id){
        $guest = Guest::find($id);
        $locations = Location::where('deleted_at',NULL)->get();
        $location = Location::where('id',$guest->room_location)->first();
        $room_details = RoomDetail::where('room_location',$location->id)->get();
        if($guest!=NULL){
            $deldate = $guest->deleted_at;
            
            if($deldate != NULL){
                return redirect('/guests')->with('error','The data has been deleted');
            }
            else{
                return view('guests.edit')->with('guest',$guest)->with('room_details',$room_details)->with('location',$location);
            }

        }
        else{
            return redirect('/guests')->with('error','The data does not exist');
        }
    }

    public function update(Request $request, $id){

        $this->validate($request,[
          'name' => 'required',
          'entry_date' => 'required',
          'room_number' => 'required',
        //   'room_location' => 'required'
        ]);

        $guest = Guest::find($id);
        $guest->name = $request->input('name');
        $guest->entry_date = $request->input('entry_date');
        // $guest->id_path = $request->input('id_path');
        $guest->description = $request->input('description');
        $guest->room_number = $request->input('room_number');
        $guest->exit_date = $request->input('exit_date');
        // $guest->room_location = $request->input('room_location');
        $guest->save();

        return redirect('/guests')->with('success','Guest Successfully Updated');
    }

    public function destroy($id){

        $date = date('Y-m-d H:i:s');
        $guest = Guest::find($id);
        $guest->deleted_at = $date;
        $guest->save();

        return redirect('/guests')->with('success','Guest Deleted');
    }

    public function search(Request $request){
        $guest_name = $request->input('guest_name');
        $guests = Guest::where('name','like','%'.$guest_name.'%')->paginate(30);
        return view('Guests.index')->with('guests',$guests);
    }
    

}
