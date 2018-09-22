<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\Guest;

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

        $location = new Location;
        $location->name = $request->input('name');
        $location->capacity = $request->input('capacity');
        $location->address = $request->input('address');
        $location->save();

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

        if($location!=NULL){
            $deldate = $location->deleted_at;
            
            if($deldate != NULL){
                return redirect('/locations')->with('error','The data has been deleted');
            }
            else{
                return view('locations.edit')->with('location',$location);
            }

        }
        else{
            return redirect('/location')->with('error','The data does not exist');
        }
    }

    public function update(Request $request, $id){
        $this->validate($request,[
          'name' => 'required',
          'capacity' => 'required'
        ]);

        $location = Location::find($id);
        $location->name = $request->input('name');
        $location->capacity = $request->input('capacity');
        $location->address = $request->input('address');
        $location->type = $request->input('room_type');
        $location->type = $request->input('room_rate');
        $location->save();

        return redirect('/locations')->with('success','location updated');
    }

    public function destroy($id){
        $date = date('Y-m-d H:i:s');
        $location = Guest::find($id);
        $location->deleted_at = $date;
        $location->save();

        return redirect('/locations')->with('success','Location Deleted');
    }

    
}
