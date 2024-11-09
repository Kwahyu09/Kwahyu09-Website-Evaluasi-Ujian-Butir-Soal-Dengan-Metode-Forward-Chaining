<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AktorController extends Controller
{
    //menampilkan halaman home staff
    public function index_staff()
    {
        return view('aktor.index', [
            "title" => "staff",
            "judul" => "Staff",
            "post" => User::latest()->Filter(request(['search']))->where('role', 'Staf')->paginate(10)
        ]);
    }

    //menampilkan halaman home ketua modul
    public function index_ketua()
    {
        return view('aktor.index', [
            "title" => "ketua",
            "judul" => "Ketua",
            "post" => User::latest()->Filter(request(['search']))->where('role', 'Ketua')->paginate(10)
        ]);
    }

    //menampilkan halaman tambah data staff
    public function create_staff()
    {
        return view('aktor.create', [
            "title" => "staff",
            "judul" => "Staff",
            "role" => "Staf"
        ]);
    }

    //menambahkan data staff ke database
    public function store_staff(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User',
            'nama' => 'required|max:60|min:3|unique:App\Models\User',
            'username' => 'required|min:6|max:8|unique:App\Models\User',
            'role' => 'required|min:4|max:9',
            'email' => 'required|email|max:60|min:6|unique:App\Models\User',
            'password' => 'required|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);
        return redirect('/staff')->with('success', 'Data Berhasil Ditambahkan!');
    }

    //menampilkan halaman tambah data ketua
    public function create_ketua()
    {
        return view('aktor.create', [
            "title" => "ketua",
            "judul" => "Ketua",
            "role" => "Ketua"
        ]);
    }

    //menambahkan data ketua ke database
    public function store_ketua(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:60|min:3|unique:App\Models\User',
            'username' => 'required|min:6|max:8|unique:App\Models\User',
            'nip' => 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User',
            'role' => 'required|min:4|max:9',
            'email' => 'required|email|max:60|min:6|unique:App\Models\User',
            'password' => 'required|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);
        return redirect('/ketua')->with('success', 'Data Berhasil Ditambahkan!');
    }

    //menampilkan halaman edit profile
    public function edit(User $user)
    {
        $role = "Admin";
        $kelas_id = "";
        if (auth()->user()->role == "Ketua") {
            $role = "Ketua";
        } elseif (auth()->user()->role == "Staf") {
            $role = "Staf";
        } elseif (auth()->user()->role == "Mahasiswa") {
            $role = "Mahasiswa";
            $kelas_id = auth()->user()->kelas_id;
        }
        return view('aktor.edit', [
            "title" => "Profile",
            "post" => $user,
            "role" => $role,
            "kelas_id" => $kelas_id
        ]);
    }

    //menampilkan halaman edit staff aktor admin
    public function edit_staf(User $user)
    {
        $kelas_id = "";
        return view('aktor.edit2', [
            "title" => "Staff",
            "link" => "staff",
            "post" => $user,
            "role" => "Staf",
            "kelas_id" => $kelas_id
        ]);
    }

    //menampilkan halaman edit ketua aktor admin
    public function edit_ketua(User $user)
    {
        $kelas_id = "";
        return view('aktor.edit2', [
            "title" => "Ketua",
            "link" => "ketua",
            "post" => $user,
            "role" => "Ketua",
            "kelas_id" => $kelas_id
        ]);
    }

    //mengubah data admin pada database
    public function profile(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|max:60|min:3',
            'role' => 'required|min:4|max:9',
            'password' => 'required|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        ];

        if (auth()->user()->role == "Ketua" || auth()->user()->role == "Admin") {
            if ($request->nip != $user->nip) {
                $rules['nip'] = 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User';
            }
        } elseif (auth()->user()->role == "Staf") {
            if ($request->nik != $user->nik) {
                $rules['nik'] = 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User';
            }
        } elseif (auth()->user()->role == "Mahasiswa") {
            if ($request->npm != $user->npm) {
                $rules['npm'] = 'required|min:9|max:10|regex:/^[A-Z0-9]+$/|unique:App\Models\User';
            }
        }

        if ($request->username != $user->username) {
            $rules['username'] = 'required|min:6|max:12|unique:App\Models\User';
        }
        if ($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|max:150|min:6|unique:App\Models\User';
        }

        // Ambil password dari database untuk user yang diedit
        $userFromDB = User::find($user->id);

        // Periksa apakah password lama yang dimasukkan sama dengan password di database setelah dihash
        if (!Hash::check($request->password_lama, $userFromDB->password)) {
            return redirect()->back()->withInput()->withErrors(['password_lama' => 'Password lama tidak sesuai']);
        }

        $validatedData = $request->validate($rules);


        $validatedData['password'] = Hash::make($validatedData['password']);
        User::where('id', $user->id)
            ->update($validatedData);
        return redirect('/')->with('success', 'Data Berhasil Diubah!');
    }

    //mengubah data staff didatabase
    public function update_staf(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|max:60|min:3',
            'role' => 'required|min:4|max:9',
            'password' => 'required|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        ];

        if ($request->nik != $user->nik) {
            $rules['nik'] = 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User';
        }
        if ($request->username != $user->username) {
            $rules['username'] = 'required|min:6|max:12|unique:App\Models\User';
        }
        if ($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|max:60|min:6|unique:App\Models\User';
        }

        // Ambil password dari database untuk user yang diedit
        $userFromDB = User::find($user->id);

        // Periksa apakah password lama yang dimasukkan sama dengan password di database setelah dihash
        if (!Hash::check($request->password_lama, $userFromDB->password)) {
            return redirect()->back()->withInput()->withErrors(['password_lama' => 'Password lama tidak sesuai']);
        }

        $validatedData = $request->validate($rules);
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::where('id', $user->id)
            ->update($validatedData);
        return redirect('/staff')->with('success', 'Data Berhasil Diubah!');
    }

    //mengubah data ketua didatabase
    public function update_ketua(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|max:60|min:3',
            'role' => 'required|min:4|max:9',
            'password' => 'required|min:10|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        ];

        if ($request->nip != $user->nip) {
            $rules['nip'] = 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\User';
        }
        if ($request->username != $user->username) {
            $rules['username'] = 'required|min:6|max:12|unique:App\Models\User';
        }
        if ($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|max:60|min:6|unique:App\Models\User';
        }

        // Ambil password dari database untuk user yang diedit
        $userFromDB = User::find($user->id);

        // Periksa apakah password lama yang dimasukkan sama dengan password di database setelah dihash
        if (!Hash::check($request->password_lama, $userFromDB->password)) {
            return redirect()->back()->withInput()->withErrors(['password_lama' => 'Password lama tidak sesuai']);
        }

        $validatedData = $request->validate($rules);
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::where('id', $user->id)
            ->update($validatedData);
        return redirect('/ketua')->with('success', 'Data Berhasil Diubah!');
    }

    //menghapus data staff didatabase
    public function destroy_staff(User $user)
    {
        User::destroy($user->id);
        return redirect('/staff')->with('success', 'Data Berhasil DiHapus!');
    }

    //menghapus data ketua di database
    public function destroy_ketua(User $user)
    {
        User::destroy($user->id);
        return redirect('/ketua')->with('success', 'Data Berhasil DiHapus!');
    }
}
