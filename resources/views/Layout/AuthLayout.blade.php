<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/Styles/Style.css') }}">
</head>

<body>

    <div class="authbox">
        <div class="row">
            <div class="col-4 bgauth">
                <img src="{{ asset('assets/img/bgauth1.png') }}" alt="Background">
            </div>
            <div class="col-8 authform d-flex justify-content-center align-items-center">
                @yield('content')
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>