@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Store</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Stores</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="editStoreForm" method="POST" action="{{ route('stores.update', $store->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="id_pemilik">ID Pemilik:</label>
                        <select name="id_pemilik" id="id_pemilik" class="form-control select2" required>
                            <option value="">-- Select Pemilik --</option>
                            @foreach($pemilik as $pem)
                            <option value="{{ $pem->id_pemilik }}" @if($pem->id_pemilik == $store->id_pemilik) selected @endif>{{ $pem->id_pemilik }} - {{ $pem->name }}</option>
                            @endforeach
                        </select>
                        <span id="id_pemilik_error" class="text-danger"></span>
                        @error('id_pemilik'){{ $message }}@enderror
                    </div>
                    <div class="form-group">
                        <label for="id_toko">Id Toko:</label>
                        <input type="text" name="id_toko" class="form-control" value="{{ $store->id_toko }}" required>
                        <span class="text-danger" id="id_toko_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar:</label>
                        <input type="file" name="gambar" class="form-control-file" id="gambarInput">
                        <span class="text-danger" id="gambar_error"></span>
                        @if ($store->gambar)
                        <img src="{{ asset('storage/' . $store->gambar) }}" alt="Gambar Preview" style="max-height: 200px;">
                        @else
                        <span>No Image Available</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" id="alamat" class="form-control" value="{{ $store->alamat }}" required>
                        <span id="alamat_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="luas_bangunan">Luas Bangunan:</label>
                        <input type="text" name="luas_bangunan" class="form-control" value="{{ $store->luas_bangunan }}" required>
                        <span class="text-danger" id="luas_bangunan_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="map">Cluster:</label>
                        <div id="map" style="height: 300px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="text" name="harga" class="form-control" value="{{ $store->harga }}" required>
                        <span class="text-danger" id="harga_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="editStoreBtn" class="btn btn-primary">Update Store</button>
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
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

        // Initialize the map
        var map = L.map('map').setView([-7.0, 113.9], 10); // Pusatkan peta ke Kabupaten Sumenep
        var marker;

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            subdomains: ['a', 'b', 'c']
        }).addTo(map);

        // Add search bar control
        var geocoder = L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            var latlng = e.geocode.center;
            map.setView(latlng, 13);
            if (marker) {
                marker.setLatLng(latlng).update();
            } else {
                marker = L.marker(latlng).addTo(map);
            }
        }).addTo(map);

        // Add attribution control with geocoder link
        L.control.attribution({
            prefix: '<a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> | Search powered by <a href="https://nominatim.openstreetmap.org" target="_blank">Nominatim</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Image preview
        $("#gambarInput").on('change', function(e) {
            var gambarInput = e.target;
            if (gambarInput.files && gambarInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#gambarPreview').attr('src', e.target.result);
                    $('#gambarPreview').css('display', 'block');
                }
                reader.readAsDataURL(gambarInput.files[0]);
            }
        });

        // Form submission with AJAX
        $("#editStoreForm").on('submit', function(e) {
            e.preventDefault();
            var btn = $('#editStoreBtn');
            btn.attr('disabled', true).text("Loading...");
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('stores.update', $store->id) }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Store Updated',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "{{ route('stores.index') }}";
                        });
                    }
                },
                error: function(response) {
                    btn.attr('disabled', false).text("Update Store");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the store.',
                        confirmButtonText: 'OK'
                    });
                    if (response.responseJSON.errors) {
                        $('#id_pemilik_error').text(response.responseJSON.errors.id_pemilik);
                        $('#id_toko_error').text(response.responseJSON.errors.id_toko);
                        $('#gambar_error').text(response.responseJSON.errors.gambar);
                        $('#alamat_error').text(response.responseJSON.errors.alamat);
                        $('#luas_bangunan_error').text(response.responseJSON.errors.luas_bangunan);
                        $('#harga_error').text(response.responseJSON.errors.harga);
                    }
                }
            });
        });
    });
</script>
@endsection