@php
    $markSummaryArray = Session::get('markSummaryArray');
    $type = $markSummaryArray[0];
    $cohortId = $markSummaryArray[1];
    $supervisor = $markSummaryArray[2];
    $moderator = $markSummaryArray[3];
    $artifactCLO = $markSummaryArray[4];
    $teamStudentLists = $markSummaryArray[5];
    $teamProjectArray = $markSummaryArray[6];
    $studentMarkArray = $markSummaryArray[7];
    $studentTotalMarkArray = $markSummaryArray[8];

    $totalMarkArray = array();
    $m = 0;
@endphp
@section('title', 'Mark Summary PDF')
@section('module', 'Rubric Page')
<h1 align="center">Mark Summary for BACS3403 {{ $type }}</h1>
<h2 align="center">{{ $cohortId }}</h2><br/>

{{-- <h4>Supervisor : {{ $supervisor['staffName'] }}</h4>
<h4>Moderator : {{ $moderator['staffName'] }}</h4> --}}

<table>
    <tr>
        <td>Supervisor : {{ $supervisor['title'] . " " . $supervisor['staffName'] }}</td>
    </tr>

    <tr>
        <td>Moderator : {{ $moderator['title'] . " " .  $moderator['staffName'] }}</td>
    </tr>
</table>

<br/><br/>

<table cellspacing="0" cellpadding="1" border="1">
    <tr style="background-color:#DCDCDC">
        <td rowspan="3" width="30" align="center">No.</td>
        <td rowspan="3" width="70" align="center">Programme</td>
        <td rowspan="3" width="150" align="center">Student Name</td>
        <td rowspan="3" width="80" align="center">Registration ID</td>
        <td colspan="{{ count($artifactCLO) }}" width="180" align="center">Marks by CLO</td>
        <td rowspan="2" width="50" align="center">Total Mark</td>
        <td rowspan="3" width="200" align="center">Project Title</td>
    </tr>

    <tr style="background-color:#DCDCDC">
        @foreach ($artifactCLO as $CLO)
            <td align="center">CLO {{ $CLO['CLO'] }}</td>
        @endforeach
    </tr>

    <tr style="background-color:#DCDCDC">
        @foreach ($artifactCLO as $CLO)
            <td align="center">{{ $CLO['totalMarks'] }}%</td>
            @php
                $totalMarkArray[$m] = $CLO['totalMarks'];
                $m++;
            @endphp
        @endforeach
        <td align="center">100</td>
    </tr>

    @php
        $studentNo = 0;
        $studentCount = count($studentMarkArray);
        if ($studentCount != 0) {
            $cloCount = count($studentMarkArray[0]);
        }
    @endphp

    @foreach ($teamStudentLists as $teamStudentList)
        <tr>
            <td align="center">{{ $studentNo + 1 }}</td>
            <td align="center">{{ $teamStudentList['programmeId'] }}</td>
            <td>{{ $teamStudentList['studentName'] }}</td>
            <td align="center">{{ $teamStudentList['studentId'] }}</td>
            @if ($studentNo < $studentCount)
                @php $finalMark = 0; @endphp
                @for ($clo = 0; $clo < $cloCount; $clo++)
					@php
						$cloMark = $studentMarkArray[$studentNo][$clo] / $totalMarkArray[$clo] * 100;
					@endphp
					@if (fmod($cloMark, 1) == 0)
						<td align="center"><b>{{ $cloMark }}</b></td>
					@else
						<td align="center"><b>{{ number_format($cloMark, 2, '.', ',') }}</b></td>
					@endif
                @endfor
            @endif
            <td align="center"><b>{{ $studentTotalMarkArray[$studentNo] }}</b></td>
            <td align="center">{{ $teamProjectArray[$studentNo] }}</td>
            @php $studentNo++; @endphp
        </tr>
    @endforeach

</table>

<br/><br/><br/><br/>

<table>
    <tr>
        <td>Signature :
            <input type="text" style="border:0; border-bottom:1px solid #000; text-align:left"/>
        </td>
    </tr>

    <br/>

    <tr>
        <td>Date :
            @php
                echo date("d-m-Y", strtotime('+8 hours'));
            @endphp
        </td>
    </tr>
</table>

