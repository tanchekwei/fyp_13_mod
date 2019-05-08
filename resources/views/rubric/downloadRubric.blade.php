@extends('layouts.app')
@section('title', 'Download Assessment Rubric')
@section('module', 'Rubric Page')
@section('content')

@if (\Session::has('errorDlProject1Rubric'))
    <div class="alert alert-danger">
        <p>{{ \Session::get('errorDlProject1Rubric') }}</p>
    </div>
@endif

@if (\Session::has('errorDlProject2Rubric'))
    <div class="alert alert-danger">
        <p>{{ \Session::get('errorDlProject2Rubric') }}</p>
    </div>
@endif

<div class="container">
    <div class="col-md-12" style="padding-top:50px">
        <h2 style="text-align:center;">Assessment Rubrics</h2><br />

        <form action="downloadProject1Rubric" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-success" style="color:white">Download Project I Assessment Rubric</div>
                <div class="card-body">
                    <p>Project I Assessment Rubric: </p>
                    <div class="col-md-12" style="text-align:center">
                        <button name="form4i" class="btn btn-success">Download Project I Assessment Rubric</button>
                    </div>
                </div>
            </div>
        </form>

        <br/>

        <form action="downloadProject2Rubric" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-success" style="color:white">Download Project II Assessment Rubric</div>
                <div class="card-body">
                    <p>Project II Assessment Rubric: </p>
                    <div class="col-md-12" style="text-align:center">
                        <button name="form4ii" class="btn btn-success">Download Project II Assessment Rubric</button>
                    </div>
                </div>
            </div>
        </form>

        <br/>

    </div>
</div>

@endsection
