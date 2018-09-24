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
          'start_date' => 'required'
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

    public function createreport(){
        return view('Attendance.createreport');
    }

    public function generateReport(Request $request){
        $room_location = $request->input('room_location');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $guests = Guest::where('deleted_at',NULL)->whereBetween('entry_date',[$start_date,$end_date])->get();
        $locations = Location::all();
        $location = Location::where('id',$room_location)->first();
        $date = date("j F Y");
        $month = date("F");
        $mpdf = new \Mpdf\Mpdf();

        //$content = "";
        foreach($locations as $location){
            $content = 
            "<div style='text-align:center;'>
            <center>
                <h1>MAYSA ANJAYA</h1>
                <p>Jl. Gunung Raya No 4 Cirendeu Ciputat Timur Tanggerang Selatan 15419. Banten . Telp. 021-7429342 </p>
            </center>
            <hr>
            <center>
                <h1>".$location->name."</h1>
            </center>
        </div>
            <div>
                <table border='1'>
                    <tr>
                        <th>Room No</th>
                        <th>Migrants Name</th>
                        <th>UNHCR Letter</th>
                        <th>Nationality</th>
                    </tr>";

            foreach($guests as $guest){
                if($guest->room_location == $location->id){
                    $content .=
                    "<tr>
                        <td>".$guest->room_number."</td>
                        <td>".$guest->name."</td>
                        <td>dummy</td>
                        <td>".$guest->nationality."</td>
                    </tr>";
                }
                
            }

            $content .= 
            "</table>
            </div>
            <div>
                <p>Tangerang Selatan ".$date."</p>
                <p>Pengelola</p>
                <br>
                <br>
                <br>
                Djoni Muhammad
            </div>
            ";

            $mpdf->WriteHTML($content);
            $mpdf->AddPage();
        }

        $description = 
            "<div style='text-align:center;'>
                <center>
                    <h1>MAYSA ANJAYA</h1>
                    <p>Jl. Gunung Raya No 4 Cirendeu Ciputat Timur Tanggerang Selatan 15419. Banten . Telp. 021-7429342 </p>
                </center>
                <hr>
            </div>
            <columns column-count='3'/>
            <p>Kepada Yth
            <strong>Kepala Rumah Detensi Imigrasi Jakarta</strong>
            <br>Up. Kasi Registrasi<br>
            Di - <strong>Tempat</strong></p>
            <p>Perihal: Laporan Pengungsi bulan ".$month."</p>
            <columnbreak />
            <columnbreak />
            <p>Tangerang, ".$date."
            No. MA / LB.09 / 9 / 2018</p>
            <columns column-count='0' vAlign='' column-gap='5' />
            <p>Bersama ini dengan hormat Kami laporkan keberadaan Pengungsi warga negara asing di akomodasi Maysa Anjaya  yang  Kami kelola  sampai dengan tanggal ".$date." sebagaimana data data terlampir:</p>
            <ol>
            ";
            
        foreach($locations as $location){
            $count = Guest::where('room_location',$location->id)->whereBetween('entry_date',[$start_date,$end_date])->count();
            $description .= 
            "<li>".$count." orang di ".$location->name." ".$location->address."</li>
            ";
        }

        $description .= 
        "</ol>
        <p>Demikian dilaporkan untuk menjadi maklum , terima kasih</p>
        <p>Hormat Kami, <br> Pengelola</p>
        <br>
        <br>
        <br>
        <p>Djoni Muhammad</p>
        ";

        $mpdf->WriteHTML($description);
        $filename = "LB_".$start_date.".pdf";
        $mpdf->Output($filename, 'D');
    }

    
}
