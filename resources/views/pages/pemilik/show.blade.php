@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Pemilik</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pemilik</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="form-group">
                <a href="{{ route('pemilik.index') }}" class="btn btn-primary">Back</a>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="id_pemilik">Id Pemilik:</label>
                    <input type="text" name="id_pemilik" class="form-control" value="{{ $pemilik->id_pemilik }}" readonly>
                </div>
                <div class="form-group">
                    <label for="name">Nama:</label>
                    <input type="text" name="name" class="form-control" value="{{ $pemilik->name }}" readonly>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat:</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $pemilik->alamat }}" readonly>
                </div>
                <div class="form-group">
                    <label for="no_hp">No. Hp:</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ $pemilik->no_hp }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection