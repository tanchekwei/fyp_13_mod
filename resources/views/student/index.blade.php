@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('studenthome')}}

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{session('error')}}
                    </div>
                @endif

                @forelse($all_student_repo as $asr)
                    <div class="card mb-3">
                        <h5 class="card-header">{{$asr->name}}
                            @php
                                $student_id = Auth::guard('student')->user()->studentId;
                                $collab = \App\Collaborator::where('studentId',$student_id)->where('repository_id', $asr->id)->get();
                            @endphp

                            @if($collab->isEmpty())
                                <span class="badge badge-pill badge-danger float-right">View only</span>
                                @else
                                <span class="badge badge-pill badge-success float-right">Write Access</span>
                            @endif

                        </h5>
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <p class="card-text">{{$asr->description}}</p>
                            <div class="form-inline float-right">
                                
                                <a href="{{route('student_display_repository', ['id'=> $asr->id, 'br'=>'master'])}}" class="btn btn-primary float-right">Go to repository</a>
                            </div>

                        </div>
                    </div>
                    @empty
                        nothing to show
                    @endforelse


            </div>
        </div>
    </div>

@endsection