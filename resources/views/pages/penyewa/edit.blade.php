@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Penyewa</h1>
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
                <form id="editPenyewaForm" method="POST" action="{{ route('penyewa.update', $penyewa->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_penyewa">Id Penyewa:</label>
                        <input type="text" name="id_penyewa" class="form-control" value="{{ $penyewa->id_penyewa }}" readonly>
                        <span class="text-danger" id="id_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" class="form-control" value="{{ $penyewa->nama }}" required>
                        <span class="text-danger" id="nama_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No. KTP:</label>
                        <input type="text" name="no_ktp" class="form-control" value="{{ $penyewa->no_ktp }}" required>
                        <span class="text-danger" id="no_ktp_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $penyewa->alamat }}" required>
                        <span class="text-danger" id="alamat_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" class="form-control" value="{{ $penyewa->telepon }}" required>
                        <span class="text-danger" id="telepon_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editPenyewaBtn" class="btn btn-primary">Update Penyewa</button>
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
    $("#editPenyewaForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editPenyewaBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        var formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#id_penyewa_error').text('');
        $('#nama_error').text('');
        $('#no_ktp_error').text('');
        $('#alamat_error').text('');
        $('#telepon_error').text('');

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
                        title: 'Penyewa Updated',
                        text: 'Penyewa Berhasil Diupdate.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('penyewa.index') }}";
                    });
                } else {
                    if (response.errors) {
                        // Handle error fields
                        // ...
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'An error occurred while updating the penyewa.',
                        confirmButtonText: 'OK'
                    });
                }

                btn.attr('disabled', false);
                btn.val("Update Penyewa");
            },
            error: function(xhr, status, error) {
                // Handle error cases
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'An error occurred while updating the penyewa.',
                    confirmButtonText: 'OK'
                });

                btn.attr('disabled', false);
                btn.val("Update Penyewa");
            }
        });
    });
</script>
@endsection