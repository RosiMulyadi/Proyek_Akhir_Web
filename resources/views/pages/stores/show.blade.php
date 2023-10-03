@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Stores Details</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Stores</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('stores.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <img src="{{ asset('storage/' . $store->gambar) }}" alt="Store Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="id_toko">ID Toko:</label>
                        <input type="text" name="id_toko" class="form-control" value="{{ $store->id_toko }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $store->alamat }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="luas_bangunan">Luas Bangunan:</label>
                        <input type="text" name="luas_bangunan" class="form-control" value="{{ $store->luas_bangunan }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="text" name="harga" class="form-control" value="{{ $store->harga }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster:</label>
                        <input type="text" name="cluster" class="form-control" value="{{ $store->cluster }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    function formatRupiah(angka) {
        var number_string = angka.toString();
        var split = number_string.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    var hargaInput = document.querySelector('input[name="harga"]');
    if (hargaInput) {
        var harga = parseInt(hargaInput.value);
        hargaInput.value = 'Rp ' + formatRupiah(harga);
    }
</script>
@endsection