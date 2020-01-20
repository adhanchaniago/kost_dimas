{{-- ATTENDANCE INDEX --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Attendance System</h1>
    <div class="col-sm-6">
        <small>Please pick the kost location to start checking attendance</small>
    </div>
    <div class="col-sm-6" style="text-align:right;">
        <a class='btn btn-primary' href="/attendance/record"><small>Click here to Check Past Attendance Records</small></a>
    </div>
    <hr>
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        
        {!! Form::open(['action' => 'AttendanceController@atd_form', 'method' => 'POST']) !!}
            <div class="form-group">
                {{Form::label('room_location', 'Room Location')}}
                @if(count($locations) < 1)
                    <a href="/locations/create">There are no locations listed, Click here to Add Locations</a>
                @else
                    <select class="form-control" name="room_location">
                        @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->name}}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
        
    </div>
@endsection