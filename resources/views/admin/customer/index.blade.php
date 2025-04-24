@extends('admin.layouts.app')
@section('content')
@vite(['resources/css/app.css', 'resources/js/customer.js'])
    <div id="customer">
        <customer-component></customer-component>
    </div>
@endsection