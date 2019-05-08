@extends('layouts.app')
@section('title', 'Form Page')
@section('module', 'Form Page')
@section('content')

@if (Session::has('fail'))
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <strong>{{ Session::get('fail') }}</strong>
    </div>
@endif

<div class="container" style="padding-top:50px">
    <h2 style="text-align:center;">Forms</h2><br/>

        <form action="formIndex" method="POST">
            @csrf

            @auth('student')
            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="form1" type="submit" value="Form 1 - Student Details" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="form2" type="submit" value="Form 2 - Project Proposal" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="form4" type="submit" value="Form 4 - Project Appointment Record" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>
            @endauth

            @auth('staff')
            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="form2StudentProposal" type="submit" value="Download Form 2 - Project Proposals" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="form3Assessment" type="submit" value="Form 3 - Project Proposal Moderation" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="formTemplate" type="submit" value="Form Template" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>
            @endauth

            {{ Session::get('sPath') }}

        </form>

</div>

@endsection
