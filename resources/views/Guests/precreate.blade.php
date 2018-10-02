{{--GUEST CREATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Location for New Guest</h1>
    {!! Form::open(['action' => 'GuestsController@create', 'method' => 'POST', 'files'=>'true']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Location')}}
      <select class="form-control" name="room_location">
          @foreach($locations as $location)
            <option value="{{$location->id}}">{{$location->name}}</option>
          @endforeach
      </select>
    </div>
    {{Form::hidden('_method','POST')}}
    {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
  {!! Form::close() !!}
@endsection