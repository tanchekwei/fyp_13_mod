@extends('layouts.app')
@section('title', 'Form 3 Page')
@section('module', 'Form Page')
@section('content')

@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">x</span>
			<span class="sr-only">Close</span>
		</button>
        <strong>{{ Session::get('success') }}</strong>
    </div>
@endif

<div class="container">
    <div class="col-md-12" style="padding-top:50px">
        <h2 style="text-align:center;">Form 3 - Project Proposal Moderation</h2><br />
        {{ Form::open(array('action' => array('FormController@storeForm3', $role))) }}
            @csrf

            <div class="form3">
                <div class="card">
                    <div class="card-header bg-primary" style="color:white">
                        <b>1. Project Details</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width:5cm">Student Name</th>
                                    <td style="width:8cm">{{ $studentDetail['studentName'] }}</td>
                                    <th scope="row" style="width:5cm">Programme</th>
                                    <td>{{ $programmeName }}</td>
                                </tr>

								@if(Session::get('role2') == "supervisor")
									<tr>
										<th scope="row">Supervisor Name</th>
										<td>{{ $supervisorName }}</td>
										<th scope="row">Cohort</th>
										<td>{{ $cohortId }}</td>
									</tr>

									<tr>
										<th scope="row">Moderator Name</th>
										<td colspan="3">{{ $moderatorName }}</td>
									</tr>
								@else
									<tr>
										<th scope="row">Supervisor Name</th>
										<td>{{ $moderatorName }}</td>
										<th scope="row">Cohort</th>
										<td>{{ $cohortId }}</td>
									</tr>

									<tr>
										<th scope="row">Moderator Name</th>
										<td colspan="3">{{ $supervisorName }}</td>
									</tr>
								@endif	

                                <tr>
                                    <th scope="row">Project Title/Scope</th>
                                    <td colspan="3">{{ $projectDetail['title'] }}</td>
                                </tr>

                                <tr>
                                    <th scope="row">Project Type</th>
                                    <td>{{ $projectDetail['cluster'] }}</td>
                                    <th scope="row">Project Category</th>
                                    <td>{{ $projectDetail['projectGroup'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br/>

                <div class="card">
                    <div class="card-header bg-primary" style="color:white">
                        <b>2. Project Scope Moderation</b> (to be filled by Moderator) (Please tick if comply)
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Project Requirements</th>
                                    <th>Comply</th>
                                </tr>
                            </thead>

                            @php
                                $requirementIdArray = array();
                                $r = 0;
                            @endphp

                            <tbody>
                                @foreach ($form3RequirementDetails as $form3RequirementDetail)
                                    @foreach ($projectRequirementLists as $projectRequirementList)
                                        @if ($form3RequirementDetail['requirementId'] == $projectRequirementList['requirementId'])
                                            <tr>
                                                <td>
                                                    <b>{{ $projectRequirementList['requirementName'] }}</b>
                                                    <br/>
                                                    {{ $projectRequirementList['description'] }}
                                                </td>
                                                <td style="text-align:center;">
                                                    <input type="checkbox" name="{{ $form3RequirementDetail['form3RequirementId'] }}" style="width:20px; height:20px;"
                                                        @if ($form3RequirementDetail['comply'] !== NULL && $form3RequirementDetail['comply'] !== "Not Complied")
                                                            checked
                                                        @endif
                                                        @if ($role == "supervisor")
                                                            disabled
                                                        @endif>
                                                </td>
                                            </tr>
                                            @php
                                                $requirementIdArray[$r] = $form3RequirementDetail['form3RequirementId'];
                                                $r++;
                                            @endphp
                                        @endif
                                    @endforeach
                                @endforeach

                                @php Session::put('requirementIdArray', $requirementIdArray); @endphp
                            </tbody>
                        </table>
                    </div>
                </div>

                <br/>

                <div class="card">
                    <div class="card-header bg-primary" style="color:white">
                        <b>3. Feedback</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Comments and Changes Recommended<br/>
                                        (by Moderator)
                                    </th>
                                    <th>Actions Taken<br/>
                                        (by Supervisor)
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <textarea name="changesRecommended" rows="10" cols="49" class="form-control" @if ($role == "supervisor") disabled @endif>@if ($form3AssessmentDetail['feedbackComment'] != NULL){{ $form3AssessmentDetail['feedbackComment'] }}@endif</textarea>
                                    </td>
                                    <td>
                                        <textarea name="actionTaken" rows="10" cols="49" class="form-control" @if ($role != "supervisor") disabled @endif>@if ($form3AssessmentDetail['feedbackAction'] != NULL){{ $form3AssessmentDetail['feedbackAction'] }}@endif</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br/>

                <div class="card">
                    <div class="card-header bg-primary" style="color:white">
                        <b>4. Assessment</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Criteria</th>
                                    <th>Mark Allocation</th>
                                    <th>Mark</th>
                                </tr>
                            </thead>

                            @php
                                $proposalCriteriaArray = array();
                                $p = 0;
                            @endphp

                            @foreach ($criteriaLists as $criteriaList)
                                @foreach ($studentCriteriaDetails as $studentCriteriaDetail)
                                    @if ($criteriaList['criteriaId'] == $studentCriteriaDetail['criteriaId'])
                                        <tr>
                                            <td>{{ $criteriaList['criteriaName'] }}</td>
                                            <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                                            <td style="text-align:center;">{{ substr($criteriaList['good'], strpos($criteriaList['good'], "-") + 1) }}</td>
                                            @php
                                                $maxValue = substr($criteriaList['good'], strpos($criteriaList['good'], "-") + 1);
                                                $min = explode("-", $criteriaList['poor']);
                                                $minValue = $min[0] - 1;
                                            @endphp

                                            <td style="text-align:center;">
                                                <input type="number" name="{{ $studentCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                @if ($studentCriteriaDetail['markModerator'] !== NULL)
                                                    value="{{ $studentCriteriaDetail['markModerator'] }}"
                                                @endif
                                                @if ($role == "supervisor")
                                                    disabled
                                                @endif>
                                            </td>
                                        </tr>
                                        @php
                                            $proposalCriteriaArray[$p] = $studentCriteriaDetail['rubricCriteriaId'];
                                            $p++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach

                            @php Session::put('proposalCriteriaArray', $proposalCriteriaArray); @endphp

                            <tr>
                                <td colspan="3" style="text-align:right;"><b>Total Marks</b></td>
                                <td style="text-align:center;">{{ $finalMark }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

            <div class="signature">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="text-align:left">Moderated By:</th>
                            <th style="text-align:left">Received By:</th>
                        </tr>

                        <tr>
                            <td>Moderator's Signature:
                                <input type="text" style="border:0; border-bottom:1px solid #000; text-align:left"/>
                            </td>
                            <td>Supervisor's Signature:
                                <input type="text" style="border:0; border-bottom:1px solid #000"/>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Moderation Date:
                                @if ($form3AssessmentDetail['dateByModerator'] != NULL)
                                    {{ date('d-m-Y', strtotime($form3AssessmentDetail['dateByModerator'])) }}
                                @endif
                            </td>

                            <td>
                                Received Date:
                                @if ($form3AssessmentDetail['dateBySupervisor'] != NULL)
                                    {{ date('d-m-Y', strtotime($form3AssessmentDetail['dateBySupervisor'])) }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="text-align:center">
                <button class="btn btn-primary" type="submit" value="save">Save</button>
            </div>
        {{ Form::close() }}

        <br/>

        {{ Form::open(array('action' => 'FormController@printForm3PDF')) }}
            @csrf
            <div style="text-align:center">
			{{-- <button class="btn btn-primary" type="submit" value="pdf">Generate as PDF</button> --}}
				<a class="btn btn-primary" href="{{ url('printForm3') }}" target="_blank">Generate as PDF</a>
            </div>
        {{ Form::close() }}

    </div>
</div>

@endsection
