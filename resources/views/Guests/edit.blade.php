{{--GUEST UPDATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Edit Guest Data</h1>
    {!! Form::open(['action' => ['GuestsController@update',$guest->id], 'method' => 'POST']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Name')}}
      {{Form::text('name',$guest->name,['class'=>'form-control','placeholder'=>'Guest Name'])}}
    </div>
    <div class="form-group">
      {{Form::label('entry_date', 'Entry Date')}}
      {{Form::date('entry_date',$guest->entry_date,['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('exit_date', 'Exit Date')}}
      {{Form::date('exit_date',$guest->exit_date,['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('description', 'Description')}}
      {{Form::textarea('description',$guest->description,['class'=>'form-control'])}}
    </div>
    {{--  <div class="form-group">
      {{Form::label('room_location', 'Room Location')}}
      
    </div>  --}}
    <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::number('room_number',$guest->room_number,['class'=>'form-control'])}}
    </div>
    {{Form::hidden('_method','POST')}}
    {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
  {!! Form::close() !!}
@endsection