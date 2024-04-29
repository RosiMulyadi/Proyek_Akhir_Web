<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pemilik;
use App\Models\Penyewa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('roles')->get();
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('role', function ($row) {
                    return $row->roles->first()->name ?? '';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('users.edit', $row->id);
                    $deleteUrl = route('users.destroy', $row->id);
                    $showUrl = route('users.show', $row->id);

                    $editButton = '<a href="' . $editUrl . '" class="btn btn-sm btn-warning btn-icon btn-round"><i class="fas fa-pen-square fa-circle mt-2"></i></a>';
                    $deleteButton = '<button onclick="deleteItem(this)" data-name="' . $row->name . '" data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-icon btn-round delete-button"><i class="fas fa-trash"></i></button>';
                    $showButton = '<a href="' . $showUrl . '" class="btn btn-sm btn-primary btn-icon btn-round"><i class="fas fa-eye"></i></a>';

                    return $editButton . '&nbsp;&nbsp;' . $showButton . '&nbsp;&nbsp;' . $deleteButton;
                })
                ->rawColumns(['role', 'action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'telepon' => ['required', 'string', 'unique:users', 'regex:/^\d{10,12}$/'],
            'jenkel' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tmpt_lahir' => 'required|string',
            'role' => 'required|exists:roles,id',
        ]);

        $userData = $request->only(['name', 'email', 'password', 'jenkel', 'tgl_lahir', 'tmpt_lahir']);
        $userData['password'] = Hash::make($request->input('password'));

        $user = User::create($userData);
        $user->assignRole($request->input('role'));

        if ($user->hasRole('Pemilik')) {
            $user->pemilik()->create([
                'name' => $user->name,
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
                'created_by' => 'System',
                'updated_by' => 'System',
            ]);
        }

        if ($user->hasRole('Penyewa')) {
            $user->penyewa()->create([
                'no_ktp' => $request->input('no_ktp'),
                'name' => $request->input('name'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
                'created_by' => 'System',
                'updated_by' => 'System',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User, Pemilik, and Penyewa created successfully.'
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('pages.users.show', compact('user', 'roles'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->first();

        return view('pages.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'telepon' => ['required', 'string', 'unique:users,telepon,' . $id, 'regex:/^\d{10,12}$/'],
            'jenkel' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tmpt_lahir' => 'required|string',
            'role' => 'required|exists:roles,id',
        ]);

        // Simpan informasi updated_by
        $userData = $request->only(['name', 'no_ktp', 'alamat', 'email', 'jenkel', 'tgl_lahir', 'tmpt_lahir', 'telepon']);
        $userData['updated_by'] = Auth::user()->name;
        $user->update($userData);

        // Simpan data Pemilik jika diperlukan
        if ($user->hasRole('Pemilik')) {
            $pemilikData = [
                'name' => $request->input('name'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
                'updated_by' => Auth::user()->name,
            ];
            $user->pemilik()->updateOrCreate(['id_pemilik' => $user->id], $pemilikData);
        } else {
            $user->pemilik()->where('id_pemilik', $user->id)->delete();
        }

        // Simpan data Penyewa jika diperlukan
        if ($user->hasRole('Penyewa')) {
            $penyewaData = [
                'no_ktp' => $request->input('no_ktp'),
                'name' => $request->input('name'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
                'updated_by' => Auth::user()->name,
            ];
            $user->penyewa()->updateOrCreate(['id_penyewa' => $user->id], $penyewaData);
        } else {
            $user->penyewa()->where('id_penyewa', $user->id)->delete();
        }

        // Sinkronisasi peran pengguna
        $user->syncRoles([$request->input('role')]);

        return response()->json([
            'success' => true,
            'message' => 'User, Pemilik, and Penyewa updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'User successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete user'
        ]);
    }
}
