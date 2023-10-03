@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Penyewa</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Penyewa</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="form-group">
                <a href="{{ route('penyewa.index') }}" class="btn btn-primary">Back</a>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="id_penyewa">Id Penyewa:</label>
                    <input type="text" name="id_penyewa" class="form-control" value="{{ $penyewa->id_penyewa }}" readonly>
                </div>
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input type="text" name="nama" class="form-control" value="{{ $penyewa->nama }}" readonly>
                </div>
                <div class="form-group">
                    <label for="no_ktp">No. KTP:</label>
                    <input type="text" name="no_ktp" class="form-control" value="{{ $penyewa->no_ktp }}" readonly>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat:</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $penyewa->alamat }}" readonly>
                </div>
                <div class="form-group">
                    <label for="telepon">Telepon:</label>
                    <input type="text" name="telepon" class="form-control" value="{{ $penyewa->telepon }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection