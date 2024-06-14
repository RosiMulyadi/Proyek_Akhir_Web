<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Store;
use App\Models\User;
use App\Policies\PembayaranPolicy;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:list-pembayaran|create-pembayaran|edit-pembayaran|delete-pembayaran', ['only' => ['index', 'store']]);
    //     $this->middleware('permission:create-pembayaran', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit-pembayaran', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete-pembayaran', ['only' => ['destroy']]);
    //     $this->middleware('permission:updateStatus-pembayaran', ['only' => ['updateStatus']]);
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pembayaran::with('penyewa', 'Store', 'user')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bayar', function ($row) {
                    return asset('storage/' . $row->bayar);
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();

                    $editBtn = optional($user)->can('editPembayaran', $row)
                        ? '<a href="' . route('pembayaran.edit', $row->id_bayar) . '" class="btn btn-warning"><i class="fas fa-pen-square fa-circle mt-2"></i></a>'
                        : '';

                    $deleteBtn = optional($user)->can('deletePembayaran', $row)
                        ? '<button class="btn btn-danger delete-btn" data-id="' . $row->id_bayar . '" onclick="deleteItem(this)"><i class="fas fa-trash"></i></button>'
                        : '';

                    return $editBtn . $deleteBtn;
                })
                ->addColumn('keterangan', function ($row) {
                    if ($row->keterangan == '0') {
                        return '<span class="badge badge-info text-white">Pending</span>';
                    } elseif ($row->keterangan == '1') {
                        return '<span class="badge badge-warning text-white">Processing</span>';
                    } elseif ($row->keterangan == '2') {
                        return '<span class="badge badge-success text-white">Completed</span>';
                    } elseif ($row->keterangan == '3') {
                        return '<span class="badge badge-danger text-white">Rejected</span>';
                    }
                })
                ->rawColumns(['bayar', 'action', 'keterangan'])
                ->toJson();
        }

        // $pembayaranCount = Pembayaran::count();
        return view('pages.bayar.index');
    }

    public function create()
    {
        // $this->authorize('createPembayaran', Pembayaran::class);
        $penyewa = Penyewa::all();
        $store = Store::all();
        $user = User::all();
        return view('pages.bayar.create', compact('penyewa', 'store', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_bayar' => 'required|string|unique:pembayaran,id_bayar',
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'id_Store' => 'required|exists:Stores,id_toko',
            'nama_penyewa' => 'required|exists:users,name',
            'harga' => 'required|numeric',
            'bayar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        $pembayaranData = $request->except('bayar');

        if ($request->hasFile('bayar')) {
            $bayarPath = $request->file('bayar')->store('bayar', 'public');
            $pembayaranData['bayar'] = $bayarPath;
        }

        $pembayaranData['created_by'] = Auth::user()->name;

        Pembayaran::create($pembayaranData);

        return response()->json(['success' => true, 'message' => 'Pembayaran created successfully']);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with('penyewa', 'Store', 'user')->find($id);
        return view('pages.bayar.show', compact('pembayaran'));
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::find($id);
        // $this->authorize('editPembayaran', $pembayaran);
        $penyewa = Penyewa::all();
        $store = Store::all();
        $user = User::all();
        return view('pages.bayar.edit', compact('pembayaran', 'penyewa', 'store', 'user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_bayar' => 'required|string',
            'id_penyewa' => 'required|exists:penyewa,id',
            'id_Store' => 'required|exists:Stores,id',
            'nama_penyewa' => 'required|exists:users,name',
            'harga' => 'required|numeric',
            'bayar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        // $this->authorize('updatePembayaran', $pembayaran);

        $pembayaranData = $request->except('bayar');

        if ($request->hasFile('bayar')) {
            $bayarPath = $request->file('bayar')->store('bayar', 'public');
            $pembayaranData['bayar'] = $bayarPath;
        }

        $pembayaran->update($pembayaranData);

        return response()->json(['success' => true, 'message' => 'Pembayaran updated successfully']);
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        // $this->authorize('deletePembayaran', $pembayaran);

        if ($pembayaran->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete Pembayaran'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        // $this->authorize('updateStatusPembayaran', $pembayaran);

        // Validate the request if necessary

        // Assuming that the 'keterangan' comes from the request
        $newStatus = $request->input('keterangan');

        // Update the keterangan based on the new status
        if ($newStatus === 'Processing') {
            $pembayaran->keterangan = '1';
        } elseif ($newStatus === 'Completed') {
            $pembayaran->keterangan = '2';
        } elseif ($newStatus === 'Rejected') {
            $pembayaran->keterangan = '3';
        } else {
            $pembayaran->keterangan = '0';
        }

        $pembayaran->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
