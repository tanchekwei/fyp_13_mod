@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @php
                $project = \App\Project::find(request()->route('id'));
                @endphp

                {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('projectrepository', $project)}}


                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> {{session('success')}}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{session('error')}}
                    </div>
                @endif

                <legend>{{$project->title}}</legend>
                <a href="{{route('form_create_repository', request()->route('id'))}}">
                    <button style="width:250px" class="btn btn-primary mb-3 float-right">Create Repository</button>
                </a>

                <table class="table table-bordered">
                    <thead class="thead-light">
                    <tr class="text-center">
                        <th>Repository Name</th>
                        <th>Description</th>
                        <th># of Students Assigned</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($repository as $r)
                        <tr>
                        <td class="text-center">{{$r->name}}</td>
                        <td> {{$r->description}}</td>

                        <td class="text-center">
                            <!-- count number of students that is assigned to repository -->
                            {{\App\Collaborator::where('repository_id',$r->id)->count()}}

                        </td>

                        <td>
                            <div class="btn-toolbar" style="width: 360px;">
                              <!--  <a href="#">
                                    <button type="button" class="btn btn-info mr-1">
                                        <i class="fa fa-pencil"></i> Edit</button>
                                </a> -->
                                <a href="{{route('form_add_students_to_repo',['id'=>request()->route('id'),'rid'=>$r->id])}}">
                                    <button type="button" class="btn btn-success mr-1">
                                        <i class="fa fa-plus-square"></i>
                                        Add students
                                    </button>
                                </a>
                                <!-- <a href="#">
                                    <button type="button" class="btn btn-danger mr-1">
                                        <i class="fa fa-ban"></i>
                                        Remove
                                    </button>
                                </a> -->
                                <a href="{{route('project_display_repository', ['id'=>$r->id, 'br'=>'master'])}}">
                                    <button type="button" class="btn btn-info" title="Go to repository">
                                        <i class="fa fa-arrow-right"></i> Repository
                                    </button>
                                </a>
                                

                            </div>
                        </td>
                        </tr>
                    @empty

                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection