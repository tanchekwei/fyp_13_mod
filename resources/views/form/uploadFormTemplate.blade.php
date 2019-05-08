@extends('layouts.app')
@section('title', 'Upload Form Template')
@section('module', 'Form Page')
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

@if (\Session::has('uploadFormSuccess'))
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">x</span>
		<span class="sr-only">Close</span>
	</button>
	<strong>{{ \Session::get('uploadFormSuccess') }}</strong>
</div>
@endif

@if (\Session::has('removeFormTemplateSuccess'))
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">x</span>
		<span class="sr-only">Close</span>
	</button>
	<strong>{{ \Session::get('removeFormTemplateSuccess') }}</strong>
</div>
@endif

@if (\Session::has('removeFormTemplateFail'))
<div class="alert alert-danger alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">x</span>
		<span class="sr-only">Close</span>
	</button>
	<strong>{{ \Session::get('removeFormTemplateFail') }}</strong>
</div>
@endif

<div class="container">
    <div class="col-md-12" style="padding-top:25px">
        <h2 style="text-align:center;">Upload Form Template</h2><br />

        <div class="card">
            <div class="card-header bg-primary" style="color:white"><b>Upload Form Template</b></div>
            <div class="card-body">
                <form action="storeFormTemplate" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <table class="table table-bordered">
                        <tr>
                            <th>Form Type</th>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="form2" name="formType" value="form2" checked>
                                    <label class="form-check-label" for="form2">Form 2 - Project Proposal</label><br/>
                                    <i>(File type must be in .doc , .docx , .pdf format)</i>
                                </div>

                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="form3" name="formType" value="form3">
                                    <label class="form-check-label" for="form3">Form 3 - Project Requirement</label><br/>
                                    <i>(File type must be in .xsl , .xslx format)</i>
                                </div>

                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="form4i" name="formType" value="form4i">
                                    <label class="form-check-label" for="form4i">Form 4 (i) - Project I Appointment Record</label><br/>
                                    <i>(File type must be in .doc , .docx , .pdf format)</i>
                                </div>

                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="form4ii" name="formType" value="form4ii">
                                    <label class="form-check-label" for="form4ii">Form 4 (ii) - Project II Appointment Record</label><br/>
                                    <i>(File type must be in .doc , .docx , .pdf format)</i>
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
                                <input class="form-control" type="file" name="form" required>
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
            if (Session::has('formTemplate')) {
                $formTemplate = Session::get('formTemplate');
                $filePath = $formTemplate['fileName'];
                $fileName = substr($filePath, strrpos($filePath, '/' )+1);
            }
        @endphp

        <div class="card">
            <div class="card-header bg-danger" style="color:white"><b>Remove Latest Form Template</b></div>
            <div class="card-body">
                <form action="deleteFormTemplate" method="POST">
                    @csrf
                    <div class="form2">
                        <table class="table table-bordered">
                            <tr>
                                <th>Form Type</th>
                                <th>Form Name</th>
                                <th>Action</th>
                            </tr>

                            @if (Session::has('formTemplate'))
                                <tr>
                                    <td>{{ $formTemplate['type'] }}</td>
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
