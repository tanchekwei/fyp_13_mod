@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @php
                    $repo =\App\Repository::find(request()->route('id'));
                    $url = explode('://', url('/'));
                    $url2 = explode(':', $url[1]);
                    //$repo_url = 'git@'.$url2[0].':'.$repo->name;
					$repo_url = 'git@i2hub.tarc.edu.my:'.$repo->name;
                @endphp




                {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('viewrepository', $repo, $repo->project()->first())}}

                <h3>{{$repo->projectCode}}/{{$repo->name}}</h3>
                <h5>{{$repo->description}}</h5>
                <h5>{{$repo->staff->staffName}}</h5>


                <div class="card text-center">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills">
                            <li class="nav-item">
                                <a class="nav-link active show" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade active show" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="row no-gutters">
                                <div class="col-md-12">
                                    <div class="btn-group float-left mb-3 mt-3 ml-3">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            branch: {{request()->route('br')}}
                                        </button>
                                        <div class="dropdown-menu">

                                            @forelse($all_branch_local as $lb)
                                                <a class="dropdown-item" href="{{route('project_display_repository', ['id'=>request()->route('id'), 'br'=>$lb->getName()])}}">{{$lb->getName()}}</a>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="input-group col-4 float-right mt-3 mb-3 mr-3">
                                        <input id="copyurl" type="text" class="form-control" placeholder="link"
                                               value="{{$repo_url}}">
                                        <div class="input-group-append">
                                            <button onclick="copyFunction()" class="btn btn-outline-secondary" type="button">
                                                Copy</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="col-md-12">
									<div id="app"> 
									
									<displayrepofolder :repository='@json($arr)'></displayrepofolder>
									</div>
									
                                    
									
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="row no-gutters mt-3 mb-3">
                                <div class="col-md-6 offset-md-8">
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
								  Add tag
								</button>
								</div>
								
								<div class="col-md-6 offset-md-3">
									<!-- Button trigger modal -->

                                    <h4>Latest News</h4>
                                    <ul class="timeline">
                                        @forelse($returnTags as $t)
                                            <li>
                                                <a href="#">{{$t[2]}}</a>
                                                <a href="#" class="float-right">{{date("d-M-Y", strtotime($t[1]))}}</a>
                                                <p>{{$t[0]}}</p>
                                            </li>
                                        @empty
                                        @endforelse

                                    </ul>
                                </div>
                            </div>
                        </div>
						
						<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Please enter your tag description</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
        <form id="tags" action="{{route('addtags')}}" method="POST" class="form-group">
		{{csrf_field()}}
		<input type="hidden" name="reponame" value="{{$repo->name}}">
			Version: <input type="text" name="tagversion" class="form-control"></br>
			Description: <input type="text" name="tagdesc" class="form-control"></br>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			<button onclick="submit_tags()" type="button" class="btn btn-primary"  >Save changes</button>
		</form>
	  
      </div>
    </div>
  </div>
</div>

                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="row no-gutters">
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                        @php
                                            $contacts = \App\Collaborator::where('repository_id', request()->route('id'))->latest()->get();
                                            $creator = $repo->staff()->first();
                                            //dd($creator);
                                        @endphp

                                        <tr>
                                            <td>
                                                <span class="badge badge-info">Creator</span>
                                                {{$creator->staffName}}
                                            </td>
                                            <td>{{$creator->email}}</td>
                                        </tr>

                                        @forelse($contacts as $c)
                                            @foreach($c->student as $s)
                                                <tr>
                                                    <td>{{$s->studentName}} </td>
                                                    <td>{{$s->TARCemail}}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                        @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="{{asset('public/js/app.js')}}" type="text/javascript"></script>
<script>
function copyFunction() {
    var copyText = document.getElementById("copyurl");
    copyText.select();
    document.execCommand("copy");

    alert('Copied: ' + copyText.value);
}

function submit_tags(){
	document.getElementById("tags").submit();
}

</script>

@endsection
