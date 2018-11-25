{{--GUEST CREATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Add Guest to {{$location->name}}</h1>
    {!! Form::open(['action' => 'GuestsController@store', 'method' => 'POST', 'files'=>'true']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Name')}}
      {{Form::text('name','',['class'=>'form-control','placeholder'=>'Guest Name'])}}
    </div>
    <div class="form-group">
      {{Form::label('entry_date', 'Entry Date')}}
      {{Form::date('entry_date','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('description', 'Description')}}
      {{Form::textarea('description','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('nationality', 'Nationality')}}
      {{Form::text('nationality','',['class'=>'form-control','placeholder'=>'Nationality'])}}
    </div>
    {{--  <div class="form-group">
      {{Form::label('room_location', 'Room Location')}}
      {{Form::select('room_location',[1=>'Maysa Kertamukti 1',2=>'Maysa Kertamukti 2', 3=>'Maysa Cirendeu', 4=>'Pesona Gunung Indah', 5=>'Maysa Kalibata'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::number('room_number','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('room_type', 'Room Type')}}
      <select class="form-control" name="room_type">
          @foreach($room_details as $room_detail)
            <option value="{{$room_detail->id}}">{{$room_detail->room_type}}</option>
          @endforeach
      </select>
    </div>
    <div class="form-group">
      {{Form::label('id_path', 'ID File')}}
      {{Form::File('id_path','',['class'=>'form-control','required'])}}
    </div>
    {{Form::hidden('room_location',$location->id)}}
    {{Form::hidden('_method','POST')}}
    {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
  {!! Form::close() !!}
@endsection