@extends('layouts.app')
@section('title', 'Template Version')
@section('module', 'Rubric Page')
@section('content')

<div class="container">
    <div class="col-md-12" style="padding-top:50px">
        <h2 style="text-align:center;">Template Version</h2><br/>

        <form action="storeTemplateVersion" method="POST">
            @csrf

            <div class="card">
                <div class="card-header bg-primary" style="color:white">
                    <b>Select template version for the following details</b>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Project 1 Rubric</th>
                            <td>
                                <select id="project1" name="project1" class="form-control">
                                    @foreach ($project1RubricDetails as $project1RubricDetail)
                                        <option value="{{ $project1RubricDetail['rubricId'] }}">{{ substr($project1RubricDetail['fileName'], strrpos($project1RubricDetail['fileName'], '/' )+1) }} - Version {{ $project1RubricDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Project 2 Rubric</th>
                            <td>
                                <select id="project2" name="project2" class="form-control">
                                    @foreach ($project2RubricDetails as $project2RubricDetail)
                                        <option value="{{ $project2RubricDetail['rubricId'] }}">{{ substr($project2RubricDetail['fileName'], strrpos($project2RubricDetail['fileName'], '/' )+1) }} - Version {{ $project2RubricDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Form 2 - Proposal</th>
                            <td>
                                <select id="form2" name="form2" class="form-control">
                                    @foreach ($form2TemplateDetails as $form2TemplateDetail)
                                        <option value="{{ $form2TemplateDetail['formTemplateId'] }}">{{ substr($form2TemplateDetail['fileName'], strrpos($form2TemplateDetail['fileName'], '/' )+1) }} - Version {{ $form2TemplateDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Form 3 - Project Requirements</th>
                            <td>
                                <select id="form3" name="form3" class="form-control">
                                    @foreach ($form3TemplateDetails as $form3TemplateDetail)
                                        <option value="{{ $form3TemplateDetail['formTemplateId'] }}">{{ substr($form3TemplateDetail['fileName'], strrpos($form3TemplateDetail['fileName'], '/' )+1) }} - Version {{ $form3TemplateDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Form 4 (i) - Project I Appointment Record</th>
                            <td>
                                <select id="form4i" name="form4i" class="form-control">
                                    @foreach ($form4iTemplateDetails as $form4iTemplateDetail)
                                        <option value="{{ $form4iTemplateDetail['formTemplateId'] }}">{{ substr($form4iTemplateDetail['fileName'], strrpos($form4iTemplateDetail['fileName'], '/' )+1) }} - Version {{ $form4iTemplateDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Form 4 (ii) - Project II Appointment Record</th>
                            <td>
                                <select id="form4ii" name="form4ii" class="form-control">
                                    @foreach ($form4iiTemplateDetails as $form4iiTemplateDetail)
                                        <option value="{{ $form4iiTemplateDetail['formTemplateId'] }}">{{ substr($form4iiTemplateDetail['fileName'], strrpos($form4iiTemplateDetail['fileName'], '/' )+1) }} - Version {{ $form4iiTemplateDetail['version'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <br/>

            <div class="col-md-12" style="text-align:center">
                <button class="btn btn-primary" type="submit">Confirm</button>
            </div>

            <br/>

        </form>

    </div>
</div>

@endsection
