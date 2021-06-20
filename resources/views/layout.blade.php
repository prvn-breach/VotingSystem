<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Election</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">    
    
    @include('header');

</head>

<body>

    <!-- LOADER  -->
    <div id="loading" class="image-preloader">
        <div class="loader">
            <img src="{{ asset('img/logo.png') }}" alt="loading">
        </div>
    </div>
    
    @yield('content')
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/form-step.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>