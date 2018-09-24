{{-- ATTENDANCE RECORD FORM --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>LB Form</h1>
    <small>Please pick the kost location and date</small>
    <hr>
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        
        {!! Form::open(['action' => 'AttendanceController@generateReport', 'method' => 'POST']) !!}
            <div class="form-group">
                {{Form::label('room_location', 'Room Location')}}
                {{Form::select('room_location',[1=>'Maysa Kertamukti 1',2=>'Maysa Kertamukti 2', 3=>'Maysa Cirendeu', 4=>'Pesona Gunung Indah', 5=>'Maysa Kalibata'],'',['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('start_date', 'Start Period')}}
                {{Form::date('start_date','',['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('end_date', 'End Period')}}
                {{Form::date('end_date','',['class'=>'form-control'])}}
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
        
    </div>
@endsection