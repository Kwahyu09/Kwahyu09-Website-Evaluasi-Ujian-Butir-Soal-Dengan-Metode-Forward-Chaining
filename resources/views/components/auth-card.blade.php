<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <style>
        .x-auth-card {
            border-radius: 20px; /* Ubah nilai sesuai keinginan Anda */
        }
    </style>
    {{-- <div class="card shadow-sm bg-white rounded">
        <div class="card-body">
            {{ $slot }}
        </div>
    </div> --}}
    <div class="w-full sm:max-w-md mt-0 px-6 py-4 bg-opacity-0 shadow-lg backdrop-filter backdrop-blur-lg overflow-hidden sm:rounded-lg">
        <div class="text-center mb-3">
            {{ $logo }}
        </div>
        <div class="text-center mb-3">
            <h3 style="font-weight: bold; color: yellow; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.8)">Sistem Informasi Ujian</h3>
            <h5 style="font-weight: bold; color: yellow; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.8)">Fakultas Kedokteran dan Ilmu Kesehatan</h5>
            <h5 style="font-weight: bold; color: yellow; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.8)">Universitas Bengkulu</h5>
        </div>
        {{ $slot }}
    </div>
</div>
