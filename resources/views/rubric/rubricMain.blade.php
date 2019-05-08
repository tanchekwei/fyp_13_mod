@extends('layouts.app')
@section('title', 'Rubric Page')
@section('module', 'Rubric Page')
@section('content')

@if (\Session::has('templateVersionSuccess'))
    <div class="alert alert-success">
        <p>{{ \Session::get('templateVersionSuccess') }}</p>
    </div>
@endif

@if (Session::has('fail'))
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <strong>{{ Session::get('fail') }}</strong>
    </div>
@endif

<div class="container" style="padding-top:25px">
    <h2 style="text-align:center;">Rubric Assessment</h2><br/>

        <form action="rubricIndex" method="POST">
            @csrf
            {{-- <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="templateVersion" type="submit" value="Template Version" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/> --}}

            @auth('staff')
            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="rubricAssessment" type="submit" value="Assessment Template" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="studentAssessment" type="submit" value="Student Assessment" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
				<input name="markSummaryProject1" type="submit" value="Mark Summary - Project I" class="btn btn-outline-dark btn-lg" style="width:100%"/>
            </div><br/>

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="markSummaryProject2" type="submit" value="Mark Summary - Project II" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>
            @endauth

            <div class="card">
                <div class="card-header" style="width:100%; padding:0">
                    <input name="downloadAssessmentRubrics" type="submit" value="Download Assessment Rubrics" class="btn btn-outline-dark btn-lg" style="width:100%"/>
                </div>
            </div><br/>

        </form>

</div>

@endsection
