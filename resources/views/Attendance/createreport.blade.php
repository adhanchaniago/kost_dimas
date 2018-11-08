{{-- ATTENDANCE RECORD FORM --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Form Laporan Bulanan</h1>
    <small>Please pick the kost location and date</small>
    <hr>
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        
        {!! Form::open(['action' => 'AttendanceController@generateReport', 'method' => 'POST']) !!}
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