{{--LOCATION CREATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <a href="/locations" class="btn btn-primary">Back to List of Locations</a>
    <h1>Add Location</h1>
    {!! Form::open(['action' => 'LocationsController@create', 'method' => 'POST', 'files'=>'true']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Name')}}
      {{Form::text('name','',['class'=>'form-control','placeholder'=>'Guest Name'])}}
    </div>
    <div class="form-group">
      {{Form::label('capacity', 'Capacity')}}
      {{Form::number('capacity','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('address', 'Description')}}
      {{Form::textarea('address','',['class'=>'form-control'])}}
    </div>
    {{Form::hidden('_method','POST')}}
    {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
  {!! Form::close() !!}
@endsection