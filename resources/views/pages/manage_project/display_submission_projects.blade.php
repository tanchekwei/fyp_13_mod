@extends('layouts.app')
@section('content')
    <h4>Project Submission Status 
	@if($cohortId!=null)
		(Cohort {{$cohortId}})
	@else
		<font class='text-danger'>(Cohort not selected)</font>
	@endif
	</h4>
    <br>
	<div class="text-info"><b>Note: </b>Only projects which have been assigned to a team will appear below.</div>
    @php
    $count = 0;
    @endphp
    @if(sizeof($projects)>0)
            <table class="table table-hover">
                <thead>
                <tr class="row">
					<td class="col"><b>Team ID</b></td>
                    <td class="col"><b>Project ID</b></td>
                    <td class="col"><b>Project Title</b></td>
                    <td class="col"><b>Submission Type</b></td>
                    <td class="col"><b>Submission Status</b></td>
                    <td class="col"><b>Action</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($projects as $project)
                    <!--If project's isCompetition is set to 1, display both competition and normal submission-->
                    @if($project->isCompetition == '1')
                        <tr class="row">
							<td class="col">{{$project->teamID}}</td>
                            <td class="col">{{$project->projectCode}}</td>
                            <td class="col">{{$project->title}}</td>
                            <td class="col">Competition</td>
                            <td class="col">{{$submission_status[$count]}}</td>
                            <td class="col"><a href="{{route('displaysubmission.show', ['projectCode'=>$project->projectCode, 'teamID'=>$project->teamID, 'isCompetition'=>'1'])}}" class="btn btn-success" type="button" value="{{$project->teamID}}|{{$project->isCompetition}}">View</a></td>
                        </tr>
                        @php
                            $count +=1;
                        @endphp
                        <tr class="row">
							<td class="col">{{$project->teamID}}</td>
                            <td class="col">{{$project->projectCode}}</td>
                            <td class="col">{{$project->title}}</td>
                            <td class="col">Normal</td>
                            <td class="col">{{$submission_status[$count]}}</td>
                            <td class="col"><a href="{{route('displaysubmission.show', ['projectCode'=>$project->projectCode, 'teamID'=>$project->teamID, 'isCompetition'=>'0'])}}" class="btn btn-success" type="button" value="{{$project->teamID}}|{{$project->isCompetition}}">View</a></td>
                        </tr>
                        <!--Display only normal submission-->
                    @else
                    <tr class="row">
						<td class="col">{{$project->teamID}}</td>
                        <td class="col">{{$project->projectCode}}</td>
                        <td class="col">{{$project->title}}</td>
                        <td class="col">Normal</td>
                        <td class="col">{{$submission_status[$count]}}</td>
                        <td class="col"><a href="/displaysubmission/show/{{$project->projectCode}}/{{$project->teamID}}/{{$project->isCompetition}}" class="btn btn-success" type="button" value="{{$project->teamID}}|{{$project->isCompetition}}">View</a></td>
                    </tr>
                    @endif
                    @php
                        $count++;
                    @endphp
                @endforeach
                </tbody>
            </table>
    @else
        No project found.
    @endif

@endsection