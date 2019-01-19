{{--LOCATION CREATE--}}
@extends('layouts.app')
@include('includes.navbar')
@section('content')
    <a href="/locations" class="btn btn-primary">Back to List of Locations</a>
    <h1>Add Location</h1>
    {!! Form::open(['action' => 'LocationsController@store', 'method' => 'POST', 'files'=>'true']) !!}
    {{--  <div class="form-group">
      {{Form::label('room_number', 'Room Number')}}
      {{Form::select('room_number',[1=>'Pendapatan Pribadi',2=>'Tambahan Modal', 3=>'Lain - Lain'],'',['class'=>'form-control'])}}
    </div>  --}}
    <div class="form-group">
      {{Form::label('name', 'Name')}}
      {{Form::text('name','',['class'=>'form-control','placeholder'=>'Location Name'])}}
    </div>
    <div class="form-group">
      {{Form::label('capacity', 'Capacity')}}
      {{Form::number('capacity','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
      {{Form::label('address', 'Address')}}
      {{Form::textarea('address','',['class'=>'form-control'])}}
    </div>
    <div class="form-group">
        <table id="selectInput" class="table table-striped">
            <tr>
                <th>Room Types</th>
                <th>Quantity</th>
                <th>ProRate</th>
                <th>Monthly Rate</th>
                <th>
                    <button class="add_form_field">Add New Field &nbsp; <span style="font-size:16px; font-weight:bold;">+ </span></button> 
                </th>
            </tr>
            <tr>
              <td>
                <input type="text" class="form-control" name="room_type[]">
              </td>
              <td>
                <input type="number" min="1" class="form-control" name="room_type_quantity[]">
              </td>
              <td>
                <input type="number" min="1" class="form-control" name="room_type_rate[]" placeholder="ProRate">
              </td>
              <td>
                <input type="number" min="1" name="room_type_rate_month[]" placeholder="Monthly Rate" class="form-control">
              </td>
              <td colspan="2">
                <center>
                  <a href="#" class="delete btn btn-danger">Delete</a>
                </center>
              </td>
            </tr>
        </table>
    </div>
    {{Form::hidden('_method','POST')}}
    {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
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

                $(wrapper).append('<tr><td><input type="text" name="room_type[]" class="form-control"></td><td><input type="number" name="room_type_quantity[]" class="form-control"></td><td><input type="number" min="1" class="form-control" name="room_type_rate[]"></td><td colspan="2"><center><a href="#" class="delete btn btn-danger">Delete</a></center></td></tr>'); 
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