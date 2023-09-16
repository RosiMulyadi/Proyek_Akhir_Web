<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:list-stores|create-stores|edit-stores|delete-stores', ['only' => ['index', 'store']]);
        $this->middleware('permission:create-stores', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-stores', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-stores', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Store::all(); // Mengambil semua kolom
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('gambar', function ($row) {
                    return asset('storage/' . $row->gambar);
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('stores.edit', $row->id) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>
                              <button class="btn btn-danger delete-btn" data-id="' . $row->id . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawColumns(['gambar', 'action'])
                ->toJson();
        }

        return view('pages.stores.index'); // Pastikan view sudah sesuai dengan kebutuhan Anda
    }

    public function create()
    {
        return view('pages.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_toko' => 'required|string|unique:stores,id_toko',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'cluster' => 'required|string',
            'harga' => 'required|string',
        ]);

        $storeData = $request->all();
        // Mengambil data input kecuali 'gambar'
        $storeData = $request->except('gambar');

        // Mengelola file gambar jika ada
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $storeData['gambar'] = $gambarPath;
        }

        // Set created_by berdasarkan nama pengguna yang terautentikasi
        $storeData['created_by'] = Auth::user()->name;

        // Simpan data ke dalam database
        $store = Store::create($storeData);

        return response()->json(['success' => true, 'message' => 'Store created successfully']);
    }

    public function show($id)
    {
        $store = Store::find($id);

        return view('pages.stores.show', compact('store'));
    }

    public function edit($id)
    {
        $store = Store::find($id);

        return view('pages.stores.edit', compact('store'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_toko' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'cluster' => 'required|string',
            'harga' => 'required|string',
        ]);

        $storeData = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $StoreData['gambar'] = $gambarPath;
        }

        $store = Store::findOrFail($id);
        $store->update($storeData);

        $store->updated_by = Auth::user()->name;
        $store->save();

        return response()->json(['success' => true, 'message' => 'Store updated successfully']);
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);

        if ($store->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Store successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Store to delete produk'
        ]);
    }
}
