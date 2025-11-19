<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/Styles/Style.css') }}">
</head>

<body>

    <div class="authbox">
        <div class="row">
            <div class="col-2 bg-warning">
                @include('components.sidebar')
            </div>
            <div class="col-8 content">
                <div class="container p-3">
                    @yield('content')
                </div>
            </div>
            <div class="col-2 bg-warning">
                {{-- Kosong atau bisa diisi nanti --}}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>