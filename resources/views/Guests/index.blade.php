{{--GUEST INDEX--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>List of Guests</h1>
    <div class="row">
        <div class="col-sm-4">
            <a href="guests/get_room" class="btn btn-primary form-control">Add Guest</a>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            {!! Form::open(['action' => 'GuestsController@search', 'method' => 'POST']) !!}
                <table style="margin-top:10px;">
                    <tr>
                        <td>
                            {{Form::text('guest_name','',['class'=>'form-control','placeholder'=>'Search By Guest Name'])}}
                        </td>
                        <td>
                            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
                        </td>
                    </tr>    
                </table>
                {{Form::hidden('_method','POST')}}
            {!! Form::close() !!}
        </div>
    </div>
    <hr>
    <table class="table table-striped">
        <tr>
            <th>Name</th>
            <th>Entry Date</th>
            <th>Location</th>
            <th>Room</th>
            <th>Details</th>
        </tr>
        @foreach($guests as $guest)
        <tr>
            <td>{{$guest->name}}</td>
            <td>{{$guest->entry_date}}</td>
            <td>{{$guest->location->name}}</td>
            <td>{{$guest->room_number}}</td>
            <td><a href="guests/{{$guest->id}}" class="btn btn-default">Details</a></td>
        </tr>
        @endforeach
    </table>
    <center>
      {{$guests->links()}};
    </center>
@endsection