{{--GUEST UPDATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <h1>Edit Location</h1>
    <a href="/locations" class="btn btn-primary">Back to Locations</a>
    {!!Form::open(['action'=>['LocationsController@destroy',$location->id],'method'=>'DELETE','class'=>'pull-right','onsubmit'=>"return confirm('Are you sure you want to delete ".$location->name."?');"])!!}
        {{Form::hidden('_method','DELETE')}}
        {{Form::submit('Delete Data',['class'=>'btn btn-danger'])}}
    {!!Form::close()!!}
    <hr>
    {!! Form::open(['action' => ['LocationsController@update',$location->id], 'method' => 'PUT']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Name')}}
      {{Form::text('name',$location->name,['class'=>'form-control','placeholder'=>'Guest Name'])}}
    </div>
    <div class="form-group">
      {{Form::label('address', 'Address')}}
      {{Form::textarea('address',$location->address,['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('capacity', 'Total Capacity')}}
      {{Form::number('total_capacity',$location->capacity,['class'=>'form-control'])}}
    </div>
     <div class="form-group">
        
        <table id="selectInput" class="table table-striped">
            <tr>
                <th>Room Types</th>
                <th>Quantity</th>
                <th>Action</th>
                <th>
                    <button class="add_form_field">Add New Field &nbsp; <span style="font-size:16px; font-weight:bold;">+ </span></button> 
                </th>
            </tr>
            @foreach ($room_details as $room_detail)
                <tr>
                    <td>
                        {{Form::text('room_type[]',$room_detail->room_type,['class'=>'form-control','placeholder'=>'Room Type'])}}
                    </td>
                    <td>
                        {{Form::number('room_type_quantity[]',$room_detail->quantity,['class'=>'form-control','placeholder'=>'Capacity'])}}
                    </td>
                    <td>
                        {{Form::number('room_type_rate[]',$room_detail->daily_rate,['class'=>'form-control','placeholder'=>'Pro Rate'])}}
                    </td>
                    <td>
                        {{Form::number('room_type_rate_month[]',$room_detail->monthly_rate,['class'=>'form-control','placeholder'=>'Monthly Rate'])}}
                    </td>
                    <td colspan="2">
                        <center>
                            <a href="#" class="delete btn btn-danger">Delete</a>
                        </center>
                    </td>
                    {{Form::hidden('room_detail_id[]',$room_detail->id)}}
                </tr>
            @endforeach
        </table>
    </div>
    
    {{Form::hidden('_method','PUT')}}
    {{Form::submit('Update',['class'=>'btn btn-primary form-control'])}}
  {!! Form::close() !!}
   
@endsection

@section('scripts')
    <script>
    // $(document).ready(function(){
    //   $("#btn1").click(function(){
    //       $("#wek").append("<input type='text' class='form-control' id='wex' placeholder='Products'>");
    //   });
    // });
    $(document).ready(function() {
        var max_fields      = 10;
        var wrapper         = $("#selectInput");
        var add_button      = $(".add_form_field");

        var x = 1;
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){
                x++;

                $(wrapper).append('<tr><td><input type="text" name="room_type[]" class="form-control"></td><td><input type="number" name="room_type_quantity[]" class="form-control"></td><td><input type="number" min="1" class="form-control" name="room_type_rate[]" placeholder="Daily Rate"></td><td><input type="number" min="1" class="form-control" name="room_type_rate_month[]" placeholder="Monthly Rate"></td><td colspan="2"><center><a href="#" class="delete btn btn-danger">Delete</a></center></td></tr>'); 
            }
            else
            {
                alert('You Reached the limits')
            }
        });

        $(wrapper).on("click",".delete", function(e){
            e.preventDefault(); $(this).parents('tr').remove(); x--;
        })
    });
  </script>
@endsection