<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PemilikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil pengguna dengan peran (role) sebagai Pemilik
            $data = User::whereHas('roles', function ($query) {
                $query->where('name', 'Pemilik');
            })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id_pemilik', function ($row) {
                    return $row->id; // Langsung kembalikan nilai ID pengguna
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('alamat', function ($row) {
                    // Kembalikan alamat sebagai string kosong jika tidak ada alamat yang tersedia
                    return $row->alamat ?? '';
                })
                ->addColumn('telepon', function ($row) {
                    // Kembalikan telepon sebagai string kosong jika tidak ada telepon yang tersedia
                    return $row->telepon ?? '';
                })
                ->toJson();
        }

        return view('pages.pemilik.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('pages.pemilik.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pemilik' => 'required|string|unique:pemilik,id_pemilik',
            'name' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string|unique:pemilik,telepon',
        ]);

        $pemilikData = $request->except('_token');

        // Mencari user berdasarkan nama yang diberikan
        $user = User::where('name', $request->input('name'))->first();

        // Jika user ditemukan, gunakan id user tersebut
        if ($user) {
            $pemilikData['name'] = $user->name;
            $pemilikData['alamat'] = $user->alamat;
            $pemilikData['telepon'] = $user->telepon;
        } else {
            // Jika user tidak ditemukan, buat user baru
            $user = User::create([
                'name' => $request->input('name'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
            ]);
        }

        // Set created_by based on the authenticated user's name
        $pemilikData['created_by'] = Auth::user()->name;

        Pemilik::create($pemilikData);

        return response()->json(['success' => true, 'message' => 'Pemilik created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pemilik = Pemilik::findOrFail($id);
        $user = User::findOrFail($pemilik->user_id);
        return view('pages.pemilik.show', compact('user', 'pemilik'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pemilik = Pemilik::findOrFail($id);
        $user = User::findOrFail($pemilik->user_id);
        return view('pages.pemilik.edit', compact('pemilik', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pemilik' => 'required|string',
            'name' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
        ]);

        $pemilikData = $request->except('_token');

        // Mencari user berdasarkan nama yang diberikan
        $user = User::where('name', $request->input('name'))->first();

        // Jika user ditemukan, gunakan id user tersebut
        if ($user) {
            $pemilikData['user_id'] = $user->id;
        } else {
            // Jika user tidak ditemukan, buat user baru
            $user = User::create([
                'name' => $request->input('name'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
            ]);
            $pemilikData['user_id'] = $user->id;
        }

        $pemilik = Pemilik::findOrFail($id);
        $pemilik->update($pemilikData);

        $pemilik->updated_by = Auth::user()->name;
        $pemilik->save();

        return response()->json(['success' => true, 'message' => 'Pemilik updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pemilik = Pemilik::findOrFail($id);

        if ($pemilik->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Pemilik successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete Pemilik'
        ]);
    }
}
