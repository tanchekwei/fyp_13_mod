@extends('layouts.app')
@section('content')
    <h4>Manage Deliverable</h4>
    <br>
    @include('misc.messages')
    <br>
    <form method="POST" action="{{action('ManageDeliverableController@store')}}" class="deliverable_form" enctype="multipart/form-data">
        <div class="table">
            <table width="40%">
                <tbody>
                <tr>
                    <td><label for="cohort" class="col-form-label">Cohort</label></td>
                    <td><select id="cohort"  name="cohort" class="custom-select selected_cohort_competition">
                            <option selected="selected" disabled>Select Cohort</option>
                            @foreach($data['cohorts'] as $cohort)
                                    <option value="{{$cohort->cohortId}}">{{$cohort->cohortId}}</option>;
                            @endforeach

                        </select></td>
                </tr>
                <tr>
                    <td><label class="col-form-label" for="faculty">Faculty</label></td>
                    <!--<td><select id="faculty" name="faculty" class="custom-select selected_faculty">-->
                    <td><select id="faculty" name="faculty" class="custom-select selected_cohort_competition">
                            <option selected="selected" disabled>Select Faculty</option>
                            @foreach($data['faculties'] as $faculty)
                                <option value="{{$faculty->facultyId}}">{{$faculty->facultyId}}</option>;
                            @endforeach
                        </select></td>

                </tr>

                <!--Allow programme selection
                <tr>
                    <td><label class="col-form-label" for="programme">Programme</label></td>
                    <td><select id="programme" class="custom-select selected_cohort_programme_competition">
                            <option selected="selected" disabled>Select Programme</option>
                        </select></td>
                </tr>
                -->
                <tr>
                    <td><label class="col-form-label" for="competition">Submission Type</label></td>
                    <td><select id="competition" name="competition"
                                class="custom-select selected_cohort_competition">
                            <option value="Normal" selected="selected">Normal</option>
                            <option value="Competition">Competition</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <hr>
        <table id="cohort_deliverable_table" class="table">
            <thead>
            <tr>
                <th class="col_name">Deliverable Name</th>
                <th class="col_type">Type</th>
                <th class="col_extension">File Extension</th>
                <th class="col_showcase">Showcase</th>
                <th class="col4_delete"></th>
            </tr>
            </thead>
            <tbody id="cohort_deliverable_table_body">
            </tbody>
        </table>
        <input type="hidden" name="deliverable_id" value="">
        <input type="hidden" name="deliverable_type_id" value="">
        <input type="hidden" name="programme" value="">
        {{ csrf_field() }}
    </form>

    <script>
        $(document).ready(function () {
            var rowCountOriginal;
            var rowCount;


            $(document).on('click', '.btn_add', function (e) {
                e.preventDefault();
                //if there is no rows, set row number to 0
                var i = 0;
                if ($('#cohort_deliverable_table tr').length <= 2) {
                    i=0;
                }
                else {
                    var row_id = $('#cohort_deliverable_table tr:last').prev().attr('id');
                    i = row_id.substring(3);  //get row number from row id
                    i++;                      //add number of row
                }

                $.ajax({
                    type: 'get',
                    url: '{{action('ManageDeliverableController@ajax_add_deliverable')}}',
                    data: {
                        index: i
                    },
                    success: function (response) {
                        //alert(response);
                        $('#cohort_deliverable_table tr:last').before(response);
                        rowCount++;
                    }
                });
                return false;
            });
            $(document).on('click', '.btn_remove', function () {
                var button_id = $(this).attr("id");
                var deliverable_name = $('#row' + button_id + ' td').find('input[type=text]').val();
                if (confirm("Are you sure to remove " + deliverable_name + "?")) {
                    $('#row' + button_id + '').remove();
                    rowCount--;
                } else {
                    return false;
                }
            });

            $(document).on('click', '.btn_submit', function (e) {
                if(rowCountOriginal === 1 && rowCount ===1){
                    alert(rowCount2);
                    alert("No deliverables detected.");
                    return false;
                }

                //get deliverable_id
                var deliverable_id = $("input[name='name[]']").map(function () {
                    return $(this).attr('id');
                }).get();

                //get deliverable_type_id
                var deliverable_type_id = [];
                $('select[name="deliverable_type[]"] option:selected').each(function () {
                    deliverable_type_id.push($(this).attr('id'));
                });

                //insert deliverable_id and deliverable_type_id into hidden field
                $('[name=deliverable_id]').val(deliverable_id);
                $('[name=deliverable_type_id]').val(deliverable_type_id);
                //need to pass programme through hidden field bc controller cant get vallue from dropdown ilist for some reason
                $('[name=programme]').val($("#programme option:selected").text());

                //disable save button after submit
                $(".deliverable_form").submit(function () {
                    $('.btn_submit').attr("disabled", true);
                    return true;
                });
            });

            //load deliverable when cohort/programme change
            $(document).on('change', '.selected_cohort_competition', function () {
                var selected_cohort = $("#cohort option:selected").text();
                var selected_faculty = $("#faculty option:selected").text();
                //var selected_programme = $("#programme option:selected").text();
                var selected_competition = $("#competition option:selected").text();
                //alert(selected_faculty);

                //if (selected_cohort !== "" && $("#faculty option:selected").index != "0" && $("#programme option:selected").index() != "0" && selected_competition !== "") {
				if (selected_cohort !== "Select Cohort" && selected_faculty !== "Select Faculty" && selected_competition !== "") {
                    $.ajax({
                        type: 'get',
                        url: '{{action('ManageDeliverableController@ajax_load_deliverable')}}',
                        data: {
                            cohort: selected_cohort,
                            faculty: selected_faculty,
                            //programme: selected_programme,
                            competition: selected_competition
                        },
                        success: function (response) {
                            let tbody = $('#cohort_deliverable_table_body');;
                            tbody.html(response);
                            rowCountOriginal = tbody.find('tr').length;
                            rowCount = tbody.find('tr').length;
                        }
                    });
                }
            });

            //change programme when faculty changed
            /*
            $(document).on('change', '#faculty', function () {
                var selected_faculty = $("#faculty option:selected").text();
                //alert(selected_faculty);
                $.ajax({
                    type: 'get',
                    url: '{{--action('ManageDeliverableController@ajax_load_programme')--}}',
                    data: {
                        faculty: selected_faculty
                    },

                    success: function (response) {
                        //convert response to json
                        var programmeName = $.parseJSON(response);
                        //extract programmeName from json and insert into programme dropdown list
                        $('#programme').html('<option selected="selected" disabled>Select Programme</option>');
                        $(programmeName).each(function (i, val) {
                            $.each(val, function (k, programmeName) {
                                $('#programme').append('<option value="'+programmeName+'">' + programmeName + '</option>');
                            });
                        });
                    }
                });

            });
            */

            //change extension when deliverable type is changed
            //use (document).on('change', '.selected_deliverable', function() to listen for newly added row
            $(document).on('change', '.selected_deliverable', function () {
                var selected_id = $(this).children(":selected").attr("id");
                var row = $(this).closest('tr').attr('id');
                //alert('The option with value ' + $(this).val() + ' and id ' + selected + ' was selected.');

                $.ajax({
                    type: 'get',
                    url: '{{action('ManageDeliverableController@ajax_change_deliverable_extension')}}',
                    data: {
                        index: selected_id
                    },
                    success: function (response) {
                        $("#" + row).find("td[class*='selected_deliverable_extension']").html(response);
                    }
                });
            });

            $(document).on('click', '#btn_yes', function (){
                var selected_cohort = $("#cohort option:selected").text();
                var selected_faculty = $("#faculty option:selected").text();
                var selected_programme = $("#programme option:selected").text();
                var selected_competition = $("#competition option:selected").text();
                if (selected_cohort !== "" && $("#faculty option:selected").index != "0" && $("#programme option:selected").index() != "0" && selected_competition !== "") {
                    $.ajax({
                        type: 'get',
                        url: '{{action('ManageDeliverableController@ajax_copy_yes')}}',
                        data: {
                            cohort: selected_cohort,
                            faculty: selected_faculty,
                            programme: selected_programme,
                            competition: selected_competition
                        },
                        success: function (response) {
                            let tbody = $('#cohort_deliverable_table_body');
                            tbody.html(response);
                            rowCountOriginal = tbody.find('tr').length;
                            rowCount = tbody.find('tr').length;
                        }
                    });
                }
            });

            $(document).on('click', '#btn_no', function () {
                let tbody = $('#cohort_deliverable_table_body');
                tbody.html("<tr><td><button class='btn btn-dark btn_add'>Add</button></td><td></td><td></td><td><input class='btn btn-success btn_submit' type='submit' value='Save'></td></tr>");
                rowCountOriginal = tbody.find('tr').length;
                rowCount = tbody.find('tr').length;
            });

            //Preselect the previously selected items after update. Placed at bottom so it will load
            @if(session('cohort') && session('faculty') && session('isCompetition'))
                $('#cohort').val('{{session("cohort")}}').trigger('change');
                $('#faculty').val('{{session("faculty")}}').trigger('change');
                $('#competition').val('{{session("isCompetition")}}').trigger('change');
            @endif
        });


    </script>

@endsection
