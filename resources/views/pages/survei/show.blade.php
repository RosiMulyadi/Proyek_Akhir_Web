@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Survei</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Detail Survei</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <a href="{{ route('survei.index') }}" class="btn btn-secondary">Back</a>
                </div>
                <div class="form-group">
                    <label for="id_penyewa">ID Penyewa:</label>
                    <input type="text" id="id_penyewa" class="form-control" value="{{ $survei->id_penyewa }}" readonly>
                </div>
                <div class="form-group">
                    <label for="nama_penyewa">Nama Penyewa:</label>
                    <input type="text" id="nama_penyewa" class="form-control" value="{{ $survei->nama_penyewa }}" readonly>
                </div>
                <div class="form-group">
                    <label for="no_ktp">Nomor KTP:</label>
                    <input type="text" id="no_ktp" class="form-control" value="{{ $survei->no_ktp }}" readonly>
                </div>
                <div class="form-group">
                    <label for="tanggal_survei">Tanggal Survei:</label>
                    <input type="date" id="tanggal_survei" class="form-control" value="{{ $survei->tanggal_survei }}" readonly>
                </div>
                <div class="form-group">
                    <label for="waktu">Waktu:</label>
                    <input type="time" id="waktu" class="form-control" value="{{ $survei->waktu }}" readonly>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan:</label>
                    <textarea id="keterangan" class="form-control" readonly>{{ $survei->keterangan }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
