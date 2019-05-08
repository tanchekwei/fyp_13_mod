@extends('layouts.app')

@section('content')
<script>
    jQuery(document).ready(function()
    {
        jQuery('#input').keyup(function(e)
        {
           e.preventDefault();

           $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });

           jQuery.ajax({
              url: "{{ url('/addadminpage/search_name_faculty') }}",
              method: 'post',
              data: {
                 name: jQuery('#input').val(),
                 facultyId: $('#facultyinput').val()
              },
              success: function(result){
                  $('#searchtable tbody tr').remove();
                  var test = "";
                  
                 for(var i = 0;i<result.length;i++)
                 {
                    if(result[i].role !== 'admin' && result[i].staffId !== "{{Session::get('staffId')}}")
                    {
                        if(result[i].status ==='active')
                        {
                            test += "<tr><td>"+result[i].staffId+"</td><td>"+result[i].staffName+"</td><td><button id='deactivate' class='btn btn-danger'>Deactivate</button></td></tr>";     
                        }
                        else
                        {
                            test += "<tr><td>"+result[i].staffId+"</td><td>"+result[i].staffName+"</td><td><button id='activate' class='btn btn-success'>Activate</button></td></tr>";     
                        }
                    }
                 }                 
                 $('#searchtable tbody').append(test);
              },
              error: function(result){
                  console.log(result);
              }
          });
        });
        
        $('#facultyinput').change(function()
        {
            $('#input').trigger('keyup');
        });
        
        $('#searchtable tbody').on('click','#deactivate',function()
        {
            var currow = $(this).closest('tr');
            var resultid = currow.find('td:eq(0)').text();
            var result = currow.find('td:eq(1)').text();
            $('#staffName').text(result);
            $('#staffId').val(resultid);
            $('#deactivatebtn').modal();
        });
        
        $('#searchtable tbody').on('click','#activate',function()
        {
            var currow = $(this).closest('tr');
            var resultid = currow.find('td:eq(0)').text();
            var result = currow.find('td:eq(1)').text();
            $('#staffName2').text(result);
            $('#staffId2').val(resultid);
            $('#activatebtn').modal();
        });
        
        $('#modal-save').click(function()
        {
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });
            
            $.ajax({
               url:"{{url('/deletestaff')}}",
               method:"post",
               data:{
                 staffId:$('#staffId').val() 
               },
               success:function(result)
               {
                   location.reload();
               },
               error:function(result)
               {
                   console.log(result);
               }
            });
        });
        
        $('#modal-save2').click(function()
        {
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });
            
            $.ajax({
               url:"{{url('/activatestaff')}}",
               method:"post",
               data:{
                 staffId:$('#staffId2').val() 
               },
               success:function(result)
               {
                   location.reload();
               },
               error:function(result)
               {
                   console.log(result);
               }
            });
        });
    });
</script>
<div class="container">
    <h1  class="h1 text-center">Update Staff Status</h1>
    <div class='row'>
        <div class='col-sm-10 col-xs-10'>
            Faculty: 
            <select id='facultyinput'>
                @if(Auth::user()->role != 'admin')
                @foreach($stafffaculty as $sf)
                <option value="{{$sf->facultyId}}">{{$sf->facultyId}}</option>
                @endforeach
                @endif
                @if(Auth::user()->role == 'admin')
                <option value="">----</option>
                @foreach($facultyresult as $fr) 
                <option value="{{$fr->facultyId}}">{{$fr->facultyId}}</option>
                @endforeach
                @endif
            </select> &nbsp;
            Name: <input id='input' type="text">
        </div>
    </div>
</div>
<br>
<div class="container">
    <table id='searchtable' class="table table-striped">
        <thead>
            <tr>
                <td>Staff Id</td>
                <td>Staff Name</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach($staffresult as $sr)
            @if($sr->role != 'admin' && $sr->staffId != "{{Session::get('staffId')}}")
            <tr>
                <td><?php echo $sr->staffId?></td>
                <td><?php echo $sr->staffName?></td>
                @if($sr->status == 'deactive')
                <td><button id="activate" class='btn btn-success'>Activate</button></td>
                @else
                <td><button id="deactivate" class='btn btn-danger'>Deactivate</button></td>
                @endif
            </tr>
            @endif
            @endforeach    
        </tbody>
    </table>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="deactivatebtn">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Deactivate Staff</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                  
            </div>
            <div class="modal-body">
                <p>Do you want to deactivate <label id='staffName'></label> account?</p>
                <input type='hidden' id='staffId'>
            </div>
            <div class='modal-footer'>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="modal-save">Yes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="activatebtn">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activate Staff</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                  
            </div>
            <div class="modal-body">
                <p>Do you want to activate <label id='staffName2'></label> account?</p>
                <input type='hidden' id='staffId2'>
            </div>
            <div class='modal-footer'>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="modal-save2">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection