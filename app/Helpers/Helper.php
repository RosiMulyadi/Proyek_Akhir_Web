<?php
// app/Helpers/helpers.php

use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        $rupiah = "Rp " . number_format($angka, 0, ',', '.');
        return $rupiah;
    }
}