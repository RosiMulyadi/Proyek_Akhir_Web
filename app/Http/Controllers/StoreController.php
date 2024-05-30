<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Pemilik;
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
    public function getClusterCoordinates(Request $request)
    {
        $clusterName = $request->input('cluster');

        // Example data - replace this with your actual data source
        $coordinates = $this->findCoordinatesByClusterName($clusterName);

        if ($coordinates) {
            return response()->json(['lat' => $coordinates['lat'], 'lng' => $coordinates['lng']]);
        } else {
            return response()->json(['error' => 'Cluster not found'], 404);
        }
    }

    private function findCoordinatesByClusterName($clusterName)
    {
        // Dummy data for demonstration - replace this with actual data fetching logic
        $clusters = [
            'Cluster A' => ['lat' => -7.95, 'lng' => 113.85],
            'Cluster B' => ['lat' => -7.96, 'lng' => 113.86],
            // Add more clusters as needed
        ];

        return $clusters[$clusterName] ?? null;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Store::with('pemilik')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('gambar', function ($row) {
                    return asset('storage/' . $row->gambar);
                })
                ->addColumn('cluster', function ($row) {
                    $cluster = $row->cluster;
                    $mapUrl = match ($cluster) {
                        'Sumenep' => 'https://maps.example.com/sumenep', // Replace with your map URL
                        'default' => 'https://maps.example.com/default', // Replace with your default map URL
                        default => 'https://maps.example.com/default', // Replace with your default map URL
                    };
                    return "<div id='map-" . $row->id . "' style='width: 100px; height: 100px;'></div><script>var map = L.map('map-" . $row->id . "').setView([0, 0], 4); L.tileLayer('" . $mapUrl . "', {
                    attribution: '&copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a>',
                    maxZoom: 18,
                }).addTo(map);</script>";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('stores.edit', $row->id) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>
                              <a href="' . route('stores.show', $row->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>
                              <button class="btn btn-danger delete-btn" data-id="' . $row->id . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawColumns(['gambar', 'action', 'id_pemilik', 'cluster'])
                ->toJson();
        }
        return view('pages.stores.index'); // Pastikan view sudah sesuai dengan kebutuhan Anda
    }

    public function create()
    {
        $pemilik = Pemilik::all();
        return view('pages.stores.create', compact('pemilik'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_pemilik' => 'required|string|exists:pemilik,id_pemilik',
            'id_toko' => 'required|string|unique:stores,id_toko',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'harga' => 'required|string',
        ]);

        // Retrieve the Pemilik model based on the provided id_pemilik value
        $pemilik = Pemilik::where('id_pemilik', $request->input('id_pemilik'))->first();

        // Prepare store data
        $storeData = $request->except('gambar', 'cluster');

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $storeData['gambar'] = $gambarPath;
        }

        // Set metadata fields
        $storeData['created_by'] = Auth::user()->name;

        // Get the cluster map URL
        $cluster = $request->input('cluster');
        $mapUrl = $cluster === 'Sumenep' ? 'https://maps.example.com/sumenep' : 'https://maps.example.com/default';

        // Add the map URL to store data
        $storeData['cluster'] = $mapUrl;

        // Create the Store entry and associate it with the Pemilik model
        $store = new Store($storeData);
        $store->pemilik()->associate($pemilik);
        $store->id_pemilik = $request->input('id_pemilik'); // Update the id_pemilik field
        $store->save();

        return response()->json(['success' => true, 'message' => 'Store created successfully.', 'store' => $store, 'mapUrl' => $mapUrl]);
    }

    public function show($id)
    {
        $store = Store::with('pemilik')->find($id);
        return view('pages.stores.show', compact('store'));
    }

    public function edit($id)
    {
        $store = Store::find($id);
        $pemilik = Pemilik::with('user')->get(); // Mengambil pemilik beserta informasi user terkait
        return view('pages.stores.edit', compact('store', 'pemilik'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'id_pemilik' => 'required|string|exists:pemilik,id_pemilik',
            'id_toko' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'harga' => 'required|string',
        ]);

        // Retrieve the Store model based on the provided id
        $store = Store::findOrFail($id);

        // Retrieve the Pemilik model based on the provided id_pemilik value
        $pemilik = Pemilik::where('id_pemilik', $request->input('id_pemilik'))->first();

        // Prepare store data
        $storeData = $request->except('gambar', 'cluster');

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $storeData['gambar'] = $gambarPath;
        }

        // Set metadata fields
        $storeData['updated_by'] = Auth::user()->name;

        // Get the cluster map URL
        $cluster = $request->input('cluster');
        $mapUrl = $cluster === 'Sumenep' ? 'https://maps.example.com/sumenep' : 'https://maps.example.com/default';

        // Add the map URL to store data
        $storeData['cluster'] = $mapUrl;

        // Update the Store entry and associate it with the Pemilik model
        $store->update($storeData);
        $store->pemilik()->associate($pemilik);
        $store->id_pemilik = $request->input('id_pemilik'); // Update the id_pemilik field
        $store->save();

        return response()->json(['success' => true, 'message' => 'Store updated successfully.', 'store' => $store, 'mapUrl' => $mapUrl]);
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
