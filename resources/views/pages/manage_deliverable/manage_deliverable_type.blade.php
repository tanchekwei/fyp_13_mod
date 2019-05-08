@extends('layouts.app')
@section('content')
    <h4>Manage Deliverable Type</h4>
    <br>
    @include('misc.messages')
    <br>
    <div>
        <table width="20%">
            <tr>
                <td><input type="button" class="btn btn-block btn-dark" id="action_add" value="Add"></td>
                <td><input type="button" class="btn btn-block btn-dark" id="action_edit" value="Edit"></td>
            </tr>
        </table>
    </div>
    <!--Previous design for action selection
    <div style="width: 50%">
        <label for="select_action">Action: </label>
        <select id="select_action" class="form-control">
            <option value="0">Create</option>
            <option value="1">Update/Delete</option>
        </select>
    </div>
    -->
    <br>
    <br>

    <div id="div_one" class="table">
        <form method="POST" action="{{action('ManageDeliverableTypeController@store')}}"
              id="deliverable_type_insert_form" enctype="multipart/form-data"  autocomplete="off">
            <table width="50%">
                <tr>
                    <td><label class="col-form-label">Deliverable Type Name</label></td>
                    <td><input class ="form-control" name="deliverable_name" type="text" required/></td>
                </tr>
                <tr>
                    <td><label class="col-form-label">File Extension</label></td>
                    <!--[a-zA-Z0-9,\-]+-->
                    <td><input class ="form-control" type="text" name="deliverable_extension" pattern="[a-zA-Z0-9\-]+(,[a-zA-Z0-9\-]+)*"
                               placeholder="Hover to view help"
                               title="For single extension :pdf
For multiple extension :pdf,doc,docx
No extension :-"
                               required/><div class="help-tip">

                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label class="col-form-label">Input Type</label></td>
                    <td><select class="custom-select" id="deliverable_field_type" name="deliverable_field_type" required>
                            <option value="File">File</option>
                            <option value="Textarea">Paragraph</option>
                            <option value="Text">Text</option>
                        </select></td>
                </tr>
                <tr>
                    <td><br></td>
                    <td><br></td>
                </tr>
                <tr>
                    <td>
                        <button class="btn btn-success" id="insert">Save</button>
                    </td>
                    <td></td>
                </tr>
            </table>
            <input type="hidden" name="deliverable_type_id" value="">
            <input type="hidden" name="action" value="">
            {{ csrf_field() }}
        </form>
    </div>
    <div id="div_two" class="table">

        <form method="POST" action="{{action('ManageDeliverableTypeController@store')}}"
              id="deliverable_type_updatedelete_form" enctype="multipart/form-data"  autocomplete="off">

            <table width="50%">
                <col width="40%">
                <col width="60%">
                <tr>
                    <td><label class="col-form-label">Select Deliverable Type</label></td>
                    <td>
                        <select id="select_deliverable_type" name="deliverable_type" class="custom-select" required>
                        </select>
                    </td>
                </tr>
            </table>
            <td colspan="2"><hr></td>
            <table width="50%">
                <col width="40%">
                <col width="60%">
                <tr>
                    <td><label class="col-form-label">Deliverable Type</label></td>
                    <td><input class ="form-control" id="deliverable_name" name="deliverable_name" type="text" value="" required/>
                    </td>
                </tr>
                <tr>

                    <td><label class="col-form-label">Deliverable Extension</label></td>
                    <td><input class ="form-control" type="text" id="deliverable_extension" name="deliverable_extension"
                               placeholder="Hover to view help"
                               title="For single extension :pdf
For multiple extension :pdf,doc,docx"
                               value="" pattern="[a-zA-Z0-9\-]+(,[a-zA-Z0-9\-]+)*" required/></td>
                </tr>
                <tr>
                    <td><label class="col-form-label">Field Type</label></td>
                    <td><select class="custom-select" id="deliverable_field_type2" name="deliverable_field_type">
                            <option value="File">File</option>
                            <option value="Textarea">Paragraph</option>
                            <option value="Text">Text</option>
                        </select></td>
                </tr>
                <tr>
                    <td><br></td>
                    <td><br></td>
                </tr>
                <tr></tr>
                <tr>
                    <td>
                        <button class="btn btn-dark" id="update">Save</button>
                    </td>
                    <td>
                        <button class="btn btn-danger" id="delete">Delete</button>
                    </td>
                </tr>

            </table>
            <input type="hidden" name="deliverable_type_id" value="">
            <input type="hidden" name="action" value="">
            {{ csrf_field() }}
        </form>
    </div>

    <script>

        $(document).ready(function () {

            $('#action_add').click(function (e) {
                //check if triggered event is done by human. This is to hide any previous alert msg when user manually switch tab
                if (e.originalEvent !== undefined) {
                    $('.alert').hide();
                }
                $('#div_one').show();
                $('#div_two').hide();
            });

            $('#action_edit').click(function (e) {
                $.ajax({
                    type: 'GET',
                    url: '{{action('ManageDeliverableTypeController@ajax_load_deliverable_type')}}',
                    dataType: "JSON",
                    data: {},
                    //headers:{
                    //    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //},
                    success: function (response) {
                        var deliverable_types = response;
                        $('#select_deliverable_type').html('<option id="-1" selected="selected" disabled>Select Deliverable Type</option>');
                        $(deliverable_types).each(function (i) {
                            $('#select_deliverable_type').append('<option value="'+deliverable_types[i].deliverable_type_id+'" id="' + deliverable_types[i].deliverable_type_id + '">' + deliverable_types[i].deliverable_type + '</option>');
                        });
                        //remember previously selected deliverable after update
                        @if(session('selected_id'))
                        $('#select_deliverable_type').val('{{session("selected_id")}}').trigger('change');
                        @endif
                    },
                    error: function (xhr, status, error) {
                        alert("Error:" + xhr.responseText);
                    }
                });
                //check if triggered event is done by human. This is to hide any previous alert msg when user manually switch tab
                if (e.originalEvent !== undefined) {
                    $('.alert').hide();
                }
                $('#div_one').hide();
                $('#div_two').show();
            });

            //Programatically change tab based on previous event
            var active_tab = '{{session('active_tab')}}';
            if(active_tab == '0'){
                $('#action_add').click();
            }
            else{
                $('#action_edit').click();
            }


            $('#insert').click(function () {
                var selected_id = $("#select_deliverable_type option:selected").attr("id");
                $('[name=action]').val("insert");
                $('[name=deliverable_type_id]').val(selected_id);

                //disable insert button after submit
                $("#deliverable_type_insert_form").submit(function () {
                    $('#insert').prop('disabled', true);
                    return true;
                });
            });
            $('#update').click(function () {
                var selected_id = $("#select_deliverable_type option:selected").attr("id");
                if(selected_id == "-1"){
                    alert("Please select a Deliverable Type before proceeding.");
                    return false;
                }
                else {
                    $('[name=action]').val("update");
                    $('[name=deliverable_type_id]').val(selected_id);

                    //disable update button after submit
                    $("#deliverable_type_updatedelete_form").submit(function () {
                        $('#update').prop('disabled', true);
                        return true;
                    });
                }

            });

            $('#delete').click(function (e) {
                var selected_id = $("#select_deliverable_type option:selected").attr("id");
                if(selected_id == "-1"){
                    alert("Please select a Deliverable Type before proceeding.");
                    return false;
                }
                else {
                    var selected = $("#select_deliverable_type option:selected").text();
                    if (confirm("Are you sure to delete " +selected+ "?")) {
                        $('[name=action]').val("delete");
                        $('[name=deliverable_type_id]').val(selected_id);

                        //disable delete button after submit
                        $("#deliverable_type_updatedelete_form").submit(function () {
                            $('#delete').prop('disabled', true);
                            return true;
                        });
                    } else {
                        return false;
                    }
                }

            });

            $('#select_deliverable_type').change(function () {

                var selected_id = $("#select_deliverable_type option:selected").attr("id");

                $.ajax({
                    type: 'GET',
                    url: '{{action('ManageDeliverableTypeController@ajax_change_deliverable_type')}}',
                    dataType: "JSON",
                    data: {
                        id: selected_id
                    },
                    //headers:{
                    //    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //},
                    success: function (response) {
                        var deliverable_types = response;
                        $('#deliverable_name').val(deliverable_types[0].deliverable_type);
                        $('#deliverable_extension').val(deliverable_types[0].deliverable_extension);
                        $('#deliverable_field_type2').val(deliverable_types[0].field_type).change();
                    },
                    error: function (xhr, status, error) {
                        alert("Error:" + xhr.responseText);
                    }
                });

            });


        });


    </script>
@endsection

