@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Survey</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Survei</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="createSurveyForm" method="POST" action="{{ route('survei.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="id_penyewa">ID Penyewa:</label>
                        <select name="id_penyewa" id="id_penyewa" class="form-control select2" required>
                            <option value="">-- Select Penyewa --</option>
                            @foreach($penyewa as $pen)
                            <option value="{{ $pen->id_penyewa }}">{{ $pen->id_penyewa }}</option>
                            @endforeach
                        </select>
                        <span id="id_penyewa_error" class="text-danger"></span>
                        @error('id_penyewa'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_penyewa">Nama Penyewa:</label>
                        <select name="nama_penyewa" id="nama_penyewa" class="form-control select2" required>
                            <option value="">-- Select Penyewa --</option>
                            @foreach($penyewa as $pen)
                            <option value="{{ $pen->name }}">{{ $pen->name }}</option>
                            @endforeach
                        </select>
                        <span id="name_error" class="text-danger"></span>
                        @error('nama_penyewa'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No KTP:</label>
                        <select name="no_ktp" id="no_ktp" class="form-control select2" required>
                            <option value="">-- Select Penyewa --</option>
                            @foreach($penyewa as $pen)
                            <option value="{{ $pen->no_ktp }}">{{ $pen->no_ktp }}</option>
                            @endforeach
                        </select>
                        <span id="no_ktp_error" class="text-danger"></span>
                        @error('no_ktp'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal_survei">Tanggal Survei:</label>
                        <input type="date" name="tanggal_survei" class="form-control" required>
                        <span class="text-danger" id="tanggal_survei_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="waktu">Waktu:</label>
                        <input type="time" name="waktu" class="form-control" required>
                        <span class="text-danger" id="waktu_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan:</label>
                        <textarea name="keterangan" class="form-control" required></textarea>
                        <span class="text-danger" id="keterangan_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createSurveyBtn" class="btn btn-primary">Create Survei</button>
                        <a href="{{ route('survei.index') }}" class="btn btn-secondary">Cancel</a>
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
    $(document).ready(function() {
        $('.select2').select2();
        // Form submission with AJAX
        $("#createSurveyForm").on('submit', function(e) {
            e.preventDefault();
            var btn = $('#createSurveyBtn');
            btn.attr('disabled', true).text("Loading...");
            let formData = new FormData(this);
            $('#id_penyewa_error').text('');
            $('#nama_penyewa_error').text('');
            $('#no_ktp_error').text('');
            $('#tanggal_survei_error').text('');
            $('#waktu_error').text('');
            $('#keterangan_error').text('');

            $.ajax({
                url: "{{ route('survei.store') }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Survei Created',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "{{ route('survei.index') }}";
                        });
                    }
                },
                error: function(response) {
                    btn.attr('disabled', false).text("Create Survei");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the survey.',
                        confirmButtonText: 'OK'
                    });
                    if (response.responseJSON.errors) {
                        $('#id_penyewa_error').text(response.responseJSON.errors.id_penyewa);
                        $('#nama_penyewa_error').text(response.responseJSON.errors.nama_penyewa);
                        $('#no_ktp_error').text(response.responseJSON.errors.no_ktp);
                        $('#tanggal_survei_error').text(response.responseJSON.errors.tanggal_survei);
                        $('#waktu_error').text(response.responseJSON.errors.waktu);
                        $('#keterangan_error').text(response.responseJSON.errors.keterangan);
                    }
                }
            });
        });
    });
</script>
@endsection