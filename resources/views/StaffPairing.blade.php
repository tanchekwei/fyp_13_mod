@extends('layouts.app')

@section('content')
        <script>
            jQuery(document).ready(function()
            {
                var arr = [];
                <?php
                    foreach($supresult as $spr)
                    {
                        $str = $spr->staffName."-".$spr->staffId;
                        ?>
                                arr.push("<?php echo $str?>");
                                console.log("<?php echo $str?>");
                        <?php
                    }                    
                ?>
                        console.log(arr);
                $('select').on('change', function() {
                  alert( this.value );
                });

                $('#displaytable tbody').on('focus','#staffinput',function()
                {
                   var currow = $(this).closest('tr');
                   var str = currow.find('td:eq(0)').text();
                   var staffname = str.substr(0,str.indexOf('-'));
                   var makeup = "";
                   $('#staffname option').remove();
                   for(var i=0; i<arr.length;i++)
                   {
                       if(arr[i]!== str)
                       {
                           makeup += "<option value='"+arr[i]+"'></option>";
                       }
                   }
                   $('#staffname').append(makeup);
                });

                $('#savebutton').click(function(e)
                {
                    var arr = [];
                   $('#displaytable tbody tr').each(function()
                   {
                       var obj = {};
                       var currow = $(this).closest('tr');
                       var str = currow.find('td:eq(0)').text();
                       var str2 = currow.find('td:eq(1) input').val();
                       var staffid = str.substr(str.indexOf('-')+1);
                       var modid = str2.substr(str2.indexOf('-')+1);;
                       obj['cohortId'] = '<?php echo $cid?>';
                       obj['staffId'] = staffid;
                       obj['moderatorId'] = modid;
                       arr.push(obj);
                   });
                   console.log(arr);

                    e.preventDefault();
                    $.ajaxSetup({
                       headers: {
                           'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                       }
                    });

                    $.ajax({
                        url:"{{url('/storepairing')}}",
                        method:"post",
                        data:{
                            arr:arr
                        },
                        success: function(result)
                        {
                            console.log(result);
                            alert('Changes have been saved');
                        },
                        error: function(result)
                        {
                            console.log(result);
                        }
                    });

                });
            });

        </script>
        <div class="container">
            <h1 class="h1 text-center">Staff Pairing</h1>
        </div>
        <div class="container">
            <div class='row'>
                <div class='col-sm-10 col-xs-10'>
                    <p>Cohort : <?php echo $cid?> </p>
                </div>
                <div class='col-sm-2 col-xs-2'>
                    <input id="savebutton" type="button" class="btn btn-success" value="Save Changes">
                </div>
            </div> 
            <table id="displaytable" class="table table-striped">
                <thead>
                    <tr>
                        <td>Supervisor</td>
                        <td>Moderator</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supresult as $spr)
                    <tr>
                        <td id="{{$spr->staffName}}">{{$spr->staffName}}-{{$spr->staffId}}</td>
                        <td>
                            @if($spr->moderatorId != null)
                                <input list="staffname" type="text" id="staffinput" value="<?php echo $spr->moderatorName."-".$spr->moderatorId?>">                
                            @else
                                <input list="staffname" type="text" id="staffinput">
                            @endif
                            <datalist id="staffname">
                                <option>----</option>
                            </datalist>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endsection