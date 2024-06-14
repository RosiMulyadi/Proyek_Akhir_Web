<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil pengguna dengan peran (role) sebagai Penyewa
            $data = User::whereHas('roles', function ($query) {
                $query->where('name', 'Penyewa');
            })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id_penyewa', function ($row) {
                    return $row->id; // Kembalikan nilai ID langsung
                })
                ->addColumn('no_ktp', function ($row) {
                    return $row->no_ktp;
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
                ->addColumn('action', function ($row) {
                    $route = route('stores.index');
                    return '<a href="'.$route.'" class="btn btn-primary">Lihat Store</a>';
                })
                ->rawColumns(['id_penyewa', 'action']) // Tentukan kolom ini sebagai raw HTML agar hyperlink dapat ditampilkan dengan benar
                ->toJson();
        }

        return view('pages.penyewa.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('pages.penyewa.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'no_ktp' => 'required|string|unique:users,no_ktp',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            'user_id' => 'required|exists:users,id', // Memastikan user_id yang dipilih ada di tabel users
        ]);

        $user = Auth::user();

        $penyewaData = $request->all();
        $penyewaData['created_by'] = $user->name;

        // Membuat user baru dengan informasi penyewa
        $newUser = User::create([
            'name' => $request->name,
            'no_ktp' => $request->no_ktp,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        // Mengaitkan user baru dengan user_id yang diberikan
        $newUser->update(['user_id' => $request->user_id]);

        return response()->json(['success' => true, 'message' => 'Penyewa created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $user = User::all();
        return view('pages.penyewa.show', compact('user', 'penyewa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $users = User::all();
        return view('pages.penyewa.edit', compact('penyewa', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_penyewa' => 'required|string',
            'nama' => 'required|string',
            'no_ktp' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
        ]);

        // Mengambil data pengguna yang terautentikasi
        $user = Auth::user();
        $penyewaData = $request->all();

        $penyewa = Penyewa::findOrFail($id);
        $penyewa->update($penyewaData);

        $penyewa->updated_by = Auth::user()->name;
        $penyewa->save();

        return response()->json(['success' => true, 'message' => 'Penyewa updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $penyewa = Penyewa::findOrFail($id);

        if ($penyewa->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Penyewa successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Penyewa to delete produk'
        ]);
    }
}
