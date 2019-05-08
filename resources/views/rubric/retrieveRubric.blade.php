@extends('layouts.app')
@section('title', 'Retrieve Rubric')
@section('module', 'Rubric Page')
@section('content')

<div class="container">
    <div class="col-md-12" style="padding-top:25px">
        <h2 style="text-align:center;">BACS3403 {{ strtoupper($type) }} RUBRIC</h2><br />

        <table class="table table-bordered">
            <thead style="background-color:lightgrey">
                <tr>
                    <th rowspan='2' style="text-align:center;">CLO</th>
                    <th rowspan='2' style="text-align:center;">Artifact</th>
                    <th rowspan='2' style="text-align:center;">Marks</th>
                    <th rowspan='2' style="text-align:center;">Criteria</th>
                    <th rowspan='2' style="text-align:center;">Descriptors</th>
                    <th colspan='3' style="text-align:center;">Assessment Criteria</th>
                </tr>

                <tr>
                    <th style="text-align:center;">Poor</th>
                    <th style="text-align:center;">Acomplished</th>
                    <th style="text-align:center;">Good</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $artifactDetails = Session::get('artifactDetails');
                    $criteriaLists = Session::get('criteriaLists');
                    $idArray = array();
                    $a = 0;
                    $i = 0;
                @endphp

                @foreach ($artifactDetails as $artifactDetail)
                    @php
                        $count = 0;
                    @endphp

                    @foreach ($criteriaLists as $criteriaList)
                        @if ($artifactDetail['artifactId'] == $criteriaList['artifactId'])
                            @php $count++; @endphp
                        @endif
                    @endforeach

                    @php
                        $idArray[$a] = $count;
                        $a++;
                    @endphp
                @endforeach

                @foreach ($artifactDetails as $artifactDetail)
                    @php $idCount = 0; @endphp
                    <tr>
                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactDetail['CLO'] }}</td>
                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactDetail['description'] }}</td>
                        <td rowspan='{{ $idArray[$i] }}'>{{ $artifactDetail['totalMarks'] }}</td>

                        @foreach ($criteriaLists as $criteriaList)
                            @if ($artifactDetail['artifactId'] == $criteriaList['artifactId'])
                                @if ($idCount == 0)
                                    <td>{{ $criteriaList['criteriaName'] }}</td>
                                    <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                                    <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                    <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                    <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                @else
                                    <tr>
                                        <td>{{ $criteriaList['criteriaName'] }}</td>
                                        <td>{!! trans(substr(str_replace('- ', '<br />- ', $criteriaList['description']), 6)) !!}</td>
                                        <td style="text-align:center;">{{ $criteriaList['poor'] }}</td>
                                        <td style="text-align:center;">{{ $criteriaList['accomplished'] }}</td>
                                        <td style="text-align:center;">{{ $criteriaList['good'] }}</td>
                                    </tr>
                                @endif
                                @php $idCount++; @endphp
                            @endif
                        @endforeach

                    </tr>
                    @php $i++; @endphp
                @endforeach

            </tbody>

        </table>

        <form action="rubric" method="GET">
            @csrf
            <div class="col-md-12" style="text-align:center">
                <button class="btn btn-primary" type="submit">Back</button>
            </div>
        </form>

        <br/>
    </div>
</div>

@endsection
