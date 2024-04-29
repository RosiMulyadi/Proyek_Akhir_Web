@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pemilik</h1>
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
                <form id="editPemilikForm" method="POST" action="{{ route('pemilik.update', $pemilik->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_pemilik">Id Pemilik:</label>
                        <input type="text" name="id_pemilik" class="form-control" value="{{ $pemilik->id_pemilik }}" required>
                        <span class="text-danger" id="id_pemilik_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" name="name" class="form-control" value="{{ $pemilik->name }}" required>
                        <span class="text-danger" id="name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $pemilik->alamat }}" required>
                        <span class="text-danger" id="alamat_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="telepon">No. Hp:</label>
                        <input type="text" name="telepon" class="form-control" value="{{ $pemilik->telepon }}" required>
                        <span class="text-danger" id="telepon_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editPemilikBtn" class="btn btn-primary">Update Pemilik</button>
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
    $("#editPemilikForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editPemilikBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        var formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#id_pemilik_error').text('');
        $('#name_error').text('');
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
                        title: 'Pemilik Updated',
                        text: 'Pemilik Berhasil Diupdate.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('pemilik.index') }}";
                    });
                } else {
                    if (response.errors) {
                        // Handle error fields
                        // ...
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'An error occurred while updating the pemilik.',
                        confirmButtonText: 'OK'
                    });
                }

                btn.attr('disabled', false);
                btn.val("Update Pemilik");
            },
            error: function(xhr, status, error) {
                // Handle error cases
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'An error occurred while updating the pemilik.',
                    confirmButtonText: 'OK'
                });

                btn.attr('disabled', false);
                btn.val("Update Pemilik");
            }
        });
    });
</script>
@endsection