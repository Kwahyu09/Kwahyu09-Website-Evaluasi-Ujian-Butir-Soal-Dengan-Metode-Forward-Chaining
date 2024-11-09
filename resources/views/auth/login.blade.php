<x-guest-layout>
    <style>
        #password::placeholder {
            color: black;
            font-weight: bold;
        }
        #login::placeholder {
            color: black;
            font-weight: bold;
        }
        #password,
        #role,
        #login,
        #submit,
        .x-auth-card {
            border-radius: 20px; /* Ubah nilai sesuai keinginan Anda */
        }
        #password,
        #role,
        #login {
            padding-left: 20px; /* Ubah nilai sesuai keinginan Anda */
            /* text-indent: 10px; Ubah nilai sesuai keinginan Anda */
        }

        /* Ganti warna latar belakang input ketika di dalam keadaan fokus */
        #login:focus,
        #password:focus {
            background-image: linear-gradient(to bottom, #c8c8c8 0%, #c8c8c8 100%); /* Ganti warna latar belakang input/select ketika aktif */
        }

        /* Ganti warna latar belakang input/select ketika tidak dalam keadaan fokus */
        #login:not(:focus),
        #password:not(:focus) {
            background-color: transparent; /* Ganti latar belakang input/select ketika tidak aktif */
        }
        select:focus {
            outline: none; /* Menghapus outline default pada select saat dalam fokus */
            background-color: #ffffff; /* Latar belakang putih ketika dalam fokus */
            color: #000; /* Warna teks normal ketika dalam fokus */
            border-color: #000; /* Warna border ketika dalam fokus (jika diperlukan) */
        }
    </style>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" style="text-shadow: 1px 1px 1px rgb(0, 0, 0)" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label class="block mb-2 text-sm font-medium text-black dark:text-black text-center font-weight: bold;" for="role"><b>Masuk Sebagai</b></label>
            <select class="mb-4 pl-10 pr-4 py-2 rounded-full w-full bg-transparent border border-gray-300 text-black placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:placeholder-gray-400 dark:text-black dark:focus:border-blue-500 dark:focus:ring-blue-500" style="font-weight: bold;" id="role" name="role" required>
                <option class="text-black" value="Mahasiswa" selected>Mahasiswa</option>
                <option class="text-black" value="Ketua">Ketua Modul</option>
                <option class="text-black" value="Staf">Staf</option>
                <option class="text-black" value="Admin">Admin</option>
            </select>
            <!-- NIK/NIP/NPM -->
            <div>
                <x-input id="login" class="mb-4 pl-10 pr-4 py-2 rounded-full w-full bg-transparent border border-gray-300 text-black placeholder-black font-bold" style="font-weight: bold;" placeholder="NIK/ NIP/ NPM" type="text" name="login" :value="old('login')" required autofocus />
            </div>
            <div>
                <x-input id="password" 
                class="mb-4 pl-10 pr-4 py-2 rounded-full w-full bg-transparent border border-gray-300 text-black placeholder-black font-bold" 
                style="font-weight: bold;"
                placeholder="Password"
                type="password"
                name="password"
                required autocomplete="current-password"  />
                <div class="flex items-center">
                    <input class="mt-1 mr-2" type="checkbox" onclick="myFunction()"> <label class="block inline text-sm font-medium text-white dark:text-black"><b>Tampilkan Password</b></label>
                </div>
            </div>
            <div class="flex items-center justify-center mt-4">
                <x-button style="color:white; background-color:#0d6efd; text-align: center; display: flex; justify-content: center; align-items: center;" class="form-control px-3 mb-4 pl-10 pr-4 py-2 rounded-full w-full border border-gray-300 text-white placeholder-white font-bold" id="submit" name="submit">
                        {{ __('Login') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
<script>
    function myFunction() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>