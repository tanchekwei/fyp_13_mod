@extends('layouts.app')

@section('content')
<script>
    jQuery(document).ready(function()
    {
		Date.prototype.toInputFormat = function() {
			var yyyy = this.getFullYear().toString();
			var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
			var dd  = this.getDate().toString();
			return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
		};
		
		$('#p2startdate').attr('disabled','disabled');
		
        $('#p1startdate').change(function()
        {
            var date = new Date($("#p1startdate").val()),
			days = 7 * parseInt($('input[name=week]:checked').val(),10);
        
			if(!isNaN(date.getTime())){
				date.setDate(date.getDate() + days);
            
				$("#p1enddate").val(date.toInputFormat());
			} else {
				alert("Invalid Date");  
			}
			
			$('#p2startdate').removeAttr('disabled');
			$('#p2startdate').attr('min',date.toInputFormat());
			$('#p2startdate').val("");
			$('#p2enddate').val("");
        });
        
        $('input[name=week]').click(function()
        {
           $('#p1startdate').trigger('change');
        });
        
        $('#p2startdate').change(function()
        {
            var date = new Date($("#p2startdate").val()),
			days = 7 * parseInt($('input[name=week2]:checked').val(),10);
        
			if(!isNaN(date.getTime())){
				date.setDate(date.getDate() + days);
            
				$("#p2enddate").val(date.toInputFormat());
			} else {
				alert("Invalid Date"); 
			}
        });
        
        $('input[name=week2]').click(function()
        {
           $('#p2startdate').trigger('change');
        });
    });
</script>
        <div class="container">
            <h1 class="h1 text-center">Update Cohort</h1>
        </div>
        
        <div class="container">
            <div class="modal-body">
                <form method="post" action="{{route('updatecohort',$id)}}">
                <input type="hidden" name="id" value="<?=$id;?>" />
                    @csrf
                <table class="table">
                    <tr>
                        <td>
                            <p>Cohort Name</p>
                        </td>
                        <td>:</td>
                        <td>
                            <input type="text" name="cohortid" class="input-group input-group-text" value="{{$cohort['cohortId']}}" required="required"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Project 1 Starting Date
                        </td>
                        <td>:</td>
                        <td>
                            <input type="date" name="p1startdate" id='p1startdate' pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="input-group input-group-text" value="{{$cohort['project1startingDate']}}" required="required"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Project 1 End Date
                        </td>
                        <td>:</td>
                        <td>
                            <div class='row'>
                                <div class='col-sm-8'> 
                                    <input type="date" name="p1enddate" id='p1enddate' class="input-group input-group-text" value="{{$cohort['project1endDate']}}" readonly="true" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"/>
                                </div>
                                <div class='col-sm-4'>
                                    <input type='radio' name='week' id='7' value='7'><label for='7'>7 Week</label>
                                    <input type='radio' name='week' id='14' value='14' checked="checked"><label for='14'>14 Week</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Project 2 Starting Date
                        </td>
                        <td>:</td>
                        <td>
                            <input type="date" name="p2startdate" id='p2startdate' class="input-group input-group-text" value="{{$cohort['project2startingDate']}}" required="required" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Project 2 End Date
                        </td>
                        <td>:</td>
                        <td>
                            <div class='row'>
                                <div class='col-sm-8'> 
                                    <input type="date" name="p2enddate" id='p2enddate' class="input-group input-group-text" value="{{$cohort['project2endDate']}}" readonly="true" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"/>
                                </div>
                                <div class='col-sm-4'>
                                    <input type='radio' name='week2' id='7_2' value='7'><label for='7_2'>7 Week</label>
                                    <input type='radio' name='week2' id='14_2' value='14' checked="checked"><label for='14_2'>14 Week</label>
                                </div>
                            </div>                            
                        </td>
                    </tr>
                </table>
                <button type="submit" class='btn btn-primary text-right'>Update</button>
                </form>
            </div>     
        </div>
@endsection