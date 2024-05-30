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
        $request->validate([
            'name' => 'required|string',
            'alamat' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $id,
            'telepon' => 'required|string|unique:users,telepon,' . $id,
            'jenkel' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tmpt_lahir' => 'required|string',
            'role' => 'required|exists:roles,id',
        ]);

        // Get User data
        $user = User::findOrFail($id);

        // Update User data
        $userData = $request->except(['_token', '_method', 'role']);

        if ($request->has('password') && !empty($request->input('password'))) {
            $userData['password'] = Hash::make($request->input('password'));
        }

        $userData['updated_by'] = Auth::user()->name;
        $user->update($userData);

        // Update role
        $user->syncRoles([$request->input('role')]);

        // Check role and update corresponding related data
        if ($user->hasRole('Pemilik')) {
            $pemilik = Pemilik::where('id_pemilik', $user->id)->first();
            if (!$pemilik) {
                $pemilik = new Pemilik();
                $pemilik->id_pemilik = $user->id; // Ensure the relationship between user and pemilik is set correctly
            }
            $pemilikData = [
                'name' => $request->name,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'updated_by' => Auth::user()->name,
            ];
            $pemilik->fill($pemilikData)->save();

            // Remove Penyewa data if previously set
            Penyewa::where('id_penyewa', $user->id)->delete();
        } elseif ($user->hasRole('Penyewa')) {
            $penyewa = Penyewa::where('id_penyewa', $user->id)->first();
            if (!$penyewa) {
                $penyewa = new Penyewa();
                $penyewa->id_penyewa = $user->id; // Ensure the relationship between user and penyewa is set correctly
            }
            $penyewaData = [
                'no_ktp' => $request->no_ktp,
                'name' => $request->name,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'updated_by' => Auth::user()->name,
            ];
            $penyewa->fill($penyewaData)->save();

            // Remove Pemilik data if previously set
            Pemilik::where('id_pemilik', $user->id)->delete();
        } else {
            // Remove Pemilik and Penyewa data if role is neither Pemilik nor Penyewa
            Pemilik::where('id_pemilik', $user->id)->delete();
            Penyewa::where('id_penyewa', $user->id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'User and related data updated successfully.']);
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
