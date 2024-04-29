<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Pemilik;
use App\Models\User; // Tambahkan penggunaan model User
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    // function __construct()
    // {
    //     $this->middleware('permission:list-stores|create-stores|edit-stores|delete-stores', ['only' => ['index', 'store']]);
    //     $this->middleware('permission:create-stores', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit-stores', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete-stores', ['only' => ['destroy']]);
    // }
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
                          <a href="' . route('stores.show', $row->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>
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
        $pemilik = Pemilik::with('user')->get(); // Mengambil pemilik beserta informasi user terkait
        return view('pages.stores.create', compact('pemilik'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_pemilik' => 'required|string|exists:pemilik,id_pemilik',
            'id_toko' => 'required|string|unique:store,id_toko',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'cluster' => 'required|string',
            'harga' => 'required|string',
        ], [
            'id_pemilik.exists' => 'The selected id pemilik is invalid. Please choose a valid id pemilik.',
        ]);

        // Dapatkan user id dari id pemilik yang dipilih
        $userId = Pemilik::findOrFail($request->input('id_pemilik'))->user_id;

        // Buat entri toko
        $store = new Store();
        $store->user_id = $userId;
        $store->id_toko = $request->input('id_toko');
        $store->alamat = $request->input('alamat');
        $store->luas_bangunan = $request->input('luas_bangunan');
        $store->cluster = $request->input('cluster');
        $store->harga = $request->input('harga');

        // Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $store->gambar = $gambarPath;
        }

        // Simpan entri toko
        $store->save();

        // Berikan respons JSON sukses
        return response()->json(['success' => true, 'message' => 'Store created successfully']);
    }

    public function show($id)
    {
        $store = Store::find($id);
        $pemilik = Pemilik::all();
        return view('pages.stores.show', compact('store', 'pemilik'));
    }

    public function edit($id)
    {
        $store = Store::find($id);
        $pemilik = Pemilik::with('user')->get(); // Mengambil pemilik beserta informasi user terkait
        return view('pages.stores.edit', compact('store', 'pemilik'));
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
            'id_pemilik' => 'required|exists:pemilik,id_pemilik', // Validation for id_pemilik
        ]);

        $storeData = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $storeData['gambar'] = $gambarPath;
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
            'message' => 'Failed to delete store'
        ]);
    }
}
