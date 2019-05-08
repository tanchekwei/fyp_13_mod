@extends('layouts.app')

@section('content')
<script>
    jQuery(document).ready(function()
    {
        var exist = [];
        var removearr = [];
        
        <?php
            foreach($supresult as $sr)
            {
                    ?>
                        exist.push("<?php echo $sr->staffId?>")
                    <?php
            }
        ?>
        console.log(exist);
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
                  $('#searchtable tr').remove();
                  var test = "";
                  
                 for(var i = 0;i<result.length;i++)
                 {
                     
                        var check =0;
                        for(var j = 0; j<exist.length;j++)
                        {
                            if(exist[j] === result[i].staffId)
                            {
                                check +=1;
                            }
                        }
                        
                        if(check === 0)
                        {
                            test += "<tr><td>"+result[i].staffName+"-"+result[i].staffId+"</td><td><button id='searchadd' class='btn btn-primary'>Add</button></td></tr>";
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
        
        $('#searchtable tbody').on('click','#searchadd',function()
        {
            var currow = $(this).closest('tr');
            var result = currow.find('td:eq(0)').text();
            var result2 = currow.find('td:eq(1)').text();
            var str = result.substr(result.indexOf('-')+1);
            var makeup = "";
            var count = 0;
            
            if($('#fyptable tr').length !== 0)
            {
                $('#fyptable tr td:nth-child(1)').each(function()
                    {
                        var check = $(this).text().trim();
                        if(check === result )
                        {
                            count += 1;
                        }
                    }); 

                if(count === 0)
                {
                    makeup += "<tr><td>"+result+"</td><td><button id='remove' class='btn btn-danger'>Remove</button></td></tr>";
                    $('#fyptable tbody').append(makeup);
                    exist.push(str);
                    currow.remove();
                }
                else
                {
                    var errmsg = "this person is already in the list";
                    alert(errmsg);
                }
            }
            else
            {
                makeup += "<tr><td>"+result+"</td><td><button id='remove' class='btn btn-danger'>Remove</button></td></tr>";
                $('#fyptable tbody').append(makeup);
                exist.push(str);
                currow.remove();
            }
        });
        
        $('#fyptable').on('click','#remove',function()
        {
            var currow = $(this).closest('tr');
            var result = currow.find('td:eq(0)').text();
            var str = result.substr(result.indexOf('-')+1);
            var count = 0;
            exist.splice($.inArray(str, exist), 1);
            currow.remove();
            if(removearr.length !== 0)
            {
                for(var i = 0;i<removearr.length;i++)
                {
                    if(str === removearr[i])
                    {
                        count ++;
                    }
                }
                
                if(count === 0)
                {
                    removearr.push(str);
                }
            }
            else
            {
                removearr.push(str);                
            }
            $('#input').trigger('keyup');
            console.log(exist);
            console.log(removearr);
        });
        
        $('#savebutton').click(function()
        {
            var addarr = [];
            $('#fyptable tr td:nth-child(1)').each(function()
            {
                var str = $(this).text().trim();
                var staffid = str.substr(str.indexOf('-')+1);
                var count = 0;
                if(addarr.length !== 0)
                {
                    for(var i = 0;i<addarr.length;i++)
                    {
                        if(staffid === addarr[i])
                        {
                            count ++;
                        }
                    }

                    if(count === 0)
                    {
                        addarr.push(staffid);
                    }
                }
                else
                {
                    addarr.push(staffid);                
                }
            });
            
            console.log(addarr);
            
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
            });
            
            $.ajax({
                url:"{{url('/Addsupervisor/storesupervisor')}}",
                method:'post',
                data:{
                    removearr:removearr,
                    addarr:addarr,
                    cid:"<?php echo $cid?>"
                },
                success:function(result)
                {
                    alert(result.success);
                    removearr = [];
                },
                error:function(result)
                {
                    console.log(result.error);
                }
            });            
        });
    });
</script>
<div class='container'>
    <h1 class='h1 text-center'>Add Supervisor</h1>
</div>

<div class='container'>
    <div class='row'>
        <div class='col-sm-10 col-xs-10'>
            <p>Cohort: <?php echo $cid?></p>
        </div>
        <div class='col-sm-2 col-xs-2'>
            <input id="savebutton" type="button" class="btn btn-success" value="Save Changes">
        </div>
    </div>
    <hr>
    <div class='row'>
        <div class='col-sm-6 col-xs-12'>
            <div>
            Supervisor List:
            </div>
            <br>
            <table id="fyptable" class="table table-striped">
                <tbody>
                    @foreach($supresult as $sr)
                    <tr>
                        <td><?php echo $sr->staffName."-".$sr->staffId?></td>
                        <td><button id='remove' class='btn btn-danger'>remove</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>        
        <div class='col-sm-6 col-xs-12'>
            <div>
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
            <br>
            <table id='searchtable' class="table table-striped">
                <tbody>
                @if(Auth::user()->role !='admin')
                @foreach($staffresult as $sr)
                <tr>
                    <td><?php echo $sr->staffName."-".$sr->staffId?></td>
                    <td><button id="searchadd" class='btn btn-primary'>Add</button></td>
                </tr>
                @endforeach
                @endif
                
                @if(Auth::user()->role =='admin')
                <?php
                foreach($staffresult2 as $sr2)
                {
                        $count = 0;
                        foreach($supresult as $spr)
                        {
                            if($sr2->staffId == $spr->staffId)
                            {
                                $count+=1;
                            }
                        }
                        if($count==0)
                        {
                            ?>
                            <tr>
                                <td><?php echo $sr2->staffName."-".$sr2->staffId?></td>
                                <td><button id="searchadd" class='btn btn-primary'>Add</button></td>
                            </tr>
                            <?php
                        }                        
                    
                }
                ?>
                
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection