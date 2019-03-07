{{-- ATTENDANCE RECORD FORM --}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Attendance Record</h1>
    <small>Please pick the kost location and date</small>
    <hr>
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        
        {!! Form::open(['action' => 'AttendanceController@showRecord', 'method' => 'POST']) !!}
            <div class="form-group">
                {{Form::label('room_location', 'Room Location')}}
                @if($locations -> isEmpty())
                    <a href="/locations/create"></a>
                @else
                    <select name="room_location" class="form-control">
                        @foreach($locations as $location)
                            <option value="{{$location->id}}">
                                {{$location->name}}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="form-group">
                {{Form::label('atd_date', 'Attendance Date')}}
                {{Form::date('atd_date','',['class'=>'form-control'])}}
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
        
    </div>
@endsection