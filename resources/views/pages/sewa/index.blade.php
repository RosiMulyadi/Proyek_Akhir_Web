@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Sewa</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Sewa</li>
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
                    <!-- <div class="card-header">
                        <a href="{{ route('sewa.create') }}" class="btn btn-sm btn-primary">Tambah</a>
                    </div> -->
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-sewa" style="width: 100%" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Id Pemilik</th>
                                        <th>Id Penyewa</th>
                                        <th>Id Toko</th>
                                        <th>Gambar</th>
                                        <th>Alamat</th>
                                        <th>Luas Bangunan</th>
                                        <th>Cluster</th>
                                        <th>Harga</th>
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
        $('#table-sewa').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sewa.index') }}",
                type: 'GET',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    className: 'align-middle',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'id_pemilik',
                    className: 'align-middle'
                },
                {
                    data: 'id_penyewa',
                    className: 'align-middle'
                },
                {
                    data: 'id_toko',
                    className: 'align-middle'
                },
                {
                    data: 'gambar',
                    className: 'align-middle',
                    render: function(data, type, full, meta) {
                        return "<img src='" + data + "' height='100'/>";
                    }
                },
                {
                    data: 'alamat',
                    className: 'align-middle'
                },
                {
                    data: 'luas_bangunan',
                    className: 'align-middle'
                },
                {
                    data: 'cluster',
                    className: 'align-middle'
                },
                {
                    data: 'harga',
                    className: 'align-middle',
                    render: function(data, type, full, meta) {
                        return formatRupiah(data);
                    }
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

    function formatRupiah(angka) {
        var numberString = angka.toString();
        var split = numberString.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    function deleteItem(button) {
        var id = $(button).data('id');
        var name = $(button).data('nama');

        Swal.fire({
            title: 'Kamu Yakin?',
            text: 'Kamu ingin menghapus sewa ' + name + '.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/sewa/' + id,
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
