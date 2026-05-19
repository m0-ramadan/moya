@extends('Admin.layout.master')

@section('title', 'خريطة السائقين')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Map Page */
        .map-page {
            height: calc(100vh - 150px);
            position: relative;
        }

        /* Map Container */
        #map {
            height: 100%;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        /* Side Panel */
        .map-side-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 350px;
            background: var(--bs-card-bg);
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: calc(100% - 40px);
        }

        /* Panel Header */
        .panel-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .panel-header h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .panel-header p {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 15px;
            background: var(--bs-light-bg-subtle);
        }

        .stat-item {
            background: var(--bs-card-bg);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #696cff;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        /* Search Box */
        .search-container {
            padding: 15px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid var(--bs-border-color);
            border-radius: 25px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bs-secondary-color);
        }

        .search-results {
            position: absolute;
            top: 100%;
            right: 0;
            left: 0;
            background: var(--bs-card-bg);
            border-radius: 10px;
            margin-top: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 1001;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: var(--bs-light-bg-subtle);
        }

        .result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            overflow: hidden;
        }

        .result-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .result-info h6 {
            font-size: 14px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .result-info p {
            font-size: 12px;
            color: var(--bs-secondary-color);
            margin-bottom: 0;
        }

        .result-badge {
            margin-right: auto;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
        }

        .result-badge.online {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .result-badge.offline {
            background: var(--bs-secondary-bg-subtle);
            color: var(--bs-secondary-text);
        }

        /* Filters */
        .filters-container {
            padding: 15px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .filter-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 10px;
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .filter-btn {
            padding: 6px 12px;
            border: 1px solid var(--bs-border-color);
            border-radius: 20px;
            background: transparent;
            color: var(--bs-secondary-color);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background: var(--bs-light-bg-subtle);
        }

        .filter-btn.active {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .filter-checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .filter-checkbox label {
            font-size: 13px;
            color: var(--bs-heading-color);
            cursor: pointer;
        }

        /* Drivers List */
        .drivers-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .driver-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: var(--bs-light-bg-subtle);
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .driver-item:hover {
            background: var(--bs-card-bg);
            border-color: #696cff;
            transform: translateX(-5px);
        }

        .driver-item.active {
            border-color: #696cff;
            background: var(--bs-card-bg);
        }

        .driver-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            overflow: hidden;
            flex-shrink: 0;
        }

        .driver-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .driver-info {
            flex: 1;
        }

        .driver-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .driver-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .driver-meta span {
            font-size: 12px;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .driver-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .driver-badge.online {
            background: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2);
        }

        .driver-badge.busy {
            background: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
        }

        .driver-order-status {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        /* Map Controls */
        .map-controls {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .map-control-btn {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: var(--bs-card-bg);
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            color: var(--bs-heading-color);
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .map-control-btn:hover {
            background: #696cff;
            color: white;
            transform: translateY(-3px);
        }

        /* Driver Info Window */
        .driver-info-window {
            min-width: 250px;
            padding: 5px;
        }

        .info-window-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-window-avatar {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            overflow: hidden;
        }

        .info-window-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-window-title h6 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .info-window-title p {
            font-size: 12px;
            color: #666;
            margin-bottom: 0;
        }

        .info-window-details {
            margin-bottom: 10px;
        }

        .info-window-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .info-window-label {
            width: 80px;
            color: #666;
        }

        .info-window-value {
            flex: 1;
            font-weight: 600;
        }

        .info-window-order {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 10px;
        }

        .info-window-order-title {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-window-order-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background: #fff3cd;
            color: #856404;
            font-size: 11px;
        }

        .info-window-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .info-window-btn {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            border: none;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }

        .info-window-btn.view {
            background: #696cff;
            color: white;
        }

        .info-window-btn.track {
            background: #198754;
            color: white;
        }

        /* Legend */
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: var(--bs-card-bg);
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .legend-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--bs-heading-color);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .legend-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .legend-marker.available {
            background: #198754;
        }

        .legend-marker.busy {
            background: #dc3545;
        }

        .legend-marker.offline {
            background: #6c757d;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .map-side-panel {
                width: 300px;
            }
        }

        @media (max-width: 768px) {
            .map-page {
                height: calc(100vh - 200px);
            }

            .map-side-panel {
                position: fixed;
                bottom: 0;
                right: 0;
                left: 0;
                top: auto;
                width: 100%;
                max-height: 50vh;
                border-radius: 20px 20px 0 0;
            }

            .map-controls {
                bottom: 55vh;
            }

            .map-legend {
                bottom: 55vh;
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            border-radius: 15px;
            display: none;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Marker */
        .custom-marker {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            border: 3px solid;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .custom-marker.available {
            border-color: #198754;
        }

        .custom-marker.busy {
            border-color: #dc3545;
        }

        .custom-marker img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .custom-marker i {
            font-size: 18px;
            color: #333;
        }

        .custom-marker.pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.drivers.index') }}">السائقين</a>
                </li>
                <li class="breadcrumb-item active">خريطة السائقين</li>
            </ol>
        </nav>

        <div class="map-page">
            <!-- Map Container -->
            <div id="map"></div>

            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
            </div>

            <!-- Side Panel -->
            <div class="map-side-panel">
                <div class="panel-header">
                    <h5>خريطة السائقين</h5>
                    <p>تتبع مواقع السائقين في الوقت الفعلي</p>
                </div>

                <!-- Stats -->
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-value" id="totalOnline">{{ $stats['online_now'] }}</div>
                        <div class="stat-label">متصل الآن</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="totalOnDelivery">{{ $stats['on_delivery'] }}</div>
                        <div class="stat-label">قيد التوصيل</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="totalAvailable">{{ $stats['available'] }}</div>
                        <div class="stat-label">متاح</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="totalDrivers">{{ $stats['total_drivers'] }}</div>
                        <div class="stat-label">إجمالي السائقين</div>
                    </div>
                </div>

                <!-- Search -->
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="بحث عن سائق بالاسم أو رقم اللوحة...">
                        <i class="fas fa-search"></i>
                        <div class="search-results" id="searchResults"></div>
                    </div>
                </div>
                @php
                    // get all vehicle sizes from the database
                    $vehicle_sizes = DB::table('services')->get();
                @endphp
                <!-- Filters -->
                <div class="filters-container">
                    <div class="filter-title">حجم المركبة</div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-size="all">الكل</button>
                        @foreach ($vehicle_sizes as $size)
                            <button class="filter-btn" data-size="{{ $size->name }}">{{ $size->name }}</button>
                        @endforeach
                    </div>

                    <div class="filter-title">الحالة</div>
                    <div class="filter-checkbox">
                        <input type="checkbox" id="filterVerified" value="verified">
                        <label for="filterVerified">موثق فقط</label>
                    </div>
                    <div class="filter-checkbox">
                        <input type="checkbox" id="filterWithOrder" value="with_order">
                        <label for="filterWithOrder">لديه طلب نشط</label>
                    </div>
                    <div class="filter-checkbox">
                        <input type="checkbox" id="filterAvailable" value="available">
                        <label for="filterAvailable">متاح فقط</label>
                    </div>
                </div>

                <!-- Drivers List -->
                <div class="drivers-list" id="driversList">
                    @forelse($drivers as $driver)
                        <div class="driver-item" data-id="{{ $driver['id'] }}" data-has-location="{{ $driver['location'] ? '1' : '0' }}" onclick="focusDriver({{ $driver['id'] }})">
                            <div class="driver-avatar" style="position:relative;">
                                @if($driver['avatar'])
                                    <img src="{{ $driver['avatar'] }}" alt="{{ $driver['name'] }}">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                                @if(!$driver['location'])
                                    <span style="position:absolute;bottom:-3px;right:-3px;width:12px;height:12px;background:#6c757d;border-radius:50%;border:2px solid #fff;"></span>
                                @elseif($driver['current_order'])
                                    <span style="position:absolute;bottom:-3px;right:-3px;width:12px;height:12px;background:#dc3545;border-radius:50%;border:2px solid #fff;"></span>
                                @else
                                    <span style="position:absolute;bottom:-3px;right:-3px;width:12px;height:12px;background:#198754;border-radius:50%;border:2px solid #fff;"></span>
                                @endif
                            </div>
                            <div class="driver-info">
                                <div class="driver-name" style="display:flex;align-items:center;gap:5px;">
                                    {{ $driver['name'] }}
                                    @if($driver['is_verified'])
                                        <i class="fas fa-check-circle text-primary" style="font-size:11px;" title="موثق"></i>
                                    @endif
                                </div>
                                <div class="driver-meta">
                                    <span><i class="fas fa-car"></i> {{ $driver['vehicle']['plate'] }}</span>
                                    <span><i class="fas fa-star text-warning"></i> {{ number_format($driver['stats']['rating'], 1) }}</span>
                                </div>
                                <div class="driver-meta" style="margin-top:3px;">
                                    @if(!$driver['location'])
                                        <span class="badge" style="background:#6c757d;color:#fff;font-size:10px;"><i class="fas fa-wifi-slash"></i> غير متصل</span>
                                    @elseif($driver['current_order'])
                                        <span class="badge" style="background:#dc3545;color:#fff;font-size:10px;">{{ $driver['current_order']['status_text'] }}</span>
                                    @elseif($driver['is_available'])
                                        <span class="badge" style="background:#198754;color:#fff;font-size:10px;"><i class="fas fa-circle"></i> متاح</span>
                                    @else
                                        <span class="badge" style="background:#ffc107;color:#000;font-size:10px;">غير متاح</span>
                                    @endif
                                    @if($driver['location'])
                                        <span style="font-size:10px;color:#6c757d;"><i class="fas fa-location-dot"></i> {{ $driver['location']['last_updated'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>لا يوجد سائقون</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Map Controls -->
            <div class="map-controls">
                <button class="map-control-btn" onclick="centerMap()" title="توسيط الخريطة">
                    <i class="fas fa-crosshairs"></i>
                </button>
                <button class="map-control-btn" onclick="refreshLocations()" title="تحديث">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="map-control-btn" onclick="toggleFullscreen()" title="شاشة كاملة">
                    <i class="fas fa-expand"></i>
                </button>
            </div>

            <!-- Legend -->
            <div class="map-legend">
                <div class="legend-title">دليل الألوان</div>
                <div class="legend-item">
                    <span class="legend-marker available"></span>
                    <span>متاح (بدون طلب)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker busy"></span>
                    <span>مشغول (لديه طلب)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker offline"></span>
                    <span>غير متصل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pusher Script -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize Pusher
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            encrypted: true
        });

        // Subscribe to driver locations channel
        const channel = pusher.subscribe('driver-locations');
        
        // Listen for location updates
        channel.bind('DriverLocationUpdated', function(data) {
            updateDriverLocation(data);
        });

        // Map variables
        let map;
        let markers = {};
        let markerCluster;
        let refreshInterval;
        let selectedDriverId = null;

        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadDrivers();
            startRealTimeUpdates();
            initSearch();
            initFilters();
        });

        function initMap() {
            // Default center (Saudi Arabia)
            map = L.map('map').setView([24.7136, 46.6753], 10);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Initialize marker cluster
            markerCluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                spiderfyOnMaxZoom: true
            });
            map.addLayer(markerCluster);
        }

        function loadDrivers() {
            showLoading();
            fetch('{{ route("admin.drivers.map.locations") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        updateMarkers(data.drivers);
                        updateStats(data.stats);
                    } else {
                        console.error('Error:', data.message);
                    }
                    hideLoading();
                })
                .catch(err => { console.error('Error loading drivers:', err); hideLoading(); });
        }

        function updateMarkers(drivers) {
            // Clear existing markers
            markerCluster.clearLayers();
            markers = {};

            let withLocation = 0;
            drivers.forEach(driver => {
                if (driver.location) {
                    addMarker(driver);
                    withLocation++;
                }
            });

            // Update sidebar list
            updateDriversList(drivers);

            if (withLocation === 0) {
                console.info('لا يوجد سائقون لديهم مواقع GPS حالياً');
            }
        }

        function updateDriversList(drivers) {
            const list = document.getElementById('driversList');
            if (!list) return;
            let html = '';
            drivers.forEach(driver => {
                const statusDot = !driver.location
                    ? 'background:#6c757d'
                    : (driver.has_order ? 'background:#dc3545' : 'background:#198754');
                const statusBadge = !driver.location
                    ? '<span style="background:#6c757d;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;">غير متصل</span>'
                    : (driver.has_order
                        ? `<span style="background:#dc3545;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;">${driver.order_status}</span>`
                        : '<span style="background:#198754;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;">متاح</span>');
                const lastUpdated = driver.location ? `<span style="font-size:10px;color:#6c757d;"><i class="fas fa-location-dot"></i> ${driver.location.last_updated}</span>` : '';
                html += `
                <div class="driver-item" data-id="${driver.id}" onclick="focusDriver(${driver.id})">
                    <div class="driver-avatar" style="position:relative;">
                        ${driver.avatar ? `<img src="${driver.avatar}" alt="${driver.name}">` : '<i class="fas fa-user"></i>'}
                        <span style="position:absolute;bottom:-3px;right:-3px;width:12px;height:12px;${statusDot};border-radius:50%;border:2px solid #fff;"></span>
                    </div>
                    <div class="driver-info">
                        <div class="driver-name">${driver.name} ${driver.is_verified ? '<i class="fas fa-check-circle text-primary" style="font-size:11px;"></i>' : ''}</div>
                        <div class="driver-meta">
                            <span><i class="fas fa-car"></i> ${driver.vehicle?.plate ?? '--'}</span>
                            <span><i class="fas fa-star text-warning"></i> ${Number(driver.stats?.rating ?? 0).toFixed(1)}</span>
                        </div>
                        <div class="driver-meta" style="margin-top:3px;gap:5px;">${statusBadge}${lastUpdated}</div>
                    </div>
                </div>`;
            });
            list.innerHTML = html || '<div class="text-center py-4 text-muted"><i class="fas fa-users fa-2x mb-2"></i><p>لا يوجد سائقون</p></div>';
        }

        function addMarker(driver) {
            const markerColor = driver.has_order ? '#dc3545' : '#198754';
            
            // Create custom icon
            const icon = L.divIcon({
                className: 'custom-marker ' + (driver.has_order ? 'busy' : 'available'),
                html: driver.avatar 
                    ? `<img src="${driver.avatar}" alt="${driver.name}">`
                    : `<i class="fas fa-user"></i>`,
                iconSize: [40, 40],
                popupAnchor: [0, -20]
            });

            // Create marker
            const marker = L.marker([driver.location.lat, driver.location.lng], { 
                icon: icon,
                riseOnHover: true
            });

            // Create popup content
            const popupContent = `
                <div class="driver-info-window">
                    <div class="info-window-header">
                        <div class="info-window-avatar">
                            ${driver.avatar 
                                ? `<img src="${driver.avatar}" alt="${driver.name}">`
                                : `<i class="fas fa-user"></i>`
                            }
                        </div>
                        <div class="info-window-title">
                            <h6>${driver.name}</h6>
                            <p>${driver.vehicle.plate}</p>
                        </div>
                    </div>
                    <div class="info-window-details">
                        <div class="info-window-row">
                            <span class="info-window-label">الحالة:</span>
                            <span class="info-window-value">
                                ${driver.has_order 
                                    ? `<span class="text-danger">مشغول - ${driver.order_status}</span>`
                                    : '<span class="text-success">متاح</span>'
                                }
                            </span>
                        </div>
                        <div class="info-window-row">
                            <span class="info-window-label">السرعة:</span>
                            <span class="info-window-value">${driver.location.speed || 0} كم/س</span>
                        </div>
                    </div>
                    ${driver.has_order ? `
                        <div class="info-window-order">
                            <div class="info-window-order-title">الطلب الحالي</div>
                            <span class="info-window-order-status">${driver.order_status}</span>
                        </div>
                    ` : ''}
                    <div class="info-window-actions">
                        <button class="info-window-btn view" onclick="viewDriver(${driver.id})">
                            <i class="fas fa-user"></i> عرض التفاصيل
                        </button>
                        <button class="info-window-btn track" onclick="trackDriver(${driver.id})">
                            <i class="fas fa-location-arrow"></i> تتبع
                        </button>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, {
                minWidth: 250,
                maxWidth: 300
            });

            marker.on('click', function() {
                highlightDriver(driver.id);
            });

            // Add to cluster
            markerCluster.addLayer(marker);
            markers[driver.id] = marker;
        }

        function updateDriverLocation(data) {
            const driverId = data.driver?.id;
            
            if (driverId && markers[driverId]) {
                const marker = markers[driverId];
                const newLatLng = [data.location.latitude, data.location.longitude];
                
                // Update marker position
                marker.setLatLng(newLatLng);
                
                // Update popup content if open
                if (marker.isPopupOpen()) {
                    // Refresh driver data
                    fetchDriverDetails(driverId);
                }
                
                // Animate marker
                marker.getElement().classList.add('pulse');
                setTimeout(() => {
                    marker.getElement().classList.remove('pulse');
                }, 1000);
            }
        }

        function fetchDriverDetails(driverId) {
            fetch(`/admin/driver-map/driver/${driverId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && markers[driverId]) {
                        const marker = markers[driverId];
                        const driver = data.driver;
                        
                        // Update popup content
                        const popupContent = generatePopupContent(driver);
                        marker.setPopupContent(popupContent);
                    }
                });
        }

        function generatePopupContent(driver) {
            return `
                <div class="driver-info-window">
                    <div class="info-window-header">
                        <div class="info-window-avatar">
                            ${driver.avatar 
                                ? `<img src="${driver.avatar}" alt="${driver.name}">`
                                : `<i class="fas fa-user"></i>`
                            }
                        </div>
                        <div class="info-window-title">
                            <h6>${driver.name}</h6>
                            <p>${driver.vehicle.plate}</p>
                        </div>
                    </div>
                    <div class="info-window-details">
                        <div class="info-window-row">
                            <span class="info-window-label">الحالة:</span>
                            <span class="info-window-value">
                                ${driver.current_order 
                                    ? `<span class="text-danger">مشغول - ${driver.current_order.status}</span>`
                                    : '<span class="text-success">متاح</span>'
                                }
                            </span>
                        </div>
                        <div class="info-window-row">
                            <span class="info-window-label">السرعة:</span>
                            <span class="info-window-value">${driver.location?.speed || 0} كم/س</span>
                        </div>
                        <div class="info-window-row">
                            <span class="info-window-label">التقييم:</span>
                            <span class="info-window-value">
                                ${driver.stats?.avg_rating || 0} <i class="fas fa-star text-warning"></i>
                            </span>
                        </div>
                    </div>
                    ${driver.current_order ? `
                        <div class="info-window-order">
                            <div class="info-window-order-title">الطلب الحالي</div>
                            <div class="info-window-row">
                                <span class="info-window-label">العميل:</span>
                                <span class="info-window-value">${driver.current_order.customer}</span>
                            </div>
                            <div class="info-window-row">
                                <span class="info-window-label">الحالة:</span>
                                <span class="info-window-order-status">${driver.current_order.status}</span>
                            </div>
                        </div>
                    ` : ''}
                    <div class="info-window-actions">
                        <a href="/admin/drivers/${driver.id}" class="info-window-btn view">
                            <i class="fas fa-user"></i> عرض التفاصيل
                        </a>
                        <button class="info-window-btn track" onclick="trackDriver(${driver.id})">
                            <i class="fas fa-location-arrow"></i> تتبع
                        </button>
                    </div>
                </div>
            `;
        }

        function startRealTimeUpdates() {
            // Refresh locations every 30 seconds
            refreshInterval = setInterval(() => {
                refreshLocations();
            }, 30000);
        }

        function refreshLocations() {
            loadDrivers();
        }

        function focusDriver(driverId) {
            if (markers[driverId]) {
                const marker = markers[driverId];
                const latLng = marker.getLatLng();
                
                map.setView([latLng.lat, latLng.lng], 16);
                marker.openPopup();
                
                highlightDriver(driverId);
            }
        }

        function highlightDriver(driverId) {
            // Remove highlight from all drivers
            document.querySelectorAll('.driver-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add highlight to selected driver
            const driverItem = document.querySelector(`.driver-item[data-id="${driverId}"]`);
            if (driverItem) {
                driverItem.classList.add('active');
                driverItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            
            selectedDriverId = driverId;
        }

        function trackDriver(driverId) {
            if (markers[driverId]) {
                const marker = markers[driverId];
                
                // Start tracking (follow marker)
                const trackInterval = setInterval(() => {
                    if (markers[driverId]) {
                        const latLng = markers[driverId].getLatLng();
                        map.setView([latLng.lat, latLng.lng], 16);
                    } else {
                        clearInterval(trackInterval);
                    }
                }, 5000);
                
                // Stop tracking after 1 minute or when user moves map
                setTimeout(() => {
                    clearInterval(trackInterval);
                }, 60000);
                
                map.once('dragstart', () => {
                    clearInterval(trackInterval);
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'تتبع السائق',
                    text: 'سيتم تحديث موقع السائق تلقائياً كل 5 ثوان',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        function viewDriver(driverId) {
            window.location.href = `/admin/drivers/${driverId}`;
        }

        function centerMap() {
            if (selectedDriverId && markers[selectedDriverId]) {
                const latLng = markers[selectedDriverId].getLatLng();
                map.setView([latLng.lat, latLng.lng], 16);
            } else {
                map.setView([24.7136, 46.6753], 10);
            }
        }

        function toggleFullscreen() {
            const elem = document.querySelector('.map-page');
            
            if (!document.fullscreenElement) {
                elem.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        function updateStats(stats) {
            if (stats.online_now    !== undefined) document.getElementById('totalOnline').textContent     = stats.online_now;
            if (stats.on_delivery   !== undefined) document.getElementById('totalOnDelivery').textContent = stats.on_delivery;
            if (stats.available     !== undefined) document.getElementById('totalAvailable').textContent  = stats.available;
            if (stats.total_drivers !== undefined) document.getElementById('totalDrivers').textContent    = stats.total_drivers;
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Search functionality
        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 500);
            });

            // Close search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }

        function performSearch(query) {
            fetch(`/admin/driver-map/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.drivers);
                    }
                });
        }

        function displaySearchResults(drivers) {
            const resultsDiv = document.getElementById('searchResults');
            
            if (drivers.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-center text-muted">لا توجد نتائج</div>';
                resultsDiv.style.display = 'block';
                return;
            }
            
            let html = '';
            drivers.forEach(driver => {
                html += `
                    <div class="search-result-item" onclick="selectSearchResult(${driver.id})">
                        <div class="result-avatar">
                            ${driver.avatar 
                                ? `<img src="${driver.avatar}" alt="${driver.name}">`
                                : '<i class="fas fa-user"></i>'
                            }
                        </div>
                        <div class="result-info">
                            <h6>${driver.name}</h6>
                            <p>${driver.phone} - ${driver.plate}</p>
                        </div>
                        <span class="result-badge ${driver.has_location ? 'online' : 'offline'}">
                            ${driver.has_location ? 'متصل' : 'غير متصل'}
                        </span>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
        }

        function selectSearchResult(driverId) {
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('searchInput').value = '';
            
            if (markers[driverId]) {
                focusDriver(driverId);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'السائق غير متصل',
                    text: 'هذا السائق غير متصل حالياً',
                    confirmButtonText: 'حسناً'
                });
            }
        }

        // Filters
        function initFilters() {
            // Size filters
            document.querySelectorAll('[data-size]').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('[data-size]').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    applyFilters();
                });
            });

            // Checkbox filters
            document.getElementById('filterVerified').addEventListener('change', applyFilters);
            document.getElementById('filterWithOrder').addEventListener('change', applyFilters);
            document.getElementById('filterAvailable').addEventListener('change', applyFilters);
        }

        function applyFilters() {
            const size = document.querySelector('[data-size].active')?.dataset.size || 'all';
            const verified = document.getElementById('filterVerified').checked;
            const withOrder = document.getElementById('filterWithOrder').checked;
            const available = document.getElementById('filterAvailable').checked;
            
            // Build filter query
            const params = new URLSearchParams();
            if (size !== 'all') params.append('vehicle_size', size);
            params.append('is_verified', verified);
            if (withOrder) params.append('order_status', 'has_order');
            if (available) params.append('order_status', 'available');
            
            fetch(`/admin/driver-map/filter?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMarkers(data.drivers);
                    }
                });
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            pusher.unsubscribe('driver-locations');
        });
    </script>
@endsection