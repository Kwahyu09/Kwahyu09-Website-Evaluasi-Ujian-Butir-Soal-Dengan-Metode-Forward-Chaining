@extends('layoutdashboard.main')
@section('container')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            @if(session()->has('succes'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <h1 class="h3 mb-3 fw-normal text-center">Tambah Akun</h1>
            <main class="form-signin w-100 m-auto mt-3">
                <form action="/register" method="post">
                    @csrf
                    <label for="name">Nama</label>
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            id="name" placeholder="Nama" required value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <label for="username">Usename</label>
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                            id="username" placeholder="Username" required value="{{ old('username') }}">
                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <label for="email">Email address</label>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control  @error('email') is-invalid @enderror"
                            id="email" placeholder="name@example.com" required value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <label for="Password">Password</label>
                    <div class="form-floating mb-3">
                        <input type="password" name="password"
                            class="form-control  @error('password') is-invalid @enderror" id="password"
                            placeholder="Password" required>
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <button class="w-100 btn btn-lg btn-primary mt-3 mb-3" type="submit">Tambah</button>
                </form>
            </main>
        </div>
    </div>
</div>
@endsection