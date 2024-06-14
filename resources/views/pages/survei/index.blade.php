@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pengajuan Survei</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pengajuan Survei</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('survei.create') }}" class="btn btn-sm btn-primary">Tambah</a>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-survey" style="width: 100%" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Id Penyewa</th>
                                        <th>Nama Penyewa</th>
                                        <th>No KTP</th>
                                        <th>Tanggal Survei</th>
                                        <th>Waktu</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- ./col -->
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

@section('script')
<script type="application/javascript">
    $(document).ready(function() {
        $('#table-survey').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('survei.index') }}",
                type: 'GET',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    className: 'align-middle',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'id_penyewa',
                    className: 'align-middle'
                },
                {
                    data: 'nama_penyewa',
                    className: 'align-middle'
                },
                {
                    data: 'no_ktp',
                    className: 'align-middle'
                },
                {
                    data: 'tanggal_survei',
                    className: 'align-middle'
                },
                {
                    data: 'waktu',
                    className: 'align-middle'
                },
                {
                    data: 'keterangan',
                    className: 'align-middle'
                },
                {
                    data: 'action',
                    className: 'align-middle text-center',
                    orderable: false,
                }
            ],
            // Rest of the DataTables settings
        });
    });

    function deleteItem(button) {
        var id = $(button).data('id');
        var id_penyewa = $(button).data('id_penyewa');

        Swal.fire({
            title: 'Kamu Yakin?',
            text: 'Kamu ingin menghapus survei ' + name + '.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/survei/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Remove the deleted row from the table
                        $(button).closest('tr').remove();

                        Swal.fire(
                            'Deleted!',
                            name + ' Telah dihapus',
                            'success'
                        );
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    }
</script>
@endsection