@extends('admin.layouts.app')
@section('content')
@vite(['resources/css/app.css', 'resources/js/employee.js'])
    <div id="employee">
        <employee-component></employee-component>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
@endsection