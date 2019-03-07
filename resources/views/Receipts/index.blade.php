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
        @if($invoices->isEmpty())
            <a href="/invoice">Click here to create an invoice first</a>
        @else
            {!! Form::open(['action' => 'InvoiceController@generateReceipt', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{Form::label('invoiceID', 'Invoice ID')}}
                    <select name="invoiceID" class="form-control">
                        @foreach($invoices as $invoice)
                            <option value="{{$invoice->id}}">{{$invoice->invoiceNumber}}</option>
                        @endforeach
                    </select>
                </div>
                
                {{Form::hidden('_method','POST')}}
                {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
            {!! Form::close() !!}
        @endif
    </div>

    <div class="col-sm-2"></div>
@endsection