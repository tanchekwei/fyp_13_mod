@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">





                <form action="{{route('assign_team')}}" method="POST">
                @csrf
                    <div>
                        <table class="table">
                            <thead class="thead-light">

                            <tr>
                                <th>Team ID</th>
                                <th>Team Members</th>
                                <th>Project Assigned</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- i hate u html -->

                            @forelse($teams as $team)
                                <tr>
                                    <input type="hidden" value="{{$team->id}}" name="teamid[]">
                                    <td>{{$team->id}}</td>
                                    <td>
                                        <table class="table table-sm table-borderless">
                                            @forelse($team->students as $students)

                                                <tr><td>{{$students->studentName}}</td>
                                                <td>{{$students->studentId}}</td></tr>

                                            @empty
                                                There seem to be no data.
                                            @endforelse
                                        </table></td>

                                    <td>

                                       <div class="form-group">
                                            <select class="form-control-sm" name="projectcode[]">
                                                @if(!in_array($team->projectCode, $project_codes))
                                                    <option>{{$team->projectCode}}</option>
                                                    @else
                                                    <option>{{$team->projectCode}}</option>
                                                    @endif

                                                @forelse($project_codes as $proj)
                                                    @if(!($proj == $team->projectCode))
                                                            <option>{{$proj}}</option>
                                                        @endif

                                                    @empty
                                                    There seem to be no data.
                                                    @endforelse

                                            </select>
                                        </div> 

                                    </td>
                                </tr>
                            @empty
                                There seems to be no data.
                            @endforelse


                            </tbody>

                        </table>
                    </div>


                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>


            </div>
        </div>
    </div>
@endsection