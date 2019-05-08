@extends('layouts.app')
@section('title', 'Form 4 Page')
@section('module', 'Form Page')
@section('content')

@if (\Session::has('errorDlForm4i'))
    <div class="alert alert-danger">
        <p>{{ \Session::get('errorDlForm4i') }}</p>
    </div>
@endif

@if (\Session::has('errorDlForm4ii'))
    <div class="alert alert-danger">
        <p>{{ \Session::get('errorDlForm4ii') }}</p>
    </div>
@endif

<div class="container">
    <div class="col-md-12" style="padding-top:50px">
        <h2 style="text-align:center;">Form 4 - Project Appointment Record</h2><br />

        <form action="downloadForm4iTemplate" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-success" style="color:white">Download Project I Appointment Record Template</div>
                <div class="card-body">
                    <p>Project I Appointment Record Template: </p>
                    <div class="col-md-12" style="text-align:center">
                        <button name="form4i" class="btn btn-success">Download Project I Appointment Record Template</button>
                    </div>
                </div>
            </div>
        </form>

        <br/>

        <form action="downloadForm4iiTemplate" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-success" style="color:white">Download Project II Appointment Record Template</div>
                <div class="card-body">
                    <p>Project II Appointment Record Template: </p>
                    <div class="col-md-12" style="text-align:center">
                        <button name="form4ii" class="btn btn-success">Download Project II Appointment Record Template</button>
                    </div>
                </div>
            </div>
        </form>

        <br/>

    </div>
</div>

@endsection
