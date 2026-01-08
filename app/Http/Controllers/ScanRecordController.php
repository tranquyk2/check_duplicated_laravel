<?php

namespace App\Http\Controllers;

use App\Models\ScanRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanRecordController extends Controller
{
    /**
     * Hiển thị trang dashboard
     */
    public function index(Request $request)
    {
        $query = ScanRecord::query()->orderBy('id', 'desc');

        // Tìm kiếm theo barcode
        if ($request->filled('search') && $request->search != '') {
            $query->where('barcode', 'like', '%' . $request->search . '%');
        }

        // Lọc theo kết quả
        if ($request->filled('ket_qua') && $request->ket_qua != '') {
            $query->where('ket_qua', $request->ket_qua);
        }

        // Lọc theo ca
        if ($request->filled('ca') && $request->ca != '') {
            $query->where('ca', $request->ca);
        }

        // Lọc theo ngày - tìm trong trường ngay_gio
        if ($request->filled('date') && $request->date != '') {
            // Chuyển đổi từ YYYY-MM-DD sang DD/MM/YYYY
            $date = \Carbon\Carbon::parse($request->date)->format('d/m/Y');
            $query->where('ngay_gio', 'like', $date . '%');
        }

        $records = $query->paginate(50)->appends($request->all());

        // Thống kê
        $statistics = [
            'total' => ScanRecord::count(),
            'today' => ScanRecord::whereDate('created_at', today())->count(),
            'duplicate' => ScanRecord::where('ket_qua', 'like', '%trùng%')
                ->orWhere('ket_qua', 'like', '%trung%')
                ->orWhere('ket_qua', 'like', '%duplicate%')
                ->count(),
            'error' => ScanRecord::where(function($query) {
                $query->where('ket_qua', 'like', '%sai%')
                      ->orWhere('ket_qua', 'like', '%model%')
                      ->orWhere('ket_qua', 'like', '%lỗi%')
                      ->orWhere('ket_qua', 'like', '%error%');
            })->count(),
            'unique' => ScanRecord::where(function($query) {
                $query->where('ket_qua', 'not like', '%trùng%')
                      ->where('ket_qua', 'not like', '%trung%')
                      ->where('ket_qua', 'not like', '%duplicate%')
                      ->where('ket_qua', 'not like', '%sai%')
                      ->where('ket_qua', 'not like', '%model%')
                      ->where('ket_qua', 'not like', '%lỗi%')
                      ->where('ket_qua', 'not like', '%error%');
            })->count(),
        ];

        // Thống kê theo ca
        $caStatistics = ScanRecord::select('ca', DB::raw('count(*) as total'))
            ->whereNotNull('ca')
            ->where('ca', '!=', '')
            ->groupBy('ca')
            ->orderBy('ca')
            ->get();

        // Lấy danh sách các kết quả để lọc
        $ketQuaList = ScanRecord::select('ket_qua', DB::raw('count(*) as total'))
            ->whereNotNull('ket_qua')
            ->where('ket_qua', '!=', '')
            ->groupBy('ket_qua')
            ->orderBy('ket_qua')
            ->get();

        // Debug: Log để kiểm tra
        \Log::info('Ca Statistics:', $caStatistics->toArray());

        return view('dashboard', compact('records', 'statistics', 'caStatistics', 'ketQuaList'));
    }

    /**
     * API endpoint để lấy dữ liệu real-time
     */
    public function getRecords(Request $request)
    {
        $records = ScanRecord::orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        return response()->json($records);
    }

    /**
     * API endpoint để lấy thống kê
     */
    public function getStatistics()
    {
        $statistics = [
            'total' => ScanRecord::count(),
            'today' => ScanRecord::whereDate('created_at', today())->count(),
            'duplicate' => ScanRecord::where('ket_qua', 'like', '%trùng%')
                ->orWhere('ket_qua', 'like', '%trung%')
                ->orWhere('ket_qua', 'like', '%duplicate%')
                ->count(),
            'error' => ScanRecord::where(function($query) {
                $query->where('ket_qua', 'like', '%sai%')
                      ->orWhere('ket_qua', 'like', '%model%')
                      ->orWhere('ket_qua', 'like', '%lỗi%')
                      ->orWhere('ket_qua', 'like', '%error%');
            })->count(),
            'unique' => ScanRecord::where(function($query) {
                $query->where('ket_qua', 'not like', '%trùng%')
                      ->where('ket_qua', 'not like', '%trung%')
                      ->where('ket_qua', 'not like', '%duplicate%')
                      ->where('ket_qua', 'not like', '%sai%')
                      ->where('ket_qua', 'not like', '%model%')
                      ->where('ket_qua', 'not like', '%lỗi%')
                      ->where('ket_qua', 'not like', '%error%');
            })->count(),
            'last_scan' => ScanRecord::latest()->first(),
        ];

        $caStatistics = ScanRecord::select('ca', DB::raw('count(*) as total'))
            ->groupBy('ca')
            ->get();

        return response()->json([
            'statistics' => $statistics,
            'ca_statistics' => $caStatistics,
        ]);
    }

    /**
     * Xóa record
     */
    public function destroy($id)
    {
        $record = ScanRecord::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Đã xóa thành công']);
    }

    /**
     * Xóa tất cả records
     */
    public function deleteAll()
    {
        ScanRecord::truncate();

        return response()->json(['message' => 'Đã xóa tất cả records']);
    }

    /**
     * Xuất dữ liệu ra Excel (CSV)
     */
    public function export(Request $request)
    {
        $query = ScanRecord::query()->orderBy('id', 'desc');

        // Lọc theo tháng (ưu tiên cao nhất)
        if ($request->filled('month') && $request->month != '') {
            // Month format: 2026-01 -> chuyển sang /01/2026 để tìm
            $parts = explode('-', $request->month);
            $month = $parts[1] . '/' . $parts[0]; // MM/YYYY
            $query->where('ngay_gio', 'like', '%/' . $month . '%');
        }
        // Lọc theo khoảng thời gian
        elseif ($request->filled('from') || $request->filled('to')) {
            if ($request->filled('from') && $request->from != '') {
                $fromDate = \Carbon\Carbon::parse($request->from)->format('d/m/Y');
                $query->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(ngay_gio, ' ', 1), '%d/%m/%Y') >= STR_TO_DATE(?, '%d/%m/%Y')", [$fromDate]);
            }
            if ($request->filled('to') && $request->to != '') {
                $toDate = \Carbon\Carbon::parse($request->to)->format('d/m/Y');
                $query->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(ngay_gio, ' ', 1), '%d/%m/%Y') <= STR_TO_DATE(?, '%d/%m/%Y')", [$toDate]);
            }
        }
        // Nếu không chọn tháng/khoảng thì áp dụng filter từ dashboard
        else {
            // Áp dụng các filter giống như trang dashboard
            if ($request->filled('search') && $request->search != '') {
                $query->where('barcode', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('ket_qua') && $request->ket_qua != '') {
                $query->where('ket_qua', $request->ket_qua);
            }

            if ($request->filled('ca') && $request->ca != '') {
                $query->where('ca', $request->ca);
            }

            if ($request->filled('date') && $request->date != '') {
                // Chuyển đổi từ YYYY-MM-DD sang DD/MM/YYYY
                $date = \Carbon\Carbon::parse($request->date)->format('d/m/Y');
                $query->where('ngay_gio', 'like', $date . '%');
            }
        }

        $records = $query->get();

        // Debug: Log số lượng records
        \Log::info('Export records count: ' . $records->count());
        \Log::info('Request params: ' . json_encode($request->all()));
        
        // Nếu không có records, vẫn tạo file với header
        if ($records->isEmpty()) {
            \Log::warning('No records found for export');
        }

        // Tạo file CSV
        $filename = 'scan_records_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row - Dùng dấu chấm phẩy cho Excel Việt Nam
            fputcsv($file, ['STT', 'Barcode', 'Ngày giờ', 'Kết quả', 'Ca'], ';');

            // Data rows
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->stt,
                    $record->barcode,
                    $record->ngay_gio,
                    $record->ket_qua,
                    $record->ca ? $record->ca : 'N/A',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
