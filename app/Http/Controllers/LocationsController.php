<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\Guest;
use App\RoomDetail;

class LocationsController extends Controller
{
    public function __construct()
     {
         $this->middleware('auth');
     }
    
    public function index(){
        $locations = Location::where('deleted_at',NULL)->get();
        return view('locations.index')->with('locations',$locations);
    }

    public function create(){
        return view('locations.create');
    }

    public function store(Request $request){
        $this->validate($request,[
          'name' => 'required',
          'capacity' => 'required'
        ]);

        $type_array = array();
        $type_array_quantity = array();
        $type_array_rate = array();
        $type_array_rate_month = array();

        $type_array = $request->input('room_type');
        $type_array_quantity = $request->input('room_type_quantity');
        $type_array_rate = $request->input('room_type_rate');
        $type_array_rate_month = $request->input('room_type_rate_month');

        $location = new Location;
        $location->name = $request->input('name');
        $location->capacity = $request->input('capacity');
        $location->address = $request->input('address');
        $location->save();

        $locationID = Location::orderBy('created_at','desc')->first();

        for($x=0;$x<sizeof($type_array);$x++){
            $room_detail = new RoomDetail;
            $room_detail->room_type = $type_array[$x];
            $room_detail->quantity = $type_array_quantity[$x];
            $room_detail->daily_rate = $type_array_rate[$x];
            $room_detail->monthly_rate = $type_array_rate_month[$x];
            $room_detail->room_location = $locationID->id;
            $room_detail->save();
        }

        return redirect('/locations')->with('success','location added');
    }

    public function show($id){
        $location = Location::find($id);

        if($location!=NULL){
            $deldate = $location->deleted_at;
            
            if($deldate != NULL){
                return redirect('/locations')->with('error','The data has been deleted');
            }
            else{
                $guests = Guest::where('room_location',$id)->where('exit',NULL)->where('deleted_at',NULL)->orderBy('room_number','asc')->paginate(10);
                return view('locations.show')->with('location',$location)->with('guests',$guests);
            }

        }
        else{
            return redirect('/location')->with('error','The data does not exist');
        }
    }

    public function edit($id){
        $location = Location::find($id);
        $room_details = RoomDetail::where('room_location',$id)->get();
        if($location!=NULL){
            $deldate = $location->deleted_at;
            
            if($deldate != NULL){
                return redirect('/locations')->with('error','The data has been deleted');
            }
            else{
                return view('locations.edit')->with('location',$location)->with('room_details',$room_details);
            }

        }
        else{
            return redirect('/location')->with('error','The data does not exist');
        }
    }

    public function update(Request $request, $id){
        $this->validate($request,[
          'name' => 'required',
          'total_capacity' => 'required'
        ]);
        
        $type_array = array();
        $type_array_quantity = array();
        $type_array_rate = array();
        $type_array_rate_month = array();

        $type_array = $request->input('room_type');
        $type_array_quantity = $request->input('room_type_quantity');
        $type_array_rate = $request->input('room_type_rate');
        $type_array_rate_month = $request->input('room_type_rate_month');
        
        $location = Location::find($id);
        $location->name = $request->input('name');
        $location->capacity = $request->input('total_capacity');
        $location->address = $request->input('address');
        $location->save();

        $room_details = RoomDetail::where('room_location',$id)->get();
        
        $counter = 0;
        foreach($room_details as $room_detail){
            if(array_key_exists($counter,$type_array)){
                $room_detail->room_type = $type_array[$counter];
                $room_detail->quantity = $type_array_quantity[$counter];
                $room_detail->monthly_rate = $type_array_rate_month[$counter];
                $room_detail->daily_rate = $type_array_rate[$counter];
                $room_detail->save();
            }
            else{
                $room = RoomDetail::where('room_type',$type_array[$counter])->first();
            }
            $counter++;
        }

        for($counter;$counter<sizeof($type_array);$counter++){
            $room_detail = new RoomDetail;
            $room_detail->room_type = $type_array[$counter];
            $room_detail->quantity = $type_array_quantity[$counter];
            $room_detail->monthly_rate = $type_array_month[$counter];
            $room_detail->daily_rate = $type_array_rate[$counter];
            $room_detail->room_location = $id;
            $room_detail->save();
        }
        

        return redirect('/locations')->with('success','location updated');
    }

    public function destroy($id){
        $date = date('Y-m-d H:i:s');
        $location = Location::find($id);
        $location->deleted_at = $date;
        $location->save();

        return redirect('/locations')->with('success','Location Deleted');
    }

    public function search(Request $request){
        $location_id = $request->input('location_id');
        $location = Location::find($location_id);
        $guest_name = $request->input('guest_name');
        $guests = Guest::where('name','like','%'.$guest_name.'%')->where('room_location',$location_id)->paginate(30);
        $error_message = "Guest Not Found";
        return view('Locations.show')->with('guests',$guests)->with('location',$location)->with('error_message',$error_message);
    }

    
}
