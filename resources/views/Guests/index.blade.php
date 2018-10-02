{{--GUEST INDEX--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>List of Guests</h1>
    <td><a href="guests/get_room" class="btn btn-primary">Add Guest</a></td>
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