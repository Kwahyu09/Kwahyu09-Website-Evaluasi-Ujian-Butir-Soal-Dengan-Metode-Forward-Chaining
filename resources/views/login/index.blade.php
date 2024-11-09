<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Template CSS -->
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/style.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title>Login Mahasiswa</title>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                @if(session()->has('loginError'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('loginError') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <h1 class="h3 mt-5 mb-3 fw-normal text-center">Login</h1>
                <main class="mt-3 form-signin w-100 m-auto">
                    <form action="{{ route('login-mahasiswa.store') }}" method="post">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                id="login" placeholder="npm anda" required autofocus value="{{ old('login') }}">
                            <label for="login">NPM</label>
                            @error('login')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="token" class="form-control @error('token') is-invalid @enderror"
                                id="token" placeholder="npm anda" autofocus value="{{ old('token') }}">
                            <label for="token">Token</label>
                            @error('token')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>
                        <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="backend/assets/modules/jquery.min.js"></script>
    <script src="backend/assets/modules/popper.js"></script>
    <script src="backend/assets/modules/tooltip.js"></script>
    <script src="backend/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="backend/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="backend/assets/modules/moment.min.js"></script>
    <script src="backend/assets/js/stisla.js"></script>
    <!-- Page Specific JS File -->
    <script src="backend/assets/js/page/index-0.js"></script>
    <!-- Template JS File -->
    <script src="backend/assets/js/scripts.js"></script>
    <script src="backend/assets/js/custom.js"></script>
</body>

</html>