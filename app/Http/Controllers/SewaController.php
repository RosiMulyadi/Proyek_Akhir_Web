<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Pemilik;
use App\Models\Penyewa;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            $data = Sewa::with('pemilik', 'penyewa', 'store')->get();
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
                    $actionBtn = '<a href="' . route('sewa.edit', $row->id) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>
                              <a href="' . route('sewa.show', $row->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>
                              <button class="btn btn-danger delete-btn" data-id="' . $row->id . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawColumns(['gambar', 'action', 'cluster'])
                ->toJson();
        }
        return view('pages.sewa.index'); // Ensure this view matches your requirements
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pemilik = Pemilik::all();
        $penyewa = Penyewa::all();
        $store = Store::all();
        return view('pages.sewa.create', compact('pemilik', 'penyewa', 'store'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_pemilik' => 'required|string|exists:pemilik,id_pemilik',
            'id_penyewa' => 'required|string|exists:penyewa,id_penyewa',
            'id_toko' => 'required|string|exists:stores,id_toko',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alamat' => 'required|string',
            'luas_bangunan' => 'required|string',
            'harga' => 'required|string',
        ]);

        // Retrieve the Pemilik and Penyewa models based on the provided ids
        $pemilik = Pemilik::where('id_pemilik', $request->input('id_pemilik'))->first();
        $penyewa = Penyewa::where('id_penyewa', $request->input('id_penyewa'))->first();
        $store = Store::where('id_toko', $request->input('id_toko'))->first();

        // Prepare sewa data
        $sewaData = $request->except('gambar', 'cluster');

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar', 'public');
            $sewaData['gambar'] = $gambarPath;
        }

        // Set metadata fields
        $sewaData['created_by'] = Auth::user()->name;

        // Get the cluster map URL
        $cluster = $request->input('cluster');
        $mapUrl = $cluster === 'Sumenep' ? 'https://maps.example.com/sumenep' : 'https://maps.example.com/default';

        // Add the map URL to sewa data
        $sewaData['cluster'] = $mapUrl;

        // Create the Sewa entry and associate it with the Pemilik and Penyewa models
        $sewa = new Sewa($sewaData);
        $sewa->pemilik()->associate($pemilik);
        $sewa->penyewa()->associate($penyewa);
        $sewa->store()->associate($store);
        $sewa->id_pemilik = $request->input('id_pemilik'); // Update the id_pemilik field
        $sewa->id_penyewa = $request->input('id_penyewa'); // Update the id_penyewa field
        $sewa->id_toko = $request->input('id_toko'); // Update the id_toko field
        $sewa->save();

        return response()->json(['success' => true, 'message' => 'Sewa created successfully.', 'sewa' => $sewa, 'mapUrl' => $mapUrl]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sewa $sewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
