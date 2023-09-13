@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create User</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">User</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="createUserForm" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <span id="name_error" class="text-danger"></span>
                        @error('name'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No. KTP:</label>
                        <input type="text" name="no_ktp" id="no_ktp" class="form-control" required>
                        <span id="no_ktp_error" class="text-danger"></span>
                        @error('no_ktp'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" required>
                        <span id="alamat_error" class="text-danger"></span>
                        @error('alamat'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <span id="email_error" class="text-danger"></span>
                        @error('email'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" id="telepon" class="form-control" required>
                        <span id="telepon_error" class="text-danger"></span>
                        @error('telepon'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span id="password_error" class="text-danger"></span>
                        @error('password'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="jenkel">Jenis Kelamin:</label>
                        <select name="jenkel" id="jenkel" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        <span id="jenkel_error" class="text-danger"></span>
                        @error('jenkel'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir:</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control" required>
                        <span id="tgl_lahir_error" class="text-danger"></span>
                        @error('tgl_lahir'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="tmpt_lahir">Tempat Lahir:</label>
                        <input type="text" name="tmpt_lahir" id="tmpt_lahir" class="form-control" required>
                        <span id="tmpt_lahir_error" class="text-danger"></span>
                        @error('tmpt_lahir'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select name="role" id="role" class="form-control select2" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <span id="role_error" class="text-danger"></span>
                        @error('role'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createUserBtn" class="btn btn-primary">Create User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
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

        $("#createUserForm").on('submit', function(e) {
            e.preventDefault();
            var btn = $('#createUserBtn');
            btn.attr('disabled', true);
            btn.val("Loading...");
            let formData = new FormData(this);
            $('#name_error').text('');
            $('#no_ktp_error').text('');
            $('#alamat_error').text('');
            $('#email_error').text('');
            $('#telepon_error').text('');
            $('#password_error').text('');
            $('#jenkel_error').text('');
            $('#tgl_lahir_error').text('');
            $('#tmpt_lahir_error').text('');
            $('#role_error').text('');

            $.ajax({
                url: "{{ route('users.store') }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $(".preloader").fadeOut();
                    if (response.success) {
                        sessionStorage.setItem('success', response.message);
                        $('#jenkel').html('<span class="badge badge-primary">' + response.jenkel + '</span>');

                        Swal.fire({
                            icon: 'success',
                            title: 'User Created',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500 // Auto close after 1.5 seconds
                        }).then(function() {
                            window.location.href = "{{ route('users.index') }}";
                        });
                    }
                },
                error: function(response) {
                    btn.attr('disabled', false);
                    btn.val("Simpan");

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the user.',
                        confirmButtonText: 'OK'
                    });

                    $('#name_error').text(response.responseJSON.errors.name);
                    $('#no_ktp_error').text(response.responseJSON.errors.no_induk);
                    $('#alamat_error').text(response.responseJSON.errors.alamat);
                    $('#email_error').text(response.responseJSON.errors.email);
                    $('#telepon_error').text(response.responseJSON.errors.telepon);
                    $('#password_error').text(response.responseJSON.errors.password);
                    $('#jenkel_error').text(response.responseJSON.errors.jenkel);
                    $('#tgl_lahir_error').text(response.responseJSON.errors.tgl_lahir);
                    $('#tmpt_lahir_error').text(response.responseJSON.errors.tmpt_lahir);
                    $('#role_error').text(response.responseJSON.errors.role);
                }
            });
        });
    });
</script>
@endsection