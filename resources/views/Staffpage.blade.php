@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h1 text-center">Staff Maintenance</h1>
</div>
<div class="container">
    @if(Auth::user()->role == 'admin')
    <a href="{{url('/addfadminpage')}}" class="btn btn-block btn-outline-secondary">Add Faculty Admin</a>
    @endif
    <a href="{{url('/addfyppage')}}" class="btn btn-block btn-outline-secondary">Add FYP Committee</a>
    <a href="{{Url('/addnewstaffpage')}}" class="btn btn-block btn-outline-secondary">Add New Staff</a>
    <a href="{{Url('/deactivatestaff')}}" class="btn btn-block btn-outline-secondary">Update Staff Status</a>
    <a href="{{url('/importstaff')}}" class="btn btn-block btn-outline-secondary">Import File</a>
	<a href="{{route('addkeys')}}" class="btn btn-block btn-outline-secondary">Generate Keys</a>
</div>
@endsection