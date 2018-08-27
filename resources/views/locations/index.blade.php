{{--LOCATION INDEX--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>List of Locations</h1>
    <table class="table table-striped">
        <tr>
            <th>Name</th>
            <th>Capacity</th>
            <th>Details</th>
        </tr>
        @foreach($locations as $location)
        <tr>
            <td>{{$location->name}}</td>
            <td>{{$location->capacity}}</td>
            <td><a href="{{ route('locations.show', $location->id) }}" class="btn btn-default">Guests</a></td>
        </tr>
        @endforeach
    </table>
@endsection