@php
    $studentRubricArray = Session::get('studentRubricArray');
    $idArray = $studentRubricArray[0];
    $staffDetails = $studentRubricArray[1];
    $role = $studentRubricArray[2];
    $rubricType = $studentRubricArray[3];
    $supervisorName = $studentRubricArray[4];
    $moderatorName = $studentRubricArray[5];
    $studentDetail = $studentRubricArray[6];
    $programmeName = $studentRubricArray[7];
    $projectTitle = $studentRubricArray[8];
    $rubricAssessDetails = $studentRubricArray[9];
    $rubricArtifactDetails = $studentRubricArray[10];
    $rubricCriteriaDetails = $studentRubricArray[11];
    $artifactLists = $studentRubricArray[12];
    $criteriaLists = $studentRubricArray[13];
    $finalMarkArray = $studentRubricArray[14];
    $subtotalCLOArray = $studentRubricArray[15];
    $grandTotal = $studentRubricArray[16];
    $grade = $studentRubricArray[17];

    Session::forget('studentRubricArray');

    if ($rubricType == "Project I") {
        $title = "BACS3403 PROJECT I ASSESSMENT";
    } else {
        $title = "BACS3413 PROJECT II ASSESSMENT";
    }
@endphp

@section('title', 'Student Rubric PDF')
@section('module', 'Rubric Page')

<h2 style="text-align:center;">{{ $title }}</h2><br/>

<table cellspacing="0" cellpadding="2" border="1">
    <thead>
        <tr>
            <td><b>Supervisor's Name</b></td>
            <td> {{ $supervisorName }}</td>
            <td><b>Moderator's Name</b></td>
            <td> {{ $moderatorName }}</td>
        </tr>

        <tr>
            <td><b>Student's Name</b></td>
            <td> {{ $studentDetail['studentName'] }}</td>
            <td><b>Project Title</b></td>
            <td> {{ $projectTitle }}</td>
        </tr>

        <tr>
            <td><b>Registration ID</b></td>
            <td>{{ $studentDetail['studentId'] }}</td>
            <td><b>Individual Title</b></td>
            <td>{{ $studentDetail['individualTitle'] }}</td>
        </tr>

        <tr>
            <td><b>Programme</b></td>
            <td colspan="3">{{ $programmeName }}</td>
        </tr>
    </thead>
</table>

<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td rowspan="2">CLO</td>
        <td rowspan="2">Artifact</td>
        <td rowspan="2">Marks</td>
        <td rowspan="2">Criteria</td>
        <td rowspan="2">Descriptors</td>
        <td colspan="3">Assessment Criteria</td>
        <td rowspan="2" >Mark by Supervisor</td>
        <td rowspan="2">Mark by Moderator</td>
        <td rowspan="2">Final Mark</td>
        <td rowspan="2">Mark Subtotal by CLO</td>
    </tr>

    <tr>
        <td style="text-align:center;">Poor</td>
        <td style="text-align:center;">Accomplished</td>
        <td style="text-align:center;">Good</td>
    </tr>

    @php
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
                    <td rowspan="{{ $idArray[$i] }}">{{ $artifactList['CLO'] }}</td>
                    <td rowspan="{{ $idArray[$i] }}">{{ $artifactList['description'] }}</td>
                    <td rowspan="{{ $idArray[$i] }}">{{ $artifactList['totalMarks'] }}</td>
                    @php
                        $totalMark += $artifactList['totalMarks'];
                    @endphp

                    {{-- @foreach ($rubricCriteriaDetails as $rubricCriteriaDetail)
                        @foreach ($criteriaLists as $criteriaList)
                            @if ($rubricArtifactDetail['rubricArtifactId'] == $rubricCriteriaDetail['rubricArtifactId'] && $rubricCriteriaDetail['criteriaId'] == $criteriaList['criteriaId'])
                                @if ($idCount == 0)
                                    <td>{{ $criteriaList['criteriaName'] }}</td>
                                    <td>{!! trans(substr(str_replace('- ', '<br/>- ', $criteriaList['description']), 6)) !!}</td>
                                    <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                    <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                    <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                    <td style="text-align:center;">
                                        @if ($role == "supervisor")
                                            @if ($rubricCriteriaDetail['markSupervisor'] !== null )
                                                {{ $rubricCriteriaDetail['markSupervisor'] }}
                                            @endif
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($rubricType == "Project I")
                                            @if ($i == 0)
                                                @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                    {{ $rubricCriteriaDetail['markModerator'] }}
                                                @endif
                                            @endif
                                        @elseif ($rubricType == "Project II")
                                            @if ($i != 3)
                                                @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                    {{ $rubricCriteriaDetail['markModerator'] }}
                                                @endif>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($role == "supervisor")
                                            {{ $finalMarkArray[$f] }}
                                        @endif
                                    </td>
                                    <td rowspan="{{ $idArray[$i] }}" style="text-align:center;">
                                        @if ($role == "supervisor")
                                            <b>{{ $subtotalCLOArray[$i] }}</b>
                                        @endif
                                    </td>
                                @else
                                    <tr>
                                        <td>{{ $criteriaList['criteriaName'] }}</td>
                                        <td>{!! trans(substr(str_replace('- ', '<br/>- ', $criteriaList['description']), 6)) !!}</td>
                                        <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                        <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                        <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                        <td style="text-align:center;">
                                            @if ($role == "supervisor")
                                                @if ($rubricCriteriaDetail['markSupervisor'] !== null )
                                                    {{ $rubricCriteriaDetail['markSupervisor'] }}
                                                @endif
                                            @endif
                                        </td>
                                        <td style="text-align:center;">
                                            @if ($rubricType == "Project I")
                                                @if ($i == 0)
                                                    @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                        {{ $rubricCriteriaDetail['markModerator'] }}
                                                    @endif
                                                @endif
                                            @elseif ($rubricType == "Project II")
                                                @if ($i != 3)
                                                    @if ($rubricCriteriaDetail['markModerator'] !== null )
                                                        {{ $rubricCriteriaDetail['markModerator'] }}
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
                    @endforeach --}}

                </tr>
                @php $i++; @endphp

            @endif
        @endforeach
    @endforeach

    <tr style="background-color:lightgrey;">
        <td colspan="2"></td>
        <td>{{ $totalMark }}</td>
        <td colspan="6"></td>
        <td colspan="2" style="text-align:right;"><b>Total Marks</b></td>
        <td style="text-align:center; @if ($grandTotal < 49.5) color:red; background-color:lightpink; @endif">
            @if ($role == "supervisor")
                <b>{{ $grandTotal }}</b>
            @endif
        </td>
    </tr>

    <tr style="background-color:lightgrey;">
        <td colspan="11" style="text-align:right;">
            <b>Grade</b>
        </td>
        <td style="text-align:center;">
            @if ($role == "supervisor")
                <b>{{ $grade }}</b>
            @endif
        </td>
    </tr>

</table>

<table>
    <tr>
        <th>Comments</th>
    </tr>

    <tr>
        <td>
            @foreach ($rubricAssessDetails as $rubricAssessDetail)
            {{ $rubricAssessDetail['comment'] }}
            @endforeach
        </td>
    </tr>
</table>

<table>
    <tr>
        <td>Supervisor's Signature:</td>
        <td>
            <input type="text" style="border:0; border-bottom:1px solid #000"/>
        </td>
        <td>Moderator's Signature:</td>
        <td>
            <input type="text" style="border:0; border-bottom:1px solid #000"/>
        </td>
    </tr>

    <tr>
        <td>Date:</td>
        <td>
            @foreach ($rubricAssessDetails as $rubricAssessDetail)
                @if ($rubricAssessDetail['dateBySupervisor'] != NULL)
                    {{ date('d-m-Y', strtotime($rubricAssessDetail['dateBySupervisor'])) }}
                @endif
            @endforeach
        </td>
        <td>Date:</td>
        <td>
            @foreach ($rubricAssessDetails as $rubricAssessDetail)
                @if ($rubricAssessDetail['dateByModerator'] != NULL)
                    {{ date('d-m-Y', strtotime($rubricAssessDetail['dateByModerator'])) }}
                @endif
            @endforeach
        </td>
    </tr>
</table>



