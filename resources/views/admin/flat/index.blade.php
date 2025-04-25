@extends('admin.layouts.app')
@section('content')
@vite(['resources/css/app.css', 'resources/js/flat.js'])
    <div id="flat">
        <flat-component></flat-component>
    </div>
@endsection