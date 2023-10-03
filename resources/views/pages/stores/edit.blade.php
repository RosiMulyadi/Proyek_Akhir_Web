@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Stores</h1>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="editStoreForm" method="POST" action="{{ route('stores.update', $store->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_toko">Id Toko:</label>
                        <input type="text" name="id_toko" class="form-control" value="{{ $store->id_toko }}" required>
                        <span class="text-danger" id="id_toko_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar:</label>
                        <input type="file" id="gambarInput" name="gambar" class="form-control-file">
                        <span class="text-danger" id="gambar_error"></span>
                        @if($store->gambar)
                        <div class="mt-2">
                            <img id="gambarPreview" src="{{ asset('storage/' . $store->gambar) }}" alt="Gambar" style="max-width: 200px;">
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ $store->alamat }}" required>
                        <span id="alamat_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="luas_bangunan">Luas Bangunan:</label>
                        <input type="text" name="luas_bangunan" class="form-control" value="{{ $store->luas_bangunan }}" required>
                        <span class="text-danger" id="luas_bangunan_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster:</label>
                        <input type="text" name="cluster" class="form-control" value="{{ $store->cluster }}" required>
                        <span class="text-danger" id="cluster_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="text" name="harga" class="form-control" value="{{ $store->harga }}" required>
                        <span class="text-danger" id="harga_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editStoreBtn" class="btn btn-primary">Update Store</button>
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="application/javascript">
    $("#gambarInput").on('change', function(e) {
        var gambarInput = e.target;
        if (gambarInput.files && gambarInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#gambarPreview').attr('src', e.target.result);
                $('#gambarPreview').css('display', 'block');
            }

            reader.readAsDataURL(gambarInput.files[0]);
        }
    });
    $("#editStoreForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editStoreBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        var formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#id_toko_error').text('');
        $('#gambar_error').text('');
        $('#alamat_error').text('');
        $('#luas_bangunan_error').text('');
        $('#cluster_error').text('');
        $('#harga_error').text('');

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Store Updated',
                        text: 'Store Berhasil Diupdate.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('stores.index') }}";
                    });
                } else {
                    if (response.errors) {
                        // Handle error fields
                        // ...
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'An error occurred while updating the store.',
                        confirmButtonText: 'OK'
                    });
                }

                btn.attr('disabled', false);
                btn.val("Update Store");
            },
            error: function(xhr, status, error) {
                // Handle error cases
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'An error occurred while updating the store.',
                    confirmButtonText: 'OK'
                });

                btn.attr('disabled', false);
                btn.val("Update Store");
            }
        });
    });
</script>
@endsection