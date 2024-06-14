@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Stores Details</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Stores</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('stores.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <img src="{{ asset('storage/' . $store->gambar) }}" alt="Store Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="id_toko">ID Toko:</label>
                        <input type="text" name="id_toko" class="form-control" value="{{ $store->id_toko }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $store->alamat }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="luas_bangunan">Luas Bangunan:</label>
                        <input type="text" name="luas_bangunan" class="form-control" value="{{ $store->luas_bangunan }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga:</label>
                        <input type="text" name="harga" class="form-control" value="{{ $store->harga }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cluster">Cluster:</label>
                        <div id="map" style="width: 100%; height: 300px;"></div>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="{{ route('sewa.create') }}" class="btn btn-primary btn-lg btn-block btn-wide">
                            <i class="fas fa-plus"></i> Pesan/Sewa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatRupiah(angka) {
            var number_string = angka.toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        var hargaInput = document.querySelector('input[name="harga"]');
        if (hargaInput) {
            var harga = parseInt(hargaInput.value.replace(/[^,\d]/g, ''), 10);
            hargaInput.value = 'Rp ' + formatRupiah(harga);
        }

        var map;
        var marker;

        // Function to save store data to localStorage
        function saveStoreData(storeData) {
            localStorage.setItem('store', JSON.stringify(storeData));
        }

        // Function to load store data from localStorage
        function loadStoreData() {
            var data = JSON.parse(localStorage.getItem('store'));
            if (data) {
                return data;
            } else {
                return null;
            }
        }

        // Function to initialize map and set view
        function initializeMap() {
            var storeData = loadStoreData();
            if (storeData && storeData.clusterLocation) {
                map = L.map('map').setView([storeData.clusterLocation.lat, storeData.clusterLocation.lng], 13);
                marker = L.marker([storeData.clusterLocation.lat, storeData.clusterLocation.lng]).addTo(map)
                    .bindPopup(storeData.clusterLocation.location)
                    .openPopup();
            } else {
                map = L.map('map').setView([-7.0, 113.9], 10); // Default view if no saved location
            }

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                subdomains: ['a', 'b', 'c']
            }).addTo(map);

            // // Add search bar control
            // var geocoder = L.Control.geocoder({
            //     defaultMarkGeocode: false
            // }).on('markgeocode', function(e) {
            //     var latlng = e.geocode.center;
            //     var locationName = e.geocode.name;
            //     map.setView(latlng, 13);
            //     if (marker) {
            //         marker.setLatLng(latlng).update()
            //             .bindPopup(locationName)
            //             .openPopup();
            //     } else {
            //         marker = L.marker(latlng).addTo(map)
            //             .bindPopup(locationName)
            //             .openPopup();
            //     }
            //     saveStoreData({
            //         clusterLocation: {
            //             lat: latlng.lat,
            //             lng: latlng.lng,
            //             location: locationName
            //         }
            //     });
            // }).addTo(map);

            // Add attribution control with geocoder link
            L.control.attribution({
                prefix: '<a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> | Search powered by <a href="https://nominatim.openstreetmap.org" target="_blank">Nominatim</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        }

        // Function to update map with new marker data
        function updateMapWithNewMarkerData(latlng, locationName) {
            map.setView(latlng, 13);
            if (marker) {
                marker.setLatLng(latlng).update()
                    .bindPopup(locationName)
                    .openPopup();
            } else {
                marker = L.marker(latlng).addTo(map)
                    .bindPopup(locationName)
                    .openPopup();
            }
            saveStoreData({
                clusterLocation: {
                    lat: latlng.lat,
                    lng: latlng.lng,
                    location: locationName
                }
            });
        }

        // Load the map
        initializeMap();

        // Update map with new marker data when "Show" button is clicked
        document.getElementById('show-button').addEventListener('click', function() {
            var latlng = new L.LatLng(-7.033, 113.917); // Example coordinates for new cluster location
            var locationName = 'Kepanjin, Kabupaten Sumenep';
            updateMapWithNewMarkerData(latlng, locationName);
        });

    });
</script>
@endsection