@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Permission</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
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
                <form id="editPermissionForm" method="POST" action="{{ route('permissions.update', $permission->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $permission->name }}" required>
                        <span id="name_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editPermissionBtn" class="btn btn-primary">Update Permission</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
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
    $("#editPermissionForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editPermissionBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        var formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#name_error').text('');
        $('#permission_error').text('');

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
                        title: 'Permission berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "{{ route('permissions.index') }}";
                    });
                } else {
                    btn.attr('disabled', false);
                    btn.val("Update Permission");
                    if (response.errors) {
                        if (response.errors.name) {
                            $('#name_error').text(response.errors.name[0]);
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                btn.attr('disabled', false);
                btn.val("Update Permission");
                if (xhr.status === 422) {
                    var errors = JSON.parse(xhr.responseText).errors;
                    if (errors.name) {
                        $('#name_error').text(errors.name[0]);
                    }
                }
            }

        });
        $('#name_error').on('input', function() {
            $('#name_error').text('');
        });
    });
</script>
@endsection