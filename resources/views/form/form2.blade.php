@extends('layouts.app')
@section('title', 'Form 2 Page')
@section('module', 'Form Page')
@section('content')

@if (\Session::has('errorDlForm2'))
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <p>{{ \Session::get('errorDlForm2') }}</p>
    </div>
@endif

@if (\Session::has('unsubmitProposalSuccess'))
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <p>{{ \Session::get('unsubmitProposalSuccess') }}</p>
    </div>
@endif

@if (\Session::has('uploadProposalSuccess'))
    <div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <p>{{ \Session::get('uploadProposalSuccess') }}</p>
    </div>
@endif

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

<div class="container">
    <div class="col-md-12" style="padding-top:25px">
        <h2 style="text-align:center;">Form 2 - Project Proposal</h2><br/>

        <form action="downloadProposalTemplate" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-success" style="color:white"><b>Download Proposal Template</b></div>
                <div class="card-body">
                    <p>Download Proposal Template: </p>
                    <div class="col-md-12" style="text-align:center">
                        <button class="btn btn-success">Download Proposal Template</button>
                    </div>
                </div>
            </div>
        </form>

        <br/>

        <form action="deleteProposal" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-dark" style="color:white"><b>Proposal Submission</b></div>
                <div class="card-body">
                    <div class="form2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Submmited work</th>
                                    <th>Action</th>
                                </tr>

                                @php
                                    $form2Array = Session::get('form2Array');
                                    $studentDetail = $form2Array[0];
                                    $teamDetail = $form2Array[1];
                                    $projectDetail = $form2Array[2];
                                    $form2Detail = $form2Array[3];

                                    if (!$form2Detail->isEmpty()) {
                                        foreach ($form2Detail as $form2) {
                                            $filePath = $form2['fileName'];
                                            $fileName = substr($filePath, strrpos($filePath, '/' )+1);
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td>
                                        @if (!$form2Detail->isEmpty())
                                            {{ $fileName }}
                                        @else
                                            <i>No work is submitted</i>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" type="submit" @if ($form2Detail->isEmpty()) disabled @endif>Unsubmit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>

        <br/>

        <form action="storeProposal" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header bg-primary" style="color:white"><b>Upload Proposal</b></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:15em">* Individual Title</th>
                            <td>
                                <input type="text" name="individualTitle" class="form-control" required
                                @if ($studentDetail['individualTitle'] !== NULL)
                                    value="{{ $studentDetail['individualTitle'] }}"
                                @endif>
                            </td>
                        </tr>

                        <tr>
                            <th style="width:15em">Client Name</th>
                            <td>
                                <input type="text" name="clientName" class="form-control"
                                @if ($projectDetail['clientName'] !== NULL)
                                    value="{{ $projectDetail['clientName'] }}"
                                @endif>
                            </td>
                        </tr>

                        <tr>
                            <th style="width:15em">Competition</th>
                            <td>
                                <input type="text" name="competitionName" class="form-control"
                                @if ($teamDetail['competitionName'] !== NULL)
                                    value="{{ $teamDetail['competitionName'] }}"
                                @endif>
                            </td>
                        </tr>

                        <tr>
                            <td colspan='2'><br/></td>
                        </tr>

                        <tr>
                            <th colspan='2'>* Choose the file you want to upload as proposal:</th>
                        </tr>

                        <tr>
                            <td colspan='2'>
                                <input class="form-control" type="file" name="proposal" @if (!$form2Detail->isEmpty()) disabled @endif required>
                                <br/>
                                <i style="color:blue">*File type must be .doc , .docx , .pdf format</i>
                            </td>
                        </tr>
                    </table>

                    <div class="col-md-12" style="text-align:center">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>

                </div>
            </div>
        </form>

        <br/>

    </div>
</div>

@endsection
