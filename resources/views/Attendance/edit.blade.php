{{-- EDIT ATTENDANCE FORM --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>List of Guests in {{$location->name}}</h1>
    {!! Form::open(['action' => 'AttendanceController@attend','method' => 'POST']) !!}
        <table class="table table-striped">
            <tr>
                <th>Name</th>
                <th>Room Number</th>
                <th>ID</th>
                <th>Attendance</th>
            </tr>
            @foreach($guests as $guest)
            <tr>
                <td>{{$guest->name}}</td>
                <td>{{$guest->room_number}}</td>
                <td><img src="../../../<?php echo $guest->id_path; ?>" style="height:200px;width:300px;"></td>
                <td><?php
                        $x = 1;
                        $size = sizeof($attendances);
                        foreach($attendances as $attendance){
                            if($guest->id == $attendance->guest_id){
                                echo "Present";
                                break 1;
                            }
                            elseif($x == $size && $guest->id != $attendance->guest_id){
                                ?>
                                {{Form::select('atd_status[]',[1=>'Present',2=>'Absent'],'',['class'=>'form-control'])}}
                                {{Form::hidden('guest_array[]',$guest->id)}}
                                {{Form::hidden('location',$location->id)}}
                                <?php          
                            }
                            $x++;
                        }
                                ?>
                </td>
            </tr>
            @endforeach
        </table>
        {{Form::hidden('_method','POST')}}
        {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
    {!! Form::close() !!}
@endsection