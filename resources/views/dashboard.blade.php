<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Theo dõi Scan Records</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .badge-success { background-color: #10b981; }
        .badge-danger { background-color: #ef4444; }
        .badge-warning { background-color: #f59e0b; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('sungho.png') }}" alt="Logo" class="h-8">
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard Scan Records</h1>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="refreshData()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i> Làm mới
                        </button>
                        <button onclick="showExportModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-download"></i> Xuất Excel
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Statistics Cards -->
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Tổng số records</p>
                            <p class="text-3xl font-bold text-gray-900" id="total-records">{{ $statistics['total'] }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-database text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Hôm nay</p>
                            <p class="text-3xl font-bold text-gray-900" id="today-records">{{ $statistics['today'] }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Barcode trùng</p>
                            <p class="text-3xl font-bold text-yellow-600" id="duplicate-records">{{ $statistics['duplicate'] }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Sai model</p>
                            <p class="text-3xl font-bold text-red-600" id="error-records">{{ $statistics['error'] }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Barcode hợp lệ</p>
                            <p class="text-3xl font-bold text-green-600" id="unique-records">{{ $statistics['unique'] }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" action="{{ url('/dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm Barcode</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập barcode..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kết quả</label>
                        <select name="ket_qua" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả</option>
                            @foreach($ketQuaList as $kq)
                                <option value="{{ $kq->ket_qua }}" {{ request('ket_qua') == $kq->ket_qua ? 'selected' : '' }}>{{ $kq->ket_qua }} ({{ $kq->total }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ca</label>
                        <select name="ca" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả</option>
                            @foreach($caStatistics as $ca)
                                <option value="{{ $ca->ca }}" {{ request('ca') == $ca->ca ? 'selected' : '' }}>{{ $ca->ca }} ({{ $ca->total }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex-1">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ url('/dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Records Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barcode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày giờ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ca</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($records as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $record->stt }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $record->barcode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $record->ngay_gio }}</td>
                                <td class="px-6 py-4">
                                    @php $ketQua = strtolower($record->ket_qua); @endphp
                                    @if(stripos($ketQua, 'sai') !== false || stripos($ketQua, 'model') !== false || stripos($ketQua, 'lỗi') !== false || stripos($ketQua, 'error') !== false)
                                        <span class="badge-danger text-white text-xs px-3 py-1 rounded-full"><i class="fas fa-times-circle"></i> {{ $record->ket_qua }}</span>
                                    @elseif(stripos($ketQua, 'trùng') !== false || stripos($ketQua, 'trung') !== false || stripos($ketQua, 'duplicate') !== false)
                                        <span class="badge-warning text-white text-xs px-3 py-1 rounded-full"><i class="fas fa-exclamation-triangle"></i> {{ $record->ket_qua }}</span>
                                    @else
                                        <span class="badge-success text-white text-xs px-3 py-1 rounded-full"><i class="fas fa-check-circle"></i> {{ $record->ket_qua }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($record->ca)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $record->ca }}</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button onclick="deleteRecord({{ $record->id }})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Chưa có dữ liệu</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">{{ $records->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900"><i class="fas fa-download text-green-600"></i> Xuất Excel</h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-500"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn khoảng thời gian</label>
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input type="radio" name="export_type" value="all" checked onchange="toggleDateInputs()" class="mr-2">
                            <span>Tất cả dữ liệu</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input type="radio" name="export_type" value="month" onchange="toggleDateInputs()" class="mr-2">
                            <span>Theo tháng</span>
                        </label>
                    </div>
                    <div id="monthInputs" class="hidden ml-6 mt-2">
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Tháng</label>
                            <input type="month" id="export_month" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input type="radio" name="export_type" value="range" onchange="toggleDateInputs()" class="mr-2">
                            <span>Tùy chỉnh khoảng thời gian</span>
                        </label>
                    </div>
                    <div id="rangeInputs" class="hidden ml-6 mt-2">
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Từ ngày</label>
                            <input type="date" id="export_from" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Đến ngày</label>
                            <input type="date" id="export_to" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button onclick="exportData()" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-download"></i> Xuất file
                    </button>
                    <button onclick="closeExportModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Hủy</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let autoRefresh = true;
        
        function refreshData() {
            fetch('/api/statistics').then(r => r.json()).then(d => {
                document.getElementById('total-records').textContent = d.statistics.total;
                document.getElementById('today-records').textContent = d.statistics.today;
                document.getElementById('duplicate-records').textContent = d.statistics.duplicate;
                document.getElementById('error-records').textContent = d.statistics.error;
                document.getElementById('unique-records').textContent = d.statistics.unique;
            });
        }

        function deleteRecord(id) {
            if (confirm('Bạn có chắc muốn xóa record này?')) {
                fetch(`/api/records/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
                }).then(r => r.json()).then(d => { alert(d.message); location.reload(); });
            }
        }

        function showExportModal() {
            document.getElementById('exportModal').classList.remove('hidden');
        }

        function closeExportModal() {
            document.getElementById('exportModal').classList.add('hidden');
        }

        function toggleDateInputs() {
            const type = document.querySelector('input[name="export_type"]:checked').value;
            document.getElementById('monthInputs').classList.add('hidden');
            document.getElementById('rangeInputs').classList.add('hidden');
            if (type === 'month') document.getElementById('monthInputs').classList.remove('hidden');
            else if (type === 'range') document.getElementById('rangeInputs').classList.remove('hidden');
        }

        function exportData() {
            const type = document.querySelector('input[name="export_type"]:checked').value;
            const params = new URLSearchParams(window.location.search);
            if (type === 'month') {
                const month = document.getElementById('export_month').value;
                if (month) params.set('month', month);
            } else if (type === 'range') {
                const from = document.getElementById('export_from').value;
                const to = document.getElementById('export_to').value;
                if (from) params.set('from', from);
                if (to) params.set('to', to);
            }
            window.location.href = '/export?' + params.toString();
            closeExportModal();
        }

        setInterval(() => { if (autoRefresh) refreshData(); }, 5000);
    </script>
</body>
</html>
