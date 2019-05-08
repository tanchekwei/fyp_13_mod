@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">


                <a class="btn btn-secondary" href="{{url()->previous()}}" role="button">
                    <i class="fa fa-arrow-left fa-1x"></i> Back
                </a>
				<!--
                <pre class="language-php">
                    <code>
                       @{{$blob}}
                    </code>
                </pre>
				-->
				<textarea id="test">{{$blob}}</textarea>
				
            </div>
        </div>
    </div>
<link rel="stylesheet" href="{{asset('public/lib/codemirror.css')}}">
<script src="{{asset('public/lib/codemirror.js')}}" type="text/javascript"></script>
<script src="{{asset('public/mode/htmlmixed/htmlmixed.js')}}" type="text/javascript"></script>
<script src="{{asset('public/mode/javascript/javascript.js')}}" type="text/javascript"></script>
<script>
var codeMirror = CodeMirror.fromTextArea(test, {
	lineNumbers:true,
	mode:"htmlmixed",
});
codeMirror.setSize(900,600);
</script>
@endsection