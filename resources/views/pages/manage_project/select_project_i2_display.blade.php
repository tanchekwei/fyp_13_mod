@extends('layouts.app')
<style>
    .tab {
    }

    /* Style the buttons inside the tab */

    .tab button {
        background-color: inherit;
        float: left;
        outline: none;
        border:none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */

    .tab button:hover {
        background-color: #eee;
    }

    /* Create an active/current tablink class */

    .tab button.active {
        border-bottom: 2px solid black;
    }

    /* Style the tab content */

    .tabcontent {
        display: none;
        padding: 6px 12px;
        border-top: none;
    }
</style>
@section('content')
    <h1>Select Project for I<sup>2</sup> Display</h1>
    <br>
    <div class="tab"  style="clear: both" >
        <button class="tablinks" onclick="openTab(event, 'Select New ProjectOld')">Select New Project</button>
        <button class="tablinks" onclick="openTab(event, 'Previously Selected ProjectOld')">Previously Selected Project</button>
    </div>

    <div id = "Select New Project" class="tabcontent">
        <form method="post" enctype="multipart/form-data">
            <table class="table">
                <thead>
                <tr class="row">
                    <td class="col"><b>Project ID</b></td>
                    <td class="col"><b>Project Name</b></td>
                    <td class="col"><b>Select</b></td>
                </tr>
                </thead>
                <tbody>
                @if(count($projects) > 0)
                    @foreach($projects as $project)
                        <tr class="row">
                            <td class="col">{{$project->getId()}}</td>
                            <td class="col">{{$project->getName()}}</td>
                            <td class="col">&nbsp;&nbsp;&nbsp;&nbsp;<input class="selectedstudent" type="checkbox" value="{{$project->getId()}}"></td>
                        </tr>
                    @endforeach
                @else
                    No record found.
                @endif
                <tr class="row">
                    <td class="col"></td>
                    <td class="col"></td>
                    <td class="col"><input class="btn btn-success" type="submit" value="Add"  onclick="selectstudent()"></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

    <div id = "Previously Selected Project" class="tabcontent">
        <form method="post" enctype="multipart/form-data">
            <table class="table">
                <thead>
                <tr class="row">
                    <td class="col"><b>Project ID</b></td>
                    <td class="col"><b>Project Name</b></td>
                    <td class="col"><b>Remove</b></td>
                </tr>
                </thead>
                <tbody>
                @if(count($projects) > 0)
                    @foreach($projects as $project)
                        <tr class="row">
                            <td class="col">{{$project->getId()}}</td>
                            <td class="col">{{$project->getName()}}</td>
                            <td class="col">&nbsp;&nbsp;&nbsp;&nbsp;<input class="selectedstudent" type="checkbox" value="{{$project->getId()}}"></td>
                        </tr>
                    @endforeach
                @else
                    No record found.
                @endif

                <tr class="row">
                    <td class="col"></td>
                    <td class="col"></td>
                    <td class="col"><input class="btn btn-danger" type="submit" value="Remove" class="remove" onclick="removestudent()"></td>
                </tr>
                </tbody>
            </table>

        </form>
    </div>

    <script>
        document.getElementsByClassName('tablinks')[0].click();

        function selectstudent() {
            event.preventDefault();
            var selectedStudent = [];
            var inputElements = document.getElementsByClassName("selectedstudent");

            for (var i = 0; i < inputElements.length; i++) {
                if (inputElements[i].checked) {
                    selectedStudent.push(inputElements[i].value);
                }
            }
            if (confirm("Are you sure to select " + selectedStudent + " ?")) {

            } else {

            }
        }

        function removestudent() {
            event.preventDefault();
            var selectedStudent = [];
            var inputElements = document.getElementsByClassName("selectedstudent");

            for (var i = 0; i < inputElements.length; i++) {
                if (inputElements[i].checked) {
                    selectedStudent.push(inputElements[i].value);
                }
            }
            if (confirm("Are you sure to remove " + selectedStudent + " ?")) {

            } else {

            }
        }

        function openTab(evt, tabName) {

            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

    </script>
@endsection
