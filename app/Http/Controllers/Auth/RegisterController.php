<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function postregister(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
            'no_ktp' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'string'],
            'tanggal_lahir' => ['nullable', 'date'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'no_ktp' => $request->no_ktp,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'jenkel' => $request->jenis_kelamin,
            'tgl_lahir' => $request->tanggal_lahir,
            'tmpt_lahir' => $request->tempat_lahir,
            'created_by' => 'system',
        ]);

        Auth::login($user); // Melakukan login pengguna yang baru dibuat

        return redirect('/home'); // Mengarahkan ke halaman login setelah registrasi
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_ktp' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'string'], // Sesuai dengan model User: 'jenkel'
            'tanggal_lahir' => ['nullable', 'date'], // Sesuai dengan model User: 'tgl_lahir'
            'tempat_lahir' => ['nullable', 'string', 'max:255'], // Sesuai dengan model User: 'tmpt_lahir'
            // 'role' => ['required', 'string', 'in:Admin,Pemilik,Penyewa'], // Menambahkan validasi untuk role
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'no_ktp' => $data['no_ktp'],
            'alamat' => $data['alamat'],
            'telepon' => $data['telepon'],
            'jenkel' => $data['jenis_kelamin'], // Sesuai dengan model User: 'jenkel'
            'tgl_lahir' => $data['tanggal_lahir'], // Sesuai dengan model User: 'tgl_lahir'
            'tmpt_lahir' => $data['tempat_lahir'], // Sesuai dengan model User: 'tmpt_lahir'
            // 'role' => $data['role'], // Menyimpan role pengguna
        ]);

        // Mengasosiasikan role pengguna sesuai dengan yang dipilih
        $user->assignRole('Admin');

        return $user;
    }
}
