@extends('layouts.app')
@include('includes.navbar')

@section('content')
    <a href="/guests" class="btn btn-Primary">Back</a>
    <h1>{{$guest->name}} Detail</h1>
    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>ID Card</th>
            <th>Entry Date</th>
            <th>Exit Date</th>
            <th>Room Number</th>
            
        </tr>
        <tr>
            <td>{{$guest->id}}</td>
            <td>{{$guest->name}}</td>
            <td><img src="../../../<?php echo $guest->id_path; ?>" style="height:200px;width:300px;" alt="ID Picture"></td>
            <td>{{$guest->entry_date}}</td>
            <td>{{$guest->exit_date}}</td>
            <td>{{$guest->room_number}}</td>
            
        </tr>
    </table>
    <div class="col-sm-6">
        
        <a href="/guests/{{$guest->id}}/edit" class="btn btn-warning">Edit</a>
        
    </div>
    <div class="col-sm-6">
            {!!Form::open(['action'=>['GuestsController@destroy',$guest->id],'method'=>'DELETE','class'=>'pull-right','onsubmit'=>"return confirm('Apakah anda yakin akan menghapus data ini?');"])!!}
                {{Form::hidden('_method','DELETE')}}
                {{Form::submit('Delete Data',['class'=>'btn btn-danger'])}}
            {!!Form::close()!!}
    </div>
@endsection