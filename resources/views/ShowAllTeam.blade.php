@extends('layouts.app')
@section('content')
    
    <script src="http://code.jquery.com/jquery-3.3.1.min.js"
               integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
               crossorigin="anonymous"></script>
               <script>
                   jQuery(document).ready(function(){
        jQuery('#btnShow').on('click',function(e){
           e.preventDefault();
           $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          jQuery.ajax({
              url: "{{ url('/showTable') }}",
              method: 'post',
              data: {
                  
              },
              success: function(result){
                  $('#showTable tr').remove();
                      var test = "";
                      test+= "<thead><tr><td>Team ID</td><td>Status</td><td>Projet Code</td><td>Supervisor</td></tr></thead><tbody>"
                      for(var i=0;i<result.length;i++)
                      {
                      test += "<tr><td>"+result[i].teamId+"</td><td>"+result[i].status+"</td><td>"+result[i].projectCode+"</td><td>"+result[i].supervisor+"</td></tr>"
                }
                test+= "</tbody>"
                   $('#showTable').append(test);
               
                console.log(result);
              },
                  
              error: function(result){
                console.log('fail');
                console.log(result);
            }
                   });
                   });
//auto assign
                   $('#btnSubmit').click(function(e)
                {
                    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
                $.ajax({
                    url: "{{ url('/autoAssign') }}",
              method: 'get',
              data: {
              },
              success: function(result){
                  var word = "Auto assign supervisor to team successfully";
                        $('#message').text(word);
                            console.log(result);
              },
                      

              error: function(result){
                console.log(result);
              }});
            });
        
        });
               </script>
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
@endif
<div class="container">
    <table class="table table-striped" border="2">
        <h3 align="center">Show All Team</h3><br />
        <tr>
            <td><input type="button" id="btnSubmit" value="Auto Assigned"></td>
            <td><input type="button" id="btnShow" value="Show All Team"></td>
            <tr>
            </table>
    <a id='message'></a>
            <table id="showTable" class="table table-striped">
            </table>
            </div>
@endsection