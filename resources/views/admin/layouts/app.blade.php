<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ $pageTitle ?? '' }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    @include('admin.include.css')
</head>

<body>
    <!-- ======= Header ======= -->
    @include('admin.include.header')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('admin.include.sidebar')
    <!-- End Sidebar-->

    <main id="main" class="main">

        @include('admin.include.breadcrumb')
        <!-- End Page Title -->
        @yield('content')
    </main>
    <!-- End #main -->

    @include('admin.include.footer')

    @include('admin.include.js')
</body>

</html>