@extends('layouts.app')

@include('includes.navbar')

@section('content')

    <h1>Create Invoice</h1>
    <div class='col-sm-12'>
        <small>Select Invoice ID</small>
    </div>
    <hr>
    <div class="col-sm-2"></div>

    <div class="col-sm-8">
        @if(count($invoices) < 1)
            <a href="/invoice">Click here to create an invoice first</a>
        @else
            {!! Form::open(['action' => 'InvoiceController@generateReceipt', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{Form::label('locationID','Location Name')}}
                    <select name="locationID" id="locationID" class="form-control">
                        @foreach($locations as $location)
                            <option value="{{$location->id}}">{{$location->name}}</option>
                        @endforeach
                    </select>
                    {{Form::label('month','Month')}}
                    <select name="month" id="month" class="form-control">
                        @for($x = 0; $x < sizeof($months); $x++)
                            <option value="{{$x+1}}">{{$months[$x]}}</option>
                        @endfor
                    </select>
                    {{Form::label('year','Year')}}
                    <select name="year" id="year" class="form-control">
                        @for($y = 0; $y < sizeof($years); $y++)
                            <option value="{{$years[$y]}}">{{$years[$y]}}</option>
                        @endfor
                    </select>
                    {{--  {{Form::label('invoiceID', 'Invoice ID')}}
                    <select name="invoiceID" class="form-control">
                        @foreach($invoices as $invoice)
                            <option value="{{$invoice->id}}">{{$invoice->invoiceNumber}}</option>
                        @endforeach
                    </select>  --}}
                </div>
                
                {{Form::hidden('_method','POST')}}
                {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
            {!! Form::close() !!}
        @endif
    </div>

    <div class="col-sm-2"></div>
@endsection