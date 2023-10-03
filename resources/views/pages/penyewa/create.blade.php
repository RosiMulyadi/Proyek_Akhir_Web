@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Penyewa</h1>
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
            <div class="card-body">
                <form id="createPenyewaForm" method="POST" action="{{ route('penyewa.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="id_penyewa">Id Penyewa:</label>
                        <input type="text" name="id_penyewa" class="form-control" required>
                        <span class="text-danger" id="id_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" class="form-control" required>
                        <span class="text-danger" id="nama_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No KTP:</label>
                        <input type="text" name="no_ktp" class="form-control" required>
                        <span class="text-danger" id="no_ktp_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" required>
                        <span class="text-danger" id="alamat_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" class="form-control" required>
                        <span class="text-danger" id="telepon_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createPenyewaBtn" class="btn btn-primary">Create Penyewa</button>
                        <a href="{{ route('penyewa.index') }}" class="btn btn-secondary">Cancel</a>
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
    $("#createPenyewaForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#createPenyewaBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        let formData = new FormData(this);
        $('#id_penyewa_error').text('');
        $('#nama_error').text('');
        $('#no_ktp_error').text('');
        $('#alamat_error').text('');
        $('#telepon_error').text('');

        $.ajax({
            url: "{{ route('penyewa.store') }}",
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
                        title: 'Penyewa Created',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('penyewa.index') }}";
                    });
                }
            },
            error: function(response) {
                btn.attr('disabled', false);
                btn.val("Simpan");

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the penyewa.',
                    confirmButtonText: 'OK'
                });

                $('#id_penyewa_error').text(response.responseJSON.errors.id_penyewa);
                $('#nama_error').text(response.responseJSON.errors.nama);
                $('#no_ktp_error').text(response.responseJSON.errors.no_ktp);
                $('#alamat_error').text(response.responseJSON.errors.alamat);
                $('#telepon_error').text(response.responseJSON.errors.telepon);
            }
        });
    });
</script>
@endsection