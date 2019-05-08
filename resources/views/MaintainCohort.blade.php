@extends('layouts.app')

@section('content')    
    <script>
        var deleteid;
        
        function doHide(){
		document.getElementById( "hide" ).style.display = "none" ;
	}

	function hideImage(){
		//  5000 = 5 seconds
		setTimeout( "doHide()", 5000 ) ;
	}
        
        function jumppage(z)
        {
            var url = "{{action('CohortController@showmenu',":z")}}";
            url = url.replace(':z',z);
            window.location.href = url;
        }
        
        function search()
        {
            var y = document.getElementById('dropdown').value;
            var url = "{{action('CohortController@orderby',":slug")}}";
            url = url.replace(':slug', y);
            window.location.href= url;
        }
        
        function yesdelete()
        {
            var url = "{{action('CohortController@destroy',":z")}}";
            url = url.replace(':z',deleteid);
            window.location.href = url;
        }
        
        jQuery(document).ready(function()
        {
            $('#table').on('click','#deleteid',function()
            {
               var currow = $(this).closest('tr');
               var result = currow.find('td:eq(0)').text();
               $('#cohortIdlbl').text(result);
               $('#cohortId').val(result);
               $('#deletemodal').modal(); 
            });            
        });
    </script>
    
    <div class="container">
        <h1 class="h1 text-center">Cohort</h1>
        @if (\Session::has('success'))
        <div id="hide" class="alert alert-success">
            <p class="text-center">{{ \Session::get('success') }} <a class="btn btn-primary" onclick="doHide()">OK</a></p>
        </div>
        @endif
        @if (\Session::has('error'))
        <div id="hide" class="alert alert-danger">
            <p class="text-center">{{ \Session::get('error') }} <a class="btn btn-primary" onclick="doHide()">OK</a></p>
        </div>
        @endif
        Sortby: 
        <select id ="dropdown" class="dropdown" onchange="search()">
            <option>-------</option>
            <option class="dropdown-item dropdown-menu" value="cohortId">Cohort</option>
            <option class="dropdown-item dropdown-menu" value="project1startingDate">Project 1 Start Date</option>
            <option class="dropdown-item dropdown-menu" value="project2startingDate">Project 2 Start Date</option>
        </select>
        @if(Auth::user()->role == 'admin')
        <a href="{{url('/crcohort')}}" class="btn btn-primary">Add New Cohort</a>
        @endif
    </div>
    <div class="container">
        <strong>Note:</strong> double click to select Cohort.
    </div>
    <br>    
    <div class="container">
        <table id="table" class="table table-striped table-hover">
            <thead>
                <tr>
                    <td>Cohort</td>
                    <td>Project 1 Starting Date</td>
                    <td>Project 1 End Date</td>
                    <td>Project 2 Starting Date</td>
                    <td>Project 2 End Date</td>
                    @if(Auth::user()->role == 'admin')
                    <td>Action</td>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($result as $c)
                <?php $cid = $c['cohortId'];?>
                <tr ondblclick="jumppage('<?php echo $cid; ?>')">
                    <td>{{$c['cohortId']}}</td>
                    <td>{{date('d/m/Y',strtotime($c['project1startingDate']))}}</td>
                    <td>{{date('d/m/Y',strtotime($c['project1endDate']))}}</td>
                    <td>{{date('d/m/Y',strtotime($c['project2startingDate']))}}</td>
                    <td>{{date('d/m/Y',strtotime($c['project2endDate']))}}</td>
                    @if(Auth::user()->role == 'admin')
                    <td>
                        <a href="{{action('CohortController@edit',$c['cohortId'])}}" class="btn btn-warning">Edit</a>
                        &nbsp;&nbsp;
                        <button id="deleteid" type="button" name="deleteid" class="btn btn-danger">Delete</button>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- The Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="deletemodal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route('deletecohort')}}" method='post'>
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Cohort</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                  
                    </div>
                    <div class="modal-body">
                        <p>Do you want to delete <label id='cohortIdlbl'></label> cohort?</p>
                        <input type='hidden' id='cohortId' name="deleteid">
                    </div>
                    <div class='modal-footer'>
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection