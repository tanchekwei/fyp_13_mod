@extends('layouts.app')
@section('title', 'Student Rubric')
@section('module', 'Rubric Page')
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

@php
    if ($rubricType == "Project I") {
        $title = "BACS3403 PROJECT I ASSESSMENT";
    } else {
        $title = "BACS3413 PROJECT II ASSESSMENT";
    }
@endphp

{{-- <div class="container">
    <div class="col-md-12" style="padding-top:20px"> --}}
        <h2 style="text-align:center;">{{ $title }}</h2><br/>

        <div class="rubric">
            <table class="table table-bordered">
				@if(Session::get('role') == "supervisor")
					<tr>
						<th>Supervisor's Name</th>
						<td> {{ $supervisorName }}</td>
						<th>Moderator's Name</th>
						<td> {{ $moderatorName }}</td>
					</tr>
				@else
					<tr>
						<th>Supervisor's Name</th>
						<td> {{ $moderatorName }}</td>
						<th>Moderator's Name</th>
						<td> {{ $supervisorName }}</td>
					</tr>
				@endif	

                <tr>
                    <th>Student's Name</th>
                    <td> {{ $studentDetail['studentName'] }}</td>
                    <th>Project Title</th>
                    <td> {{ $projectTitle }}</td>
                </tr>

                <tr>
                    <th>Registration ID</th>
                    <td> {{ $studentDetail['studentId'] }}</td>
                    <th>Individual Title</th>
                    <td> {{ $studentDetail['individualTitle'] }}</td>
                </tr>

                <tr>
                    <th>Programme</th>
                    <td colspan='3'>{{ $programmeName }}</td>
                </tr>
            </table>

            {{ Form::open(array('action' => array('RubricController@studentMark', $role))) }}
            {{-- <form action="{{ url('studentMark', $role) }}" method="POST"> --}}
                @csrf
                <table class="table table-bordered" style="font-size:12px">
                    <thead style="background-color:lightgrey">
                        <tr>
                            <th rowspan='2'>CLO</th>
                            <th rowspan='2'>Artifact</th>
                            <th rowspan='2'>Marks</th>
                            <th rowspan='2'>Criteria</th>
                            <th rowspan='2'>Descriptors</th>
                            <th colspan='3'>Assessment Criteria</th>
                            <th rowspan='2'>Mark by Supervisor</th>
                            <th rowspan='2'>Mark by Moderator</th>
                            <th rowspan='2'>Final Mark</th>
                            <th rowspan='2'>Mark Subtotal by CLO</th>
                        </tr>

                        <tr>
                            <th style="text-align:center;">Poor</th>
                            <th style="text-align:center;">Accomplished</th>
                            <th style="text-align:center;">Good</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $rubricCriteriaArray = array();
                            $totalMark = 0;
                            $c = 0;
                            $f = 0;
                            $i = 0;
                        @endphp

                        @foreach ($rubricArtifactDetails as $rubricArtifactDetail)
                            @foreach ($artifactLists as $artifactList)
                                @if ($rubricArtifactDetail['artifactId'] == $artifactList['artifactId'])
                                    @php
                                        $idCount = 0;
                                    @endphp
                                    <tr>
                                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactList['CLO'] }}</td>
                                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactList['description'] }}</td>
                                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactList['totalMarks'] }}</td>
                                        @php
                                            $totalMark += $artifactList['totalMarks'];
                                        @endphp

                                        @foreach ($rubricCriteriaDetails as $rubricCriteriaDetail)
                                            @foreach ($criteriaLists as $criteriaList)
                                                @if ($rubricArtifactDetail['rubricArtifactId'] == $rubricCriteriaDetail['rubricArtifactId'] && $rubricCriteriaDetail['criteriaId'] == $criteriaList['criteriaId'])
                                                    @if ($idCount == 0)
                                                        <td>{{ $criteriaList['criteriaName'] }}</td>
                                                        <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                                                        <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                                        <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                                        <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                                        @php
                                                            $maxValue = substr($criteriaList['good'], strpos($criteriaList['good'], "-") + 1);
                                                            $min = explode("-", $criteriaList['poor']);
                                                            $minValue = $min[0] - 1;

                                                            $rubricCriteriaArray[$c] = $rubricCriteriaDetail['rubricCriteriaId'];
                                                            $c++;
                                                        @endphp
                                                        <td style="text-align:center;">
                                                            @if ($role == "supervisor")
                                                                <input type="number" name="s_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                @if ($rubricCriteriaDetail['markSupervisor'] !== null )
                                                                    value="{{ $rubricCriteriaDetail['markSupervisor'] }}"
                                                                @endif>
                                                            @endif
                                                        </td>
                                                        <td style="text-align:center;">
                                                            @if ($rubricType == "Project I")
                                                                @if ($i == 0)
                                                                    <input type="number" name="m_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                    @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                                        value="{{ $rubricCriteriaDetail['markModerator'] }}"
                                                                    @endif>
                                                                @endif
                                                            @elseif ($rubricType == "Project II")
                                                                @if ($i != 3)
                                                                    <input type="number" name="m_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                    @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                                        value="{{ $rubricCriteriaDetail['markModerator'] }}"
                                                                    @endif>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td style="text-align:center;">
                                                            @if ($role == "supervisor")
                                                                {{ $finalMarkArray[$f] }}
                                                            @endif
                                                        </td>
                                                        <td rowspan='{{ $idArray[$i] }}' style="text-align:center;">
                                                            @if ($role == "supervisor")
                                                                <b>{{ $subtotalCLOArray[$i] }}</b>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <tr>
                                                            <td>{{ $criteriaList['criteriaName'] }}</td>
                                                            <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                                                            <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                                            <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                                            <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                                            @php
                                                                $maxValue = substr($criteriaList['good'], strpos($criteriaList['good'], "-") + 1);
                                                                $min = explode("-", $criteriaList['poor']);
                                                                $minValue = $min[0] - 1;

                                                                $rubricCriteriaArray[$c] = $rubricCriteriaDetail['rubricCriteriaId'];
                                                                $c++;
                                                            @endphp
                                                            <td style="text-align:center;">
                                                                @if ($role == "supervisor")
                                                                    <input type="number" name="s_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                    @if ($rubricCriteriaDetail['markSupervisor'] !== null )
                                                                        value="{{ $rubricCriteriaDetail['markSupervisor'] }}"
                                                                    @endif>
                                                                @endif
                                                            </td>
                                                            <td style="text-align:center;">
                                                                @if ($rubricType == "Project I")
                                                                    @if ($i == 0)
                                                                        <input type="number" name="m_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                        @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                                            value="{{ $rubricCriteriaDetail['markModerator'] }}"
                                                                        @endif>
                                                                    @endif
                                                                @elseif ($rubricType == "Project II")
                                                                    @if ($i != 3)
                                                                        <input type="number" name="m_{{ $rubricCriteriaDetail['rubricCriteriaId'] }}" class="form-control" max="{{ $maxValue }}" min="{{ $minValue }}" step=".1"
                                                                        @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                                            value="{{ $rubricCriteriaDetail['markModerator'] }}"
                                                                        @endif>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td style="text-align:center;">
                                                                @if ($role == "supervisor")
                                                                    {{ $finalMarkArray[$f] }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @php
                                                        $idCount++;
                                                        $f++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tr>
                                    @php $i++; @endphp

                                @endif
                            @endforeach
                        @endforeach

                        {{-- To get the rubricCriteriaId for storing mark, for mark input id purpose --}}
                        @php
                            Session::put('rubricCriteriaArray', $rubricCriteriaArray);
                        @endphp

                        <tr style="background-color:lightgrey;">
                            <td colspan='2'></td>
                            <td>{{ $totalMark }}</td>
                            <td colspan='6'></td>
                            <td colspan='2' style="text-align:right;"><b>Total Marks</b></td>
                            <td style="text-align:center; @if ($grandTotal < 49.5) color:red; background-color:lightpink; @endif">
                                @if ($role == "supervisor")
                                    <b>{{ $grandTotal }}</b>
                                @endif
                            </td>
                        </tr>

                        <tr style="background-color:lightgrey;">
                            <td colspan='11' style="text-align:right;">
                                <b>Grade</b>
                            </td>
                            <td style="text-align:center;">
                                @if ($role == "supervisor")
                                    <b>{{ $grade }}</b>
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>

                <table class="table table-bordered">
                    <tr>
                        <th>Comments</th>
                    </tr>

                    <tr>
                        <td>
                            @foreach ($rubricAssessDetails as $rubricAssessDetail)
                            <textarea name="comment" rows="5" cols="1" class="form-control">{{ $rubricAssessDetail['comment'] }}</textarea>
                            @endforeach
                        </td>
                    </tr>
                </table>

                <br/>

                <div class="signature">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Supervisor's Signature:</th>
                                <td>
                                    <input type="text" style="border:0; border-bottom:1px solid #000"/>
                                </td>
                                <th>Moderator's Signature:</th>
                                <td>
                                    <input type="text" style="border:0; border-bottom:1px solid #000"/>
                                </td>
                            </tr>

                            <tr>
                                <th>Date:</th>
                                <td>
                                    @foreach ($rubricAssessDetails as $rubricAssessDetail)
                                        @if ($rubricAssessDetail['dateBySupervisor'] != NULL)
                                            {{ date('d-m-Y', strtotime($rubricAssessDetail['dateBySupervisor'])) }}
                                        @endif
                                    @endforeach
                                </td>
                                <th>Date:</th>
                                <td>
                                    @foreach ($rubricAssessDetails as $rubricAssessDetail)
                                        @if ($rubricAssessDetail['dateByModerator'] != NULL)
                                            {{ date('d-m-Y', strtotime($rubricAssessDetail['dateByModerator'])) }}
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-12" style="text-align:center">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
            {{ Form::close() }}

            {{-- <br/>

            {{ Form::open(array('action' => 'RubricController@printStudentRubric')) }}
                @csrf
                <div style="text-align:center">
                    <button class="btn btn-primary" type="submit">Generate as PDF</button>
                </div>
            {{ Form::close() }} --}}
        </div>

    {{-- </div>
</div> --}}

@endsection
