{{-- ATTENDANCE RECORD --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Attendance on  {{$atd_date}}</h1> 
        <table class="table table-striped">
            <tr>
                <th>Name</th>
                <th>Room Number</th>
                <th>ID</th>
                <th>Entry Date</th>
                <th>Stay Duration</th>
                <th>Exit Date</th>
            </tr>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{$attendance->guest->name}}</td>
                <td>{{$attendance->guest->room_number}}</td>
                <td><img src="../../../<?php echo $attendance->guest->id_path; ?>" style="height:200px;width:300px;"></td>
                <td>{{$attendance->guest->entry_date}}</td>
                <td>
                    <?php
                        //logic 1
                        if($attendance->guest->exit_date == NULL){
                            $date2 = date_create($atd_date);
                            $date1 = date_create($attendance->guest->entry_date); 
                            $days_left = $date2->diff($date1);
                            $int = $days_left->days;
                            echo $int." days";    
                        }
                        else{
                            $date2 = date_create($attendance->guest->exit_date);
                            $date1 = date_create($attendance->guest->entry_date); 
                            $days_left = $date2->diff($date1);
                            $int = $days_left->days;
                            echo $int." days";
                        }
                        

                    ?>
                </td>
                <td>{{$attendance->guest->exit_date}}</td>
            </tr>
            @endforeach
        </table>
@endsection