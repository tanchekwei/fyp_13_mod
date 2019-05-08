@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @php
            $project = \App\Project::find(request()->route('id'));
            $project_code = $project->projectCode;
            //echo $project_code;
            @endphp

            {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('createrepository', $project)}}

            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{session('failed')}}
                </div>
            @endif


            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                    {{$errors->all()[0]}}

                </div>
            @endif

            <form class="form-horizontal" method="POST" action="{{route('post_create_repo')}}">
                {{csrf_field()}}
                <fieldset>
                    <input type="hidden" name="projectcode" value="{{$project_code}}">

                    <!-- Form Name -->
                    <legend>New Repository</legend>

                    <div class="card card-default">
                        <div class="card-body">
                            <h5 class="card-title">Repository details</h5>
                            <!-- Text input-->
                            <div class="form-group">


                                <label class="col-md-4 control-label" for="textinput">Repository Name</label>
                                <div class="col-md">
                                    <input id="repositoryname" name="repositoryname" type="text"
                                           placeholder="Repository Name"
                                           class="form-control input-md">
                                </div>
                            </div>

                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="textinput">Description</label>
                                <div class="col-md">
                                    <input id="repositorydesc" name="repositorydesc" type="text"
                                           placeholder="Repository Description"
                                           class="form-control input-md">
                                </div>
                            </div>
							
							


                            <!-- Button (Double) -->
                            <div class="form-group">
                                <div class="float-right">
                                    <button id="btnCreate" name="btnCreate"
                                            class="btn btn-success">
                                        Create
                                    </button>
                                    <!--
                                    <button id="btnCancel" name="btnCancel"
                                            class="btn btn-danger col-md-8 float-xl-right">Cancel
                                    </button>-->
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
</div>
@endsection