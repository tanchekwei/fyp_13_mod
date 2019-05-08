@extends('layouts.app')
@section('content')
    <h5>Project Title : {{$title}} (Cohort {{$cohortId}})</h5><br>

    @if(sizeof($all_students)>0)
        <!--Load all students in the project team-->
        @foreach($all_students as $all_student)
            <table class="table table-light table-bordered">
                <col width="20%">
                <col width="80%">
                <thead class=""><tr><td colspan="2"><b>Student Name : {{$all_student->studentName}}</b></td></tr></thead>
                <tbody>
                <!--The students who have submitted project is stored in $students-->
                <!--Check if $students is empty. If empty then display no submission message for every student-->
                @if(sizeOf($students)>0)
                    <!--Look for student who has submitted their project, then display their submission items.-->
                    @foreach($students as $student)
                        @if($student->studentId == $all_student->studentId)
                            @foreach($submissions as $submission)
                                @if($submission->submission_id == $student->submission_id)
                                    <tr>
                                        <td><b>{{$submission->deliverable_name}}</b></td>
                                        @if($submission->file_name != null && $submission->file_extension != null)
                                            <td><a href="{{route('displaysubmission.download', ['item_id'=>$submission->item_id])}}">{{$submission->file_name}}.{{$submission->file_extension}}</a></td>
                                        @else
                                            <td>{{$submission->content}}</td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                            <tr><td colspan="2">Submission Date : {{$student->submission_date}}</td></tr>
							<tr><td class="text-danger" colspan="2"><a href="{{route('displaysubmission.remove', ['submission_id'=>$submission->submission_id])}}" onclick="return confirm('Are you sure to delete the submission for {{$all_student->studentName}}?')">Remove</a></td></tr>
                        @else
                            <tr>
                                <td colspan="2">This student has not submit.</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td colspan="2">This student has not submit.</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <br>
        @endforeach
    @else
        No student found.
    @endif

@endsection