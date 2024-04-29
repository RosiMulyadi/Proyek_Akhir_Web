@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Pemilik</h1>
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
            <div class="card-body">
                <form id="createPemilikForm" method="POST" action="{{ route('pemilik.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="id_pemilik">Id Pemilik:</label>
                        <input type="text" name="id_pemilik" class="form-control" required>
                        <span class="text-danger" id="id_pemilik_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" name="name" class="form-control" required>
                        <span class="text-danger" id="name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" required>
                        <span class="text-danger" id="alamat_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="telepon">No HP:</label>
                        <input type="text" name="telepon" class="form-control" required>
                        <span class="text-danger" id="telepon_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createPemilikBtn" class="btn btn-primary">Create Pemilik</button>
                        <a href="{{ route('pemilik.index') }}" class="btn btn-secondary">Cancel</a>
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
    $("#createPemilikForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#createPemilikBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        let formData = new FormData(this);
        $('#id_pemilik_error').text('');
        $('#name_error').text('');
        $('#alamat_error').text('');
        $('#telepon_error').text('');

        $.ajax({
            url: "{{ route('pemilik.store') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    sessionStorage.setItem('success', response.message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Pemilik Created',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('pemilik.index') }}";
                    });
                }
            },
            error: function(response) {
                btn.attr('disabled', false);
                btn.val("Simpan");

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the pemilik.',
                    confirmButtonText: 'OK'
                });

                $('#id_pemilik_error').text(response.responseJSON.errors.id_pemilik);
                $('#name_error').text(response.responseJSON.errors.name);
                $('#alamat_error').text(response.responseJSON.errors.alamat);
                $('#telepon_error').text(response.responseJSON.errors.telepon);
            }
        });
    });
</script>
@endsection
