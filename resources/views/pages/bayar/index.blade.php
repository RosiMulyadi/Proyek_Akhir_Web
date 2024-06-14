@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pelaksanaan Pembayaran</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
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
                        <a href="{{ route('bayar.create') }}" class="btn btn-sm btn-primary">Tambah</a>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-pembayaran" style="width: 100%" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Bayar</th>
                                        <th>ID Penyewa</th>
                                        <th>ID Toko</th>
                                        <th>Nama Penyewa</th>
                                        <th>Harga</th>
                                        <th>Bayar</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- Your table body content goes here -->
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
        var table = $('#table-pembayaran').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('bayar.index') }}",
                type: 'GET',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    className: 'align-middle'
                },
                {
                    data: 'id_bayar',
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
                    data: 'nama_penyewa',
                    className: 'align-middle'
                },
                {
                    data: 'harga',
                    className: 'align-middle'
                },
                {
                    data: 'bayar',
                    className: 'align-middle',
                    render: function(data, type, full, meta) {
                        return "<img src='" + data + "' height='100'/>";
                    }
                },
                {
                    data: 'keterangan',
                    className: 'align-middle text-center'
                },
                {
                    data: 'action',
                    className: 'align-middle text-center'
                }
            ],
            // Rest of the DataTables settings
        });

        var clickCount = 0; // Track the number of clicks
        var statusChosen = false; // Flag to check if the status has been chosen

        $('#table-pembayaran tbody').on('click', 'button.share-btn', function() {
            if (statusChosen) {
                // Status has already been chosen, do nothing
                return;
            }

            var data = table.row($(this).closest('tr')).data();
            var id = data.id;
            var title = data.judul; // Assuming you have a field named 'judul'

            clickCount++; // Increment the click count

            var statusMappings = {
                1: ['Diproses (sesuai Kategori)', 'Anda ingin mengupdate status pembayaran dengan judul ' + title + ' menjadi "Diproses (sesuai Kategori)"?'],
                2: ['Diproses (oleh Kepala Kemenag)', 'Anda ingin mengupdate status pembayaran dengan judul ' + title + ' menjadi "Diproses (oleh Kepala Kemenag)"?'],
                3: ['Diterima', 'Anda ingin mengupdate status pembayaran dengan judul ' + title + ' menjadi "Diterima" atau "Ditolak"?'],
            };

            // If click count exceeds 3, reset it
            if (clickCount > 3) {
                clickCount = 1;
            }

            var [status_keterangan, confirmationText] = statusMappings[clickCount];

            if (clickCount === 1 || clickCount === 2) {
                // SweetAlert for "Diproses (sesuai Kategori)" and "Diproses (oleh Kepala Kemenag)"
                Swal.fire({
                    title: 'Update Status?',
                    text: confirmationText,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform the update based on the chosen status
                        updateStatus(id, status_keterangan, title, confirmationText);
                    }
                    // Reset statusChosen flag for further notifications
                    statusChosen = false;
                });
            } else if (clickCount === 3) {
                // SweetAlert for "Diterima" or "Ditolak"
                Swal.fire({
                    title: 'Update Status?',
                    text: confirmationText,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Diterima',
                    cancelButtonText: 'Ditolak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        status_keterangan = 'Diterima';
                    } else {
                        status_keterangan = 'Ditolak';
                    }
                    // Perform the update based on the chosen status
                    updateStatus(id, status_keterangan, title, confirmationText);
                    // Set statusChosen to true to prevent further notifications
                    statusChosen = true;
                });
            }
        });

        function updateStatus(id, status_keterangan, title, confirmationText) {
            // Perform an AJAX request to update the status_keterangan
            $.ajax({
                url: '/pembayaran/updateStatus/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    keterangan: status_keterangan
                },
                success: function(response) {
                    // Reload the DataTable to reflect the changes
                    table.ajax.reload();

                    // Optionally, you can show a success message without using Swal.fire
                    console.log('Status Updated:', 'Status pembayaran dengan judul ' + title + ' telah diubah menjadi "' + status_keterangan + '".');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('#table-pembayaran tbody').on('click', 'button.delete-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('judul');

            Swal.fire({
                title: 'Kamu Yakin?',
                text: 'Kamu ingin menghapus pembayaran dengan judul ' + name + '.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/pembayaran/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Remove the deleted row from the table
                            table.ajax.reload(); // Reload the DataTable to reflect the changes

                            Swal.fire(
                                'Deleted!',
                                'Pembayaran dengan judul ' + name + ' telah dihapus',
                                'success'
                            );
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
