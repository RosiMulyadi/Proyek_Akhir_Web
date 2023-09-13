@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit User</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <!-- ... -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="editUserForm" method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                        <span id="name_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No. KTP:</label>
                        <input type="text" name="no_ktp" id="no_ktp" class="form-control" value="{{ $user->no_ktp }}" required>
                        <span id="no_ktp_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ $user->alamat }}" required>
                        <span id="alamat_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                        <span id="email_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" id="telepon" class="form-control" value="{{ $user->telepon }}" required>
                        <span id="telepon_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="jenkel">Jenis Kelamin:</label>
                        <select name="jenkel" id="jenkel" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ $user->jenkel === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $user->jenkel === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <span id="jenkel_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir:</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control" value="{{ $user->tgl_lahir }}" required>
                        <span id="tgl_lahir_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="tmpt_lahir">Tempat Lahir:</label>
                        <input type="text" name="tmpt_lahir" id="tmpt_lahir" class="form-control" value="{{ $user->tmpt_lahir }}" required>
                        <span id="tmpt_lahir_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="role">Peran:</label>
                        <select name="role" id="role" class="form-control select2" required>
                            <option value="">-- Pilih Peran --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                        <span id="role_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editUserBtn" class="btn btn-primary">Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ... -->
</div>
@endsection

@section('script')
<script type="application/javascript">
    $(document).ready(function() {
        $('.select2').select2();
    });

    $("#editUserForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editUserBtn');
        btn.attr
        var btn = $('#editUserBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        let formData = new FormData(this);
        $('#name_error').text('');
        $('#no_induk_error').text('');
        $('#alamat_error').text('');
        $('#email_error').text('');
        $('#telepon_error').text('');
        $('#jenkel_error').text('');
        $('#tgl_lahir_error').text('');
        $('#tmpt_lahir_error').text('');
        $('#role_error').text('');

        $.ajax({
            url: "{{ route('users.update', $user->id) }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    sessionStorage.setItem('success', response.message);
                    $('#jenkel').html('<span class="badge badge-primary">' + response.jenkel + '</span>');

                    Swal.fire({
                        icon: 'success',
                        title: 'User Updated',
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
                btn.val("Update User");

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the user.',
                    confirmButtonText: 'OK'
                });

                $('#name_error').text(response.responseJSON.errors.name);
                $('#no_induk_error').text(response.responseJSON.errors.no_induk);
                $('#alamat_error').text(response.responseJSON.errors.alamat);
                $('#email_error').text(response.responseJSON.errors.email);
                $('#telepon_error').text(response.responseJSON.errors.telepon);
                $('#status_error').text(response.responseJSON.errors.status);
                $('#jenkel_error').text(response.responseJSON.errors.jenkel);
                $('#tgl_lahir_error').text(response.responseJSON.errors.tgl_lahir);
                $('#tmpt_lahir_error').text(response.responseJSON.errors.tmpt_lahir);
                $('#role_error').text(response.responseJSON.errors.role);
                $('#limit_pinjaman_error').text(response.responseJSON.errors.limit_pinjaman);
            }
        });
    });
</script>
@endsection