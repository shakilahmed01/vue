@extends('admin.layouts.app')
@section('content')
@vite(['resources/css/app.css', 'resources/js/employee.js'])
    <div id="employee">
        <employee-component></employee-component>
    </div>
@endsection