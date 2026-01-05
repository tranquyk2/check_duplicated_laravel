<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanRecord extends Model
{
    protected $fillable = [
        'stt', 'barcode', 'ngay_gio', 'ket_qua', 'ca'
    ];
}
