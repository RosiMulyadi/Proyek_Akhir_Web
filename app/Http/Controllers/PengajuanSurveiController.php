<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSurvei;
use App\Models\Penyewa;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PengajuanSurveiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PengajuanSurvei::with('penyewa')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('survei.edit', $row->id) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>
                              <a href="' . route('survei.show', $row->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>
                              <button class="btn btn-danger delete-btn" data-id="' . $row->id . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'id_penyewa', 'nama_penyewa', 'no_ktp'])
                ->toJson();
        }
        return view('pages.survei.index'); // Pastikan view sudah sesuai dengan kebutuhan Anda
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penyewa = Penyewa::all();
        return view('pages.survei.create', compact('penyewa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'nama_penyewa' => 'required|exists:penyewa,name',
            'no_ktp' => 'required|exists:penyewa,no_ktp',
            'tanggal_survei' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'required|string',
        ]);

        // Create a new PengajuanSurvei instance with the validated data
        $surveiData = $request->all();

        // Retrieve the Penyewa model based on the provided id_penyewa value
        $penyewa = Penyewa::where('id_penyewa', $request->input('id_penyewa'))
            ->where('name', $request->input('nama_penyewa'))
            ->where('no_ktp', $request->input('no_ktp'))
            ->first();

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The id penyewa field is required.'], 400);
        }

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The nama penyewa field is required.'], 400);
        }

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The no ktp field is required.'], 400);
        }

        // Associate the retrieved models with the PengajuanSurvei
        $surveiData['id_penyewa'] = $penyewa->id_penyewa;
        $surveiData['nama_penyewa'] = $penyewa->name;
        $surveiData['no_ktp'] = $penyewa->no_ktp;
        $surveiData['created_by'] = Auth::user()->name;

        // Create the PengajuanSurvei record
        $pengajuanSurvei = PengajuanSurvei::create($surveiData);

        return response()->json(['success' => true, 'message' => 'Pengajuan survei created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $survei = PengajuanSurvei::find($id);
        return view('pages.survei.show', compact('survei'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $survei = PengajuanSurvei::find($id);
        $penyewa = Penyewa::all();
        return view('pages.survei.edit', compact('survei', 'penyewa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'nama_penyewa' => 'required|exists:penyewa,name',
            'no_ktp' => 'required|exists:penyewa,no_ktp',
            'tanggal_survei' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'required|string',
        ]);

        // Find the PengajuanSurvei record based on the provided ID
        $survei = PengajuanSurvei::findOrFail($id);

        // Retrieve the Penyewa model based on the provided id_penyewa value
        $penyewa = Penyewa::where('id_penyewa', $request->input('id_penyewa'))
            ->where('name', $request->input('nama_penyewa'))
            ->where('no_ktp', $request->input('no_ktp'))
            ->first();

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The id penyewa field is required.'], 400);
        }

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The nama penyewa field is required.'], 400);
        }

        if (!$penyewa) {
            return response()->json(['success' => false, 'message' => 'The no ktp field is required.'], 400);
        }

        // Update the PengajuanSurvei instance with the validated data
        $surveiData = $request->all();

        // Ensure the related fields are correctly associated with the Penyewa model
        $surveiData['id_penyewa'] = $penyewa->id_penyewa;
        $surveiData['nama_penyewa'] = $penyewa->name;
        $surveiData['no_ktp'] = $penyewa->no_ktp;
        $surveiData['updated_by'] = Auth::user()->name;

        // Update the PengajuanSurvei record
        $survei->update($surveiData);

        return response()->json(['success' => true, 'message' => 'Survei updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $survei = PengajuanSurvei::findOrFail($id);

        if ($survei->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Survei successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Survei to delete produk'
        ]);
    }
}
