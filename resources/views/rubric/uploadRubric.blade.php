@extends('layouts.app')
@section('title', 'Upload Rubric')
@section('module', 'Rubric Page')
@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (\Session::has('removeRubricTemplateSuccess'))
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">x</span>
		<span class="sr-only">Close</span>
	</button>
	<strong>{{ \Session::get('removeRubricTemplateSuccess') }}</strong>
</div>
@endif

@if (\Session::has('removeRubricTemplateFail'))
<div class="alert alert-danger alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">x</span>
		<span class="sr-only">Close</span>
	</button>
	<strong>{{ \Session::get('removeRubricTemplateFail') }}</strong>
</div>
@endif

<div class="container">
    <div class="col-md-12" style="padding-top:25px">
        <h2 style="text-align:center;">Upload Rubric</h2><br />

        <div class="card">
            <div class="card-header bg-primary" style="color:white"><b>Upload Rubric</b></div>
            <div class="card-body">
                <form action="uploadRubric" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <table class="table table-bordered">
                        <tr>
                            <th>Project Type</th>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="projectI" name="rubricType" value="projectI" checked>
                                    <label class="form-check-label" for="projectI">Project I</label>
                                </div>

                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="projectII" name="rubricType" value="projectII">
                                    <label class="form-check-label" for="projectII">Project II</label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan='2'><br/></td>
                        </tr>

                        <tr>
                            <th colspan='2'>Choose the file you want to upload as rubric: </th>
                        </tr>

                        <tr>
                            <td colspan='2'>
                                <input class="form-control" type="file" name="rubric" required>
                            </td>
                        </tr>

                        <tr>
                            <td colspan='2' style="color:blue">
                                <i>*File type must be .xsl or .xslx format</i>
                            </td>
                        </tr>
                    </table>

                    <div class="col-md-12" style="text-align:center">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <br/>

        @php
            if (Session::has('rubricTemplate')) {
                $rubricTemplate = Session::get('rubricTemplate');
                $filePath = $rubricTemplate['fileName'];
                $fileName = substr($filePath, strrpos($filePath, '/' )+1);
            }
        @endphp

        <div class="card">
            <div class="card-header bg-danger" style="color:white"><b>Remove Latest Rubric Template</b></div>
            <div class="card-body">
                <form action="deleteRubricTemplate" method="POST">
                    @csrf
                    <div class="form2">
                        <table class="table table-bordered">
                            <tr>
                                <th>Rubric Type</th>
                                <th>Rubric Name</th>
                                <th>Action</th>
                            </tr>

                            @if (Session::has('rubricTemplate'))
                                <tr>
                                    <td>{{ $rubricTemplate['type'] }}</td>
                                    <td>{{ $fileName }}</td>
                                    <td><button class="btn btn-danger" type="submit">Remove</button></td>
                                </tr>
                            @else
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><button class="btn btn-danger" type="submit" disabled>Remove</button></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
