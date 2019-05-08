@extends('layouts.app')

@section('content')
    @if($isCompetition == '0')
        <h4>Project Submission</h4>
    @else
        <h4>Competition Submission</h4>
    @endif

    <br>
    @php
        //Load every deliverable_name, deliverable_id, field_type into arrays, then each array is converted into delimited string.
        //For javascript validation use
        $deliverable_name = array();
        $deliverable_id = array();
        $field_type = array();
        $deliverable_extension = array();
        $count = 0;

            //Load deliverable_name, deliverable_id, field_type into arrays
            foreach ($deliverables as $deliverable){
                $deliverable_name[$count] =$deliverable->deliverable_name;
                $deliverable_id[$count] =$deliverable->deliverable_id;
                $field_type[$count] = $deliverable->field_type;
                $deliverable_extension[$count] = $deliverable->deliverable_extension;
                $count++;
            }
            //Convert array to delimited string
            $deliverable_name_string = implode("|", $deliverable_name);
            $deliverable_id_string = implode("|", $deliverable_id);
            $field_type_string = implode("|", $field_type);
            $deliverable_extension_string = implode("|", $deliverable_extension);
    @endphp

    @if(empty($deliverables))
        @if(!empty($submission_message))
            <div class="alert alert-success"> {{ $submission_message }}</div>
        @else
            <div class="alert alert-info">Submission unavailable.</div>
        @endif
    @else
        <form method="POST" action="{{action('ProjectSubmissionController@store')}}" id="submissionform"
              enctype="multipart/form-data">

                <table class="table table-hover" style="width: 100%;">
                    <colgroup>
                        <col style="width:23%;">
                    </colgroup>
                    <tbody>
                    @foreach($deliverables as $deliverable)
                        <tr>
                            <td>{{$deliverable->deliverable_name}}</td>
                            @if($deliverable->field_type === "Textarea")
                                <td colspan="2"><textarea id="field" rows="4" cols="55"
                                                          name="{{$deliverable->deliverable_id}}" required></textarea>
                                </td>
                                <td></td>
                            @elseif($deliverable->field_type === "Text")
                                <td colspan="2"><input id="field" type="{{$deliverable->field_type}}"
                                                       name="{{$deliverable->deliverable_id}}" size="55" required/></td>
                                <td></td>
                            @else
                                <td><input id="field" type="{{$deliverable->field_type}}" name="{{$deliverable->deliverable_id}}" required/></td>
                                <td><div class="text-primary">Accepted File Format: {{$deliverable->deliverable_extension}}</div></td>
                            @endif
                        </tr>
                    @endforeach
                    <tr>
                        <td class="status" colspan="3"><input class="btn btn-success" type="submit" value="Submit"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                <input type="hidden" name="isCompetition" value="{{$isCompetition}}">

            {{ csrf_field() }}
        </form>

        <script>
            $(document).ready(function () {

                $('#submissionform').on('submit', function (e) {
                    e.preventDefault();

                    var extension_flag = 0;
                    var invalid_extension = [];

                    var id = "{{$deliverable_id_string}}";
                    var id_array = id.split('|');
                    var extension = "{{$deliverable_extension_string}}";
                    var extension_array = extension.split('|');
                    var field_type = "{{$field_type_string}}";
                    var field_type_array = field_type.split('|');
                    var name = "{{$deliverable_name_string}}";
                    var name_array = name.split('|');

                    //extension validation
                    for (var i = 0; i < id_array.length; i++) {
                        if (field_type_array[i] == "File") {
                            var file_name = $('[name=' + id_array[i] + ']').val();
                            var file_name_ext = file_name.substr(file_name.lastIndexOf('.') + 1);
                            var sub_extension_array = extension_array[i].split(',');
                            if (jQuery.inArray(file_name_ext, sub_extension_array) == -1) {
                                invalid_extension.push(i);
                                extension_flag = 1;
                            }
                        }
                    }

                    if (extension_flag == 0) {
                        this.submit();
                        $('.status').html('<div class="alert-info">Upload in progress, please do not close this page. Upload time may vary depending on your internet upload speed.</div>');
                    }
                    else {
                        var alert_string = "";
                        for (var z = 0; z < invalid_extension.length; z++) {
                            alert_string += "Please ensure that " + name_array[invalid_extension[z]] + " has a format of " + extension_array[invalid_extension[z]].split(',').join('/') + ".\n";
                        }
                        alert(alert_string);
                    }
                });
            });
        </script>

@endsection