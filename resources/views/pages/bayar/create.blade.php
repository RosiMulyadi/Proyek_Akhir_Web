@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Pembayaran</h1>
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
                <form id="createPembayaranForm" method="POST" action="{{ route('bayar.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="id_bayar">ID Bayar:</label>
                        <input type="text" name="id_bayar" class="form-control" required>
                        <span class="text-danger" id="id_bayar_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="id_penyewa">ID Penyewa:</label>
                        <select name="id_penyewa" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($penyewa as $penyewa)
                                <option value="{{ $penyewa->id }}">{{ $penyewa->id }} - {{ $penyewa->nama }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="id_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="id_toko">ID Toko:</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($store as $store)
                                <option value="{{ $store->id }}">{{ $store->id }} - {{ $store->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="id_toko_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="nama_penyewa">Nama Penyewa:</label>
                        <select name="nama_penyewa" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($user as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="nama_penyewa_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <select name="harga" class="form-control" required>
                            <option value="">-pilihan-</option>
                            @foreach($store as $store)
                                <option value="{{ $store->harga }}">{{ $store->harga }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="harga_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="bayar">Bayar:</label>
                        <input type="file" name="bayar" class="form-control-file" id="bayarInput" accept=".jpeg, .jpg, .png" required>
                        <small class="form-text text-muted">Pilih file gambar dengan ekstensi ( jpeg, jpg, atau png )</small>
                        <span class="text-danger" id="bayar_error"></span>
                    </div>
                    <div class="form-group">
                        <img id="bayarPreview" src="#" alt="bayarPreview" style="max-height: 200px; display: none;">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan:</label>
                        <select name="keterangan" class="form-control" required>
                            <option value="Diajukan">Diajukan</option>
                            <option value="Diproses (sesuai Kategori)">Diproses (sesuai Kategori)</option>
                            <option value="Diproses (oleh Kepala Kemenag)">Diproses (oleh Kepala Kemenag)</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                        <span class="text-danger" id="keterangan_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="createPembayaranBtn" class="btn btn-primary">Create Pembayaran</button>
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

        $("#createPembayaranForm").on('submit', function(e) {
            e.preventDefault();
            var btn = $('#createPembayaranBtn');
            btn.attr('disabled', true);
            btn.val("Loading...");
            let formData = new FormData(this);
            $('#id_bayar_error').text('');
            $('#id_penyewa_error').text('');
            $('#id_toko_error').text('');
            $('#nama_penyewa_error').text('');
            $('#harga_error').text('');
            $('#bayar_error').text('');
            $('#keterangan_error').text('');

            $.ajax({
                url: "{{ route('bayar.store') }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        sessionStorage.setItem('success', response.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Created',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "{{ route('bayar.index') }}";
                        });
                    }
                },
                error: function(response) {
                    btn.attr('disabled', false);
                    btn.val("Create Pembayaran");

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the pembayaran.',
                        confirmButtonText: 'OK'
                    });

                    $('#id_bayar_error').text(response.responseJSON.errors.id_bayar);
                    $('#id_penyewa_error').text(response.responseJSON.errors.id_penyewa);
                    $('#id_toko_error').text(response.responseJSON.errors.id_toko);
                    $('#nama_penyewa_error').text(response.responseJSON.errors.nama_penyewa);
                    $('#harga_error').text(response.responseJSON.errors.harga);
                    $('#bayar_error').text(response.responseJSON.errors.bayar);
                    $('#keterangan_error').text(response.responseJSON.errors.keterangan);
                }
            });
        });
    });
</script>
@endsection
