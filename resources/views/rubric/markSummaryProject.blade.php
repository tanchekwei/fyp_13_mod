@extends('layouts.app')
@section('title', 'Mark Summary Project')
@section('module', 'Rubric Page')
@section('content')

@php
    $totalMarkArray = array();
    $m = 0;

    if ($type == "Project I") {
        $title = "BACS3403 Project I";
    } else {
        $title = "BACS3413 Project II";
    }
@endphp

<div class="container">
    <div class="col-md-15" style="padding-top:25px">
        <h2 style="text-align:center;">Mark Summary for {{ $title }}</h2>
        <h3 style="text-align: center;">{{ $cohortId }}</h3><br/>

        <div class="signature">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td style="width:8em; text-align:center">Supervisor :</td>
                        <td style="width:73em; text-align:left">{{ $supervisor['title'] . " " . $supervisor['staffName'] }}</td>
                    </tr>

                    <tr>
                        <td style="width:8em; text-align:center">Moderator :</td>
                        <td style="width:73em; text-align:left">{{ $moderator['title'] . " " . $moderator['staffName'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="markSummary">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan='3'>No</th>
                        <th rowspan='3'>Programme</th>
                        <th rowspan='3'>Student Name</th>
                        <th rowspan='3'>Registration ID</th>
                        <th colspan='{{ count($artifactCLO) }}'>Marks by CLO</th>
                        <th rowspan='2'>Total Mark</th>
                        <th rowspan='3'>Project Title</th>
                    </tr>

                    <tr>
                        @foreach ($artifactCLO as $CLO)
                            <th>CLO {{ $CLO['CLO'] }}</th>
                        @endforeach
                    </tr>

                    <tr>
                        @foreach ($artifactCLO as $CLO)
                            <th>{{ $CLO['totalMarks'] }}%</th>
                            @php
                                $totalMarkArray[$m] = $CLO['totalMarks'];
                                $m++;
                            @endphp
                        @endforeach
                        <th>100</th>
                    </tr>
                </thead>

                @php
                    $studentNo = 0;
                    $studentCount = count($studentMarkArray);
                    if ($studentCount != 0) {
                        $cloCount = count($studentMarkArray[0]);
                    }
                @endphp

                <tbody>
                    @foreach ($teamStudentLists as $teamStudentList)
                        <tr>
                            <td>{{ $studentNo + 1 }}</td>
                            <td>{{ $teamStudentList['programmeId'] }}</td>
                            <td style="text-align:left">{{ $teamStudentList['studentName'] }}</td>
                            <td>{{ $teamStudentList['studentId'] }}</td>
                            @if ($studentNo < $studentCount)
                                @php $finalMark = 0; @endphp
                                @for ($clo = 0; $clo < $cloCount; $clo++)
									@php
										$cloMark = $studentMarkArray[$studentNo][$clo] / $totalMarkArray[$clo] * 100;
									@endphp
									@if (fmod($cloMark, 1) == 0)
										<td><b>{{ $cloMark }}</b></td>
									@else
										<td><b>{{ number_format($cloMark, 2, '.', ',') }}</b></td>
									@endif
                                @endfor
                            @endif
                            <td><b>{{ $studentTotalMarkArray[$studentNo] }}</b></td>
                            <td>{{ $teamProjectArray[$studentNo] }}</td>
                            @php $studentNo++; @endphp
                        </tr>
                    @endforeach
                </tbody>

            </table><br/><br/>

        </div>

        <div class="signature">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Signature:
                            <input type="text" style="border:0; border-bottom:1px solid #000; text-align:left"/>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Date:
                            @php
                                echo date("d-m-Y", strtotime('+8 hours'));
                            @endphp
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form action="printMarkSummary" method="post">
            @csrf
            <div class="col-md-12" style="text-align:center">
				<button name="generatePDF" class="btn btn-primary" type="submit" value="generatePDF">Generate as PDF</button>
            </div>
        </form>

    </div>
</div>

@endsection
