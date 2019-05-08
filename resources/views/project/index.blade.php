@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">


            @{{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('projecthome')}}


            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{session('error')}}
                </div>
            @endif


            <table class="table">
                <thead>
                <tr>
                    <th>Project Code</th>
                    <th>Project Title</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

               <!-- <a href="{{route('form_assign_team')}}">
                    <button type="button" class="btn btn-default" title="Assign a team">
                        <i class="fa fa-users" aria-hidden="true"></i>
                    </button>
                </a>-->
                @forelse($projects as $project)
                    <tr>
                        <td>{{$project->project_code}}</td>
                        <td>{{$project->title}}</td>
                        <td>{{str_limit($project->description, 50, '...')}}</td>
                        <td>
                            <div class="btn-toolbar">

                                <a href="{{route('project_repository', $project->project_code)}}">
                                    <button type="button" class="btn btn-default mr-1" title="View repositories">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </a>

                                <a href="#">
                                    <button type="button" class="btn btn-danger" title="Delete project">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    This staff has no project under them.
                @endforelse


                </tbody>
            </table>
        </div>
    </div>
</div>

    @endsection