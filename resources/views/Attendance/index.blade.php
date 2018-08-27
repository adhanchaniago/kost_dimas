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
                {{Form::select('room_location',[1=>'Maysa Kertamukti 1',2=>'Maysa Kertamukti 2', 3=>'Maysa Cirendeu', 4=>'Pesona Gunung Indah', 5=>'Maysa Kalibata'],'',['class'=>'form-control'])}}
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
        
    </div>
@endsection