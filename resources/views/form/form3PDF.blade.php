@php
    $form3PdfArray = Session::get('form3PdfArray');
    $staffDetails = $form3PdfArray[0];
    $role = $form3PdfArray[1];
    $cohortId = $form3PdfArray[2];
    $supervisorName = $form3PdfArray[3];
    $moderatorName = $form3PdfArray[4];
    $studentDetail = $form3PdfArray[5];
    $programmeName = $form3PdfArray[6];
    $projectDetail = $form3PdfArray[7];
    $projectRequirementLists = $form3PdfArray[8];
    $form3AssessmentDetail = $form3PdfArray[9];
    $form3RequirementDetails = $form3PdfArray[10];
    $criteriaLists = $form3PdfArray[11];
    $studentCriteriaDetails = $form3PdfArray[12];
    $finalMark = $form3PdfArray[13];
@endphp

@section('title', 'Form 3 PDF Page')
@section('module', 'Form Page')
<h2 style="text-align:center;">Form 3 - Project Proposal Moderation</h2><br />

<table cellspacing="0" cellpadding="3" border="1">
    <tr style="background-color:#DCDCDC">
        <td colspan="4"><b>1. Project Details</b></td>
    </tr>

    <tr>
        <td style="background-color:lavender">Student Name</td>
        <td>{{ $studentDetail['studentName'] }}</td>
        <td style="background-color:lavender">Programme</td>
        <td>{{ $programmeName }}</td>
    </tr>

	@if(Session::get('role2') == "supervisor")
		<tr>
			<td style="background-color:lavender">Supervisor Name</td>
			<td>{{ $supervisorName }}</td>
			<td style="background-color:lavender">Cohort</td>
			<td>{{ $cohortId }}</td>
		</tr>

		<tr>
			<td style="background-color:lavender">Moderator Name</td>
			<td colspan="3">{{ $moderatorName }}</td>
		</tr>
	@else
		<tr>
			<td style="background-color:lavender">Supervisor Name</td>
			<td>{{ $moderatorName }}</td>
			<td style="background-color:lavender">Cohort</td>
			<td>{{ $cohortId }}</td>
		</tr>

		<tr>
			<td style="background-color:lavender">Moderator Name</td>
			<td colspan="3">{{ $supervisorName }}</td>
		</tr>
	@endif	

    <tr>
        <td style="background-color:lavender">Project Title/Scope</td>
        <td colspan="3">{{ $projectDetail['title'] }}</td>
    </tr>

    <tr>
        <td style="background-color:lavender">Project Type</td>
        <td>{{ $projectDetail['cluster'] }}</td>
        <td style="background-color:lavender">Project Category</td>
        <td>{{ $projectDetail['group'] }}</td>
    </tr>
</table>

<table cellspacing="0" cellpadding="3" border="1">
    <tr style="background-color:#DCDCDC">
        <td colspan="4"><b>2. Project Scope Moderation</b></td>
    </tr>

    <tr>
        <td colspan="3" align="center" style="background-color:lavender"><b>Project Requirements</b></td>
        <td align="center" style="background-color:lavender"><b>Comply</b></td>
    </tr>

    @foreach ($form3RequirementDetails as $form3RequirementDetail)
        @foreach ($projectRequirementLists as $projectRequirementList)
            @if ($form3RequirementDetail['requirementId'] == $projectRequirementList['requirementId'])
                <tr>
                    <td colspan="3">
                        <b>{{ $projectRequirementList['requirementName'] }}</b>
                        <br/>
                        {{ $projectRequirementList['description'] }}
                    </td>
                    <td style="text-align:center;">
                        @if ($form3RequirementDetail['comply'] != NULL && $form3RequirementDetail['comply'] != "Not Complied")
                            Yes
                        @else
                            No
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
</table>

<table cellspacing="0" cellpadding="3" border="1">
    <tr style="background-color:#DCDCDC">
        <td colspan="4"><b>3. Feedback</b></td>
    </tr>

    <tr>
        <td colspan="2" align="center" style="background-color:lavender"><b>Comments and Changes Recommended (by Moderator)</b>
        </td>
        <td colspan="2" align="center" style="background-color:lavender"><b>Actions Taken (by Supervisor)</b>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            @if ($form3AssessmentDetail['feedbackComment'] != NULL){{ $form3AssessmentDetail['feedbackComment'] }}@endif 
        </td>
        <td colspan="2">
            @if ($form3AssessmentDetail['feedbackAction'] != NULL){{ $form3AssessmentDetail['feedbackAction'] }}@endif 
        </td>
    </tr>
</table>

<table cellspacing="0" cellpadding="3" border="1">
    <tr style="background-color:#DCDCDC">
        <td colspan="4"><b>4. Assessment</b></td>
    </tr>

    <tr>
        <td align="center" style="background-color:lavender"><b>Item</b></td>
        <td align="center" style="background-color:lavender"><b>Criteria</b></td>
        <td align="center" style="background-color:lavender"><b>Mark Allocation</b></td>
        <td align="center" style="background-color:lavender"><b>Mark</b></td>
    </tr>

    @foreach ($criteriaLists as $criteriaList)
        @foreach ($studentCriteriaDetails as $studentCriteriaDetail)
            @if ($criteriaList['criteriaId'] == $studentCriteriaDetail['criteriaId'])
                <tr>
                    <td>{{ $criteriaList['criteriaName'] }}</td>
                    <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                    <td style="text-align:center;">{{ substr($criteriaList['good'], strpos($criteriaList['good'], "-") + 1) }}</td>
                    <td style="text-align:center;">
                        @if ($studentCriteriaDetail['markModerator'] !== NULL)
                            {{ $studentCriteriaDetail['markModerator'] }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach

    <tr>
        <td colspan="3" style="text-align:right;"><b>Total Marks</b></td>
        <td style="text-align:center;">{{ $finalMark }}</td>
    </tr>
</table>

<br/><br/><br/><br/>

<table>
    <tr>
        <td style="text-align:left">Moderated By:</td>
        <td style="text-align:left">Received By:</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td>Moderator's Signature:</td>
        <td>Supervisor's Signature:</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td>Moderation Date:
			@if ($form3AssessmentDetail['dateByModerator'] !== NULL)
				{{ date('d-m-Y', strtotime($form3AssessmentDetail['dateByModerator'])) }}
			@endif
        </td>

        <td>Received Date:
			@if ($form3AssessmentDetail['dateBySupervisor'] !== NULL)
				{{ date('d-m-Y', strtotime($form3AssessmentDetail['dateBySupervisor'])) }}
			@endif
        </td>
    </tr>
</table>
