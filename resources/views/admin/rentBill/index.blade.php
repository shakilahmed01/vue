@extends('admin.layouts.app')
@section('content')
@vite(['resources/css/app.css', 'resources/js/rentbill.js'])
    <div id="rentbill">
    <rentbill-component></rentbill-component>
    </div>
@endsection