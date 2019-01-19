@extends('layouts.app')

@include('includes.navbar')

@section('content')

    <h1>Invoice Settings</h1>
    <div class='col-sm-12'>
        <a href="/invoice" class="btn btn-primary">Back</a>
    </div>
    <div class="col-sm-12">
        <small>The numbers below will be used in the invoice, if there are any changes you want to make, please modify the form below</small>
        <hr>
    </div>
    <div class="col-sm-2"></div>

    <div class="col-sm-8">
        {!! Form::open(['action' => 'InvoiceController@modifySettings', 'method' => 'POST']) !!}
            <div class="form-group">
                {{Form::label('vendor_no', 'Vendor No.')}}
                {{Form::text('vendor_no',$invoice_detail->vendor_no,['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('co_no', 'Co No.')}}
                {{Form::text('co_no',$invoice_detail->co_no,['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('leg_code', 'LEG Approval Code')}}
                {{Form::text('leg_code',$invoice_detail->leg_code,['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('bill_to', 'Bill To')}}
                {{Form::textarea('bill_to',$invoice_detail->bill_to,['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('acc_bank', 'Bank Name')}}
                {{Form::text('acc_bank',$invoice_detail->acc_bank,['class'=>'form-control'])}}
            </div>
            <div class="form-group">
                {{Form::label('acc_num', 'Account Number')}}
                {{Form::text('acc_num',$invoice_detail->acc_num,['class'=>'form-control'])}}
            </div>
            {{Form::hidden('_method','POST')}}
            {{Form::submit('Submit',['class'=>'btn btn-primary form-control'])}}
        {!! Form::close() !!}
    </div>

    <div class="col-sm-2"></div>
@endsection