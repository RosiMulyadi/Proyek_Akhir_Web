@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Company</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Company</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="createCompanyForm" method="POST" action="{{ route('stores.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="id_toko">Id Toko:</label>
                        <input type="text" name="id_toko" class="form-control" required>
                        <span class="text-danger" id="id_toko_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar:</label>
                        <input type="file" name="gambar" class="form-control-file" id="gambarInput" required>
                        <span class="text-danger" id="gambar_error"></span>
                    </div>
                    <div class="form-group">
                        <img id="gambarPreview" src="#" alt="Gambar Preview" style="max-height: 200px; display: none;">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" required>
                        <span id="alamat_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="luas_bangunan">Luas Bangunan:</label>
                        <input type="text" name="luas_bangunan" class="form-control" required>
                        <span class="text-danger" id="luas_bangunan_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster:</label>
                        <input type="text" name="cluster" class="form-control" required>
                        <span class="text-danger" id="cluster_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="text" name="harga" class="form-control" required>
                        <span class="text-danger" id="harga_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createCompanyBtn" class="btn btn-primary">Create Store</button>
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
    $("#createCompanyForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#createCompanyBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        let formData = new FormData(this);
        $('#id_toko_error').text('');
        $('#gambar_error').text('');
        $('#alamat_error').text('');
        $('#luas_bangunan_error').text('');
        $('#cluster_error').text('');
        $('#harga_error').text('');

        $.ajax({
            url: "{{ route('stores.store') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                $(".preloader").fadeOut();
                if (response.success) {
                    sessionStorage.setItem('success', response.message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Store Created',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('stores.index') }}";
                    });
                }
            },
            error: function(response) {
                btn.attr('disabled', false);
                btn.val("Simpan");

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the store.',
                    confirmButtonText: 'OK'
                });

                $('#id_toko_error').text(response.responseJSON.errors.id_toko);
                $('#gambar_error').text(response.responseJSON.errors.gambar);
                $('#alamat_error').text(response.responseJSON.errors.alamat);
                $('#luas_bangunan_error').text(response.responseJSON.errors.luas_bangunan);
                $('#cluster_error').text(response.responseJSON.errors.cluster);
                $('#harga_error').text(response.responseJSON.errors.harga);
            }
        });
    });
</script>
@endsection