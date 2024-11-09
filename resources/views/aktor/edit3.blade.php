@extends('layoutdashboard.main')
@section('container')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="flash-data" data-flashdata="{{ session('success') }}">
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Edit Akun {{ $title }}</h4>
                </div>
                <form action="/{{ $role }}/{{ $post->username }}/update" method="post">
                    @method('put')
                    @csrf
                    <input type="hidden" name="kelas_id" class="form-control" id="kelas_id" required
                        value="{{ old('kelas_id', $kelas_id) }}">
                    <input type="hidden" name="role" class="form-control" id="role" required
                        value="{{ old('role', $role) }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="inputAddress">Nama</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                id="nama" required value="{{ old('nama', $post->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputAddress2">Username</label>
                                <input type="text" name="username"
                                    class="form-control @error('username') is-invalid @enderror" id="username" required
                                    value="{{ old('username', $post->username) }}">
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputAddress2">NPM</label>
                                <input type="text" name="npm" class="form-control @error('npm') is-invalid @enderror"
                                    id="npm" required value="{{ old('npm', $post->npm) }}">
                                @error('npm')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Email</label>
                                <input type="email" name="email"
                                    class="form-control  @error('email') is-invalid @enderror" id="email" required
                                    value="{{ old('email', $post->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_lama">Password Lama</label>
                                <input type="password" name="password_lama"
                                    class="form-control  @error('password_lama') is-invalid @enderror" id="password_lama"
                                    required>
                                <input class="mt-1" type="checkbox" onclick="myFunction()">Tampilkan Password
                                @error('password_lama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Password Baru</label>
                                <input type="password" name="password"
                                    class="form-control  @error('password') is-invalid @enderror" id="password" required>
                                <input class="mt-1" type="checkbox" onclick="myFunction1()">Tampilkan Password
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control  @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" required>
                                <input class="mt-1" type="checkbox" onclick="myFunction2()">Tampilkan Password
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer mr-3 mb-3 mt-0">
                            <a class="ml-1 btn btn-danger float-right" href="/kelas/{{ $slug_kelas }}">Batal</a>
                            <button class="btn btn-primary float-right" type="submit">Ubah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="inputPassword4">NPM memuat:</label>
                        <ul>
                            <li>9-10 Karakter</li>
                            <li>Harus Angka dan Huruf Besar</li>
                        </ul>
                        <label for="inputPassword4">Username memuat:</label>
                        <ul>
                            <li>Minimal 6 karakter</li>
                            <li>Diperbolehkan huruf besar/ huruf kecil/ angka misalnya: Kw0942/093128</li>
                        </ul>
                        <label for="inputPassword4">Password Memuat:</label>
                        <ul>
                            <li>Minimal 10 karakter</li>
                            <li>Harus mengandung setidaknya satu huruf kecil (a-z)</li>
                            <li>Harus mengandung setidaknya satu huruf besar (A-Z)</li>
                            <li>Harus mengandung setidaknya satu angka (0-9)</li>
                            <li>Diperbolehkan menggunakan karakter khusus, misalnya: !@#$%^&* (Opsional)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function myFunction() {
            var x = document.getElementById("password_lama");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function myFunction1() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function myFunction2() {
            var x = document.getElementById("password_confirmation");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
@endsection
