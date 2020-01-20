{{--GUEST CREATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
  <h1>Location for New Guest</h1>
  <hr>
  @if(count($locations) < 1)
    <div class="row">
      <div class="col-sm-12">
        <center>
          <h2>There are no Locations yet</h2>
          <a href="/locations/create">Click Here to Add Location</a>
        </center>
      </div>
    </div>
  @else
    {!! Form::open(['action' => 'GuestsController@create', 'method' => 'POST', 'files'=>'true']) !!}
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
  @endif
@endsection