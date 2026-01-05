<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanRecord;

class ScanController extends Controller
{
    public function receive(Request $request)
    {
        $scans = $request->json()->all();
        foreach ($scans as $scan) {
            ScanRecord::create([
                'stt' => $scan['STT'] ?? 0,
                'barcode' => $scan['Barcode'] ?? '',
                'ngay_gio' => $scan['NgayGio'] ?? '',
                'ket_qua' => $scan['KetQua'] ?? '',
                'ca' => $scan['Ca'] ?? '',
            ]);
        }
        return response()->json(['success' => true]);
    }
}
