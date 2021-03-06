{{--LOCATION INDEX--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>List of Locations</h1>
    <a href="/locations/create" class="btn btn-primary">Add New Location</a>
    <hr>
    <table class="table table-striped">
        <tr>
            <th>Name</th>
            <th>Capacity</th>
            <th>Details</th>
            <th>Edit</th>
        </tr>
        @foreach($locations as $location)
        <tr>
            <td>{{$location->name}}</td>
            <td>{{$location->capacity}}</td>
            <td><a href="{{ route('locations.show', $location->id) }}" class="btn btn-default">Guests</a></td>
            <td><a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning">Edit</a></td>
        </tr>
        @endforeach
    </table>
@endsection