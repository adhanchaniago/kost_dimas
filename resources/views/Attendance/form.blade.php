{{-- ATTENDANCE FORM --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>{{$location->name}} Attendance Form</h1>
    <hr>
    {!! Form::open(['action' => 'AttendanceController@attend','method' => 'POST']) !!}
        <table class="table table-striped">
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Room Number</th>
                <th>ID</th>
                <th>Attendance</th>
            </tr>
            @if(count($guests) < 1)
                <tr>
                    <td colspan="5">
                        <a href="/guests/get_room">There are no guests in this location, Click here to Add guests</a>
                    </td>
                </tr>
            @else
                <?php $counter = 1;?>
                @foreach($guests as $guest)
                <tr>
                    <td>{{$counter}}</td>
                    <td>{{$guest->name}}</td>
                    <td>{{$guest->room_number}}</td>
                    <td><img src="../../../<?php echo $guest->id_path; ?>" style="height:200px;width:300px;"></td>
                    <td>{{Form::select('atd_status[]',[1=>'Present',2=>'Absent'],'',['class'=>'form-control'])}}</td>
                    {{Form::hidden('guest_array[]',$guest->id)}}
                </tr>
                <?php $counter++;?>
                @endforeach
            @endif
        </table>
        {{Form::hidden('location',$location->id)}}
        {{Form::hidden('_method','POST')}}
        @if(count($guests) > 1)
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        @endif
    {!! Form::close() !!}
@endsection