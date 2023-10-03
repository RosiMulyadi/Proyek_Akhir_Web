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
            $data = Penyewa::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('penyewa.edit', $row->id) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>
                      <a href="' . route('penyewa.show', $row->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>
                      <button class="btn btn-danger delete-btn" data-id="' . $row->id . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('pages.penyewa.index'); // Pastikan view sudah sesuai dengan kebutuhan Anda
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
            'id_penyewa' => 'required|string|unique:penyewa,id_penyewa',
            'nama' => 'required|string',
            'no_ktp' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
        ]);

        $user = Auth::user();

        // Pastikan semua validasi telah dilakukan sebelum mencoba menyimpan data
        $penyewaData = $request->all();
        $penyewaData['created_by'] = $user->name; // Set created_by berdasarkan nama pengguna yang terautentikasi

        // Simpan data ke dalam database
        $penyewa = Penyewa::create($penyewaData);

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
