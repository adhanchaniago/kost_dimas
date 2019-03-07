@extends('layouts.app')
@include('includes.navbar')

@section('content')
    <a href="/locations" class="btn btn-primary">Back to List of Locations</a>
    <h1>{{$location->name}} Guests</h1>
        {!! Form::open(['action' => 'LocationsController@search', 'method' => 'POST']) !!}
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
            <input type="hidden" name="location_id" value={{$location->id}}>
            {{Form::hidden('_method','POST')}}
        {!! Form::close() !!}
        <table class="table table-striped" id="guest-table">
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>ID Card</th>
                <th>Entry Date</th>
                <th>Room Number</th>
                
            </tr>
            <?php 
                $counter = ($guests->currentpage()-1)* $guests->perpage() + 1;
            ?>
            @if($guests->isEmpty())
                @if($error_message == null)
                    <tr>
                        <td colspan="5">
                            <a href="/guests/get_room">There are no guests yet, click here to add guests</a>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="5">
                            {{$error_message}}
                        </td>
                    </tr>
                @endif
            @else
                @foreach($guests as $guest)
                    <tr>
                        <td>{{$counter}}</td>
                        <td>{{$guest->name}}</td>
                        <td><img src="../../../<?php echo $guest->id_path; ?>" style="height:200px;width:300px;"></td>
                        <td>{{$guest->entry_date}}</td>
                        <td>{{$guest->room_number}}</td>
                    </tr>
                    <?php 
                        $counter++;
                    ?>
                @endforeach
            @endif
        </table>
        <center>
        {{$guests->links()}};
        </center>
    
    {{--  <div class="col-sm-6">
        
        <a href="{{ route('guests.edit', $guest) }}" class="btn btn-warning">Edit</a>
    
    </div>
    <div class="col-sm-6">
            {!!Form::open(['action'=>['GuestsController@destroy',$guest->id],'method'=>'DELETE','class'=>'pull-right','onsubmit'=>"return confirm('Apakah anda yakin akan menghapus data ini?');"])!!}
                {{Form::hidden('_method','DELETE')}}
                {{Form::submit('Delete Data',['class'=>'btn btn-danger'])}}
            {!!Form::close()!!}
    </div>  --}}
@endsection