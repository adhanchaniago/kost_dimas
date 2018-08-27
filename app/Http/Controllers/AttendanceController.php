<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\Location;
use App\Attendance;

class AttendanceController extends Controller
{
    public function __construct()
     {
         $this->middleware('auth');
     }
    
    public function index(){
        return view('attendance.index');
    }

    public function atd_form(Request $request){
        $this->validate($request,[          
          'room_location' => 'required'
        ]);
        $date = date('Y-m-d');
        
        $room_location = $request->input('room_location');
        $attendances = Attendance::where('atd_date',$date)->where('room_location',$room_location)->get();
        $location = Location::find($room_location);
        $guests = Guest::where('room_location',$room_location)->where('exit_date',NULL)->where('deleted_at',NULL)->get();

        if($attendances->isEmpty()){
            return view('attendance.form')->with('guests',$guests)->with('location',$location);
        }
        else{
            return view('attendance.edit')->with('guests',$guests)->with('location',$location)->with('attendances',$attendances);
        }
        
        
    }

    public function attend(Request $request){
        $this->validate($request,[          
          'atd_status' => 'required'
        ]);
        //array and date initialization for attendance
        $date = date('Y-m-d');
        $status_array = array();
        $guest_array = array();
        
        //set array values from hidden input
        $status_array = $request->input('atd_status');
        $guest_array = $request->input('guest_array');
        
        $location = $request->input('location');
        
        for($counter=0;$counter<sizeof($status_array);$counter++){
            
            if($status_array[$counter] == 1){
                $attendance = new Attendance;
                $attendance->atd_date = $date;
                $attendance->room_location = $location;
                $attendance->guest_id = $guest_array[$counter];
                $attendance->save();
            }    
        }

        return redirect('attendance')->with('success','Attendance Submitted');

    }

    public function record(){
        
        return view('attendance.recordForm');
        
    }

    public function showRecord(Request $request){
        $this->validate($request,[          
          'room_location' => 'required',
          'atd_date' => 'required'
        ]);
        $date = ('Y-m-d');
        // $entry_date = Guest::where('deleted_at',NULL)->get();
        $room = $request->input('room_location');
        $location = Location::where('id',$request->input('room_location'))->first();
        $room_location = $location->name;
        $atd_date = $request->input('atd_date');
        $attendances = Attendance::where('room_location',$room)->where('atd_date',$atd_date)->get();
        // foreach($attendances as $attendance){
        //     echo $attendance->guest->name;
        // }
        return view('attendance.record')->with('attendances',$attendances)->with('room_location',$room_location)->with('atd_date',$atd_date);

    }

    
}
