@extends('layouts.app')

@include('includes.navbar')

@section('content')

    <h1>Create Invoice</h1>
    <div class='col-sm-12'>
        <small>Select Invoice Period and Location</small>
    </div>
    <div class='col-sm-12'>
        <small><a href="invoice/settings" class="btn btn-primary">Invoice Settings</a></small>
    </div>
    <hr>
    <div class="col-sm-2"></div>

    <div class="col-sm-8">
        {!! Form::open(['action' => 'InvoiceController@generateInvoice', 'method' => 'POST']) !!}
            <div class="form-group">
                {{Form::label('startDate', 'Start Date')}}
                {{Form::date('startDate','',['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('endDate', 'End Date')}}
                {{Form::date('endDate','',['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('locationID', 'Location')}}
                <select name="locationID" class="form-control">
                    @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                {{Form::label('invoiceDetailID', 'Invoice Detail ID')}}
                {{--  {{Form::number('invoiceDetailID','',['class'=>'form-control'])}}  --}}
                <select name="invoiceDetailID" class="form-control">
                    @foreach($invoice_details as $invoice_detail)
                        <option value="{{$invoice_detail->id}}">{{$invoice_detail->leg_code}}</option>
                    @endforeach
                </select>
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
    </div>

    <div class="col-sm-2"></div>
@endsection