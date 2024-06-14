@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pembayaran</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="editPembayaranForm" method="POST" action="{{ route('bayar.update', $pembayaran->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_bayar">ID Bayar:</label>
                        <input type="text" name="id_bayar" class="form-control" value="{{ $pembayaran->id_bayar }}" required>
                        <span class="text-danger" id="id_bayar_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="id_penyewa">ID Penyewa:</label>
                        <select name="id_penyewa" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($penyewas as $penyewa)
                            <option value="{{ $penyewa->id }}" {{ $pembayaran->id_penyewa == $penyewa->id ? 'selected' : '' }}>{{ $penyewa->id }} - {{ $penyewa->nama }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="id_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="id_toko">ID Toko:</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $pembayaran->id_toko == $store->id ? 'selected' : '' }}>{{ $store->id }} - {{ $store->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="id_toko_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="nama_penyewa">Nama Penyewa:</label>
                        <select name="nama_penyewa" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($users as $user)
                            <option value="{{ $user->name }}" {{ $pembayaran->nama_penyewa == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="nama_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <select name="harga" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->harga }}" {{ $pembayaran->harga == $store->harga ? 'selected' : '' }}>{{ $store->harga }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="harga_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="bayar">Bayar:</label>
                        <input type="file" id="bayarInput" name="bayar" class="form-control-file" accept=".jpeg, .jpg, .png">
                        <small class="form-text text-muted">Pilih file gambar dengan ekstensi ( jpeg, jpg, atau png )</small>
                        <span class="text-danger" id="bayar_error"></span>
                        @if($pembayaran->bayar)
                        <div class="mt-2">
                            <img id="bayarPreview" src="{{ asset('storage/' . $pembayaran->bayar) }}" alt="bayar" style="max-width: 200px;">
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan:</label>
                        <select name="keterangan" class="form-control" required>
                            <option value="Diajukan" {{ $pembayaran->keterangan === 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="Diproses (sesuai Kategori)" {{ $pembayaran->keterangan === 'Diproses (sesuai Kategori)' ? 'selected' : '' }}>Diproses (sesuai Kategori)</option>
                            <option value="Diproses (oleh Kepala Kemenag)" {{ $pembayaran->keterangan === 'Diproses (oleh Kepala Kemenag)' ? 'selected' : '' }}>Diproses (oleh Kepala Kemenag)</option>
                            <option value="Diterima" {{ $pembayaran->keterangan === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ $pembayaran->keterangan === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <span class="text-danger" id="keterangan_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editPembayaranBtn" class="btn btn-primary">Update Pembayaran</button>
                        <a href="{{ route('bayar.index') }}" class="btn btn-secondary">Cancel</a>
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
    $("#bayarInput").on('change', function(e) {
        var bayarInput = e.target;
        if (bayarInput.files && bayarInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#bayarPreview').attr('src', e.target.result);
                $('#bayarPreview').css('display', 'block');
            }

            reader.readAsDataURL(bayarInput.files[0]);
        }
    });

    $("#editPembayaranForm").on('submit', function(e) {
        e.preventDefault();
        var btn = $('#editPembayaranBtn');
        btn.attr('disabled', true);
        btn.val("Loading...");
        var formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#id_bayar_error').text('');
        $('#id_penyewa_error').text('');
        $('#id_toko_error').text('');
        $('#nama_penyewa_error').text('');
        $('#harga_error').text('');
        $('#bayar_error').text('');
        $('#keterangan_error').text('');

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
                        title: 'Pembayaran Updated',
                        text: 'Pembayaran Berhasil Diupdate.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('bayar.index') }}";
                    });
                } else {
                    if (response.errors) {
                        // Handle error fields
                        // ...
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'An error occurred while updating the pembayaran.',
                        confirmButtonText: 'OK'
                    });
                }

                btn.attr('disabled', false);
                btn.val("Update Pembayaran");
            },
            error: function(xhr, status, error) {
                // Handle error cases
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'An error occurred while updating the pembayaran.',
                    confirmButtonText: 'OK'
                });

                btn.attr('disabled', false);
                btn.val("Update Pembayaran");
            }
        });
    });
</script>
@endsection