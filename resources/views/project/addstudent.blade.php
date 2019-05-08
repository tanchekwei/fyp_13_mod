@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @php
                    $project = \App\Project::find(request()->route('id'));
                    $repo= \App\Repository::find(request()->route('rid'));
                @endphp

                {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('repoaddstudents', $project, $repo)}}

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{session('success')}}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{session('error')}}
                    </div>
                @endif

<h3>Assign student to repository</h3>
                <form action="{{route('add_students_to_repo')}}" method="POST">
                    @csrf
                    <table class="table">

                        <thead class="thead-light">
                        <tr>
                            <th>Team ID</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Assign Status</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($teams as $team)
                            <input type="hidden" name="repositoryid" value="{{request()->route('rid')}}">
                            @forelse($team->students as $student)
                                <tr>
                                    <td>{{$student->teamId}}</td>
                                    <td>{{$student->studentId}}</td>
                                    <td>{{$student->studentName}}</td>
                                    <td>
                                        <input type="hidden" name="students[]" value="{{$student->studentId}}">
                                        <input type="checkbox" name="assigned[]" value="{{$student->studentId}}" {{\App\Collaborator::where('studentId', $student->studentId)->where('repository_id', request()->route('rid'))->get()->isEmpty()? '':'checked'}}/>
                                    </td>
                                </tr>
                                @empty

                                @endforelse
                            @empty

                        @endforelse

                        </tbody>

                    </table>
                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                </form>

            </div>
        </div>
    </div>
@endsection