@extends('layouts.app')

@section('title', 'MonitorSuhu - Environmental Dashboard')

@push('styles')
<style>
    .card { border-radius: 12px; transition: all 0.3s ease; }
    .icon-shape { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .bg-purple { background: linear-gradient(135deg, #8a2be2, #4b0082); color: white; }
    .bg-blue { background: linear-gradient(135deg, #00bfff, #1e90ff); color: white; }
    .bg-orange { background: linear-gradient(135deg, #ff8c00, #ff4500); color: white; }
    .btn-purple { background-color: #8a2be2; border: none; color: white; }
    .btn-purple:hover { background-color: #7a1ed2; color: white; }
    .chart-container { position: relative; height: 280px; width: 100%; }
    
    /* Compact Control Box & Slider Styling */
    .control-box { background: #ffffff; padding: 8px 12px; border-radius: 10px; }
    .control-item { min-width: 145px; }
    .scale-input { 
        width: 52px; 
        height: 22px; 
        font-size: 0.75rem; 
        font-weight: 700; 
        text-align: center; 
        padding: 1px 2px;
    }
    
    .form-range { 
        height: 0.35rem; 
        margin-bottom: 6px; 
    }
    .form-range::-webkit-slider-thumb { 
        width: 0.85rem; 
        height: 0.85rem; 
        background: #0d6efd !important; 
        margin-top: -3px;
    }
    .form-range::-moz-range-thumb { 
        width: 0.85rem; 
        height: 0.85rem; 
        background: #0d6efd !important; 
    }

    /* Warning Trigger Card Animation */
    .card-warning {
        border: 2px solid #dc3545 !important;
        background-color: #fff5f5 !important;
        animation: pulseWarning 1.5s infinite;
    }
    @keyframes pulseWarning {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    /* Header Bar Styling */
    .rack-header-bar {
        padding: 12px 20px;
        border-radius: 16px 0 16px 0;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    .rack-bar-1 {
        background: linear-gradient(90deg, #1e1b4b 0%, #2e1065 100%);
        border: 1.5px solid #a855f7;
        color: #ffffff;
    }
    .rack-bar-2 {
        background: linear-gradient(90deg, #042f2e 0%, #064e3b 100%);
        border: 1.5px solid #2dd4bf;
        color: #ffffff;
    }

    .accordion-button:not(.collapsed) { background-color: #f1f5f9; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted small fw-semibold">/ Dashboard</span>
            <h3 class="fw-bold m-0">Environmental Monitoring</h3>
        </div>
        <button onclick="fetchLiveData()" class="btn btn-purple px-4 py-2 rounded-3 shadow-sm">
            <i class="fa-solid fa-rotate me-2"></i>Refresh Data
        </button>
    </div>

    <!-- Control Form for Threshold Settings -->

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <!-- Header with Icon & Subtitle -->
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9 3.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1-5 0M1 2.5A.5.5 0 0 1 1.5 2h6a.5.5 0 0 1 0 1h-6A.5.5 0 0 1 1 2.5M14.5 14a.5.5 0 0 1 .5.5v.001a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1 0-1h6a.5.5 0 0 1 .5.5M11.5 11a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9 12.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1-5 0M1 12.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M4.5 6a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2 7.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1-5 0M.5 7.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H1a.5.5 0 0 1-.5-.5M15 7.5a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1 0-1h6a.5.5 0 0 1 .5.5"/>
                </svg>
            </div>
            <div>
                <h5 class="card-title fw-bold text-dark mb-0">Buzzer Parameter</h5>
            </div>
        </div>

        <form id="thresholdForm" class="row g-3 align-items-end">
            <!-- Suhu Input -->
            <div class="col-md-5">
                <label for="temp_max" class="form-label fw-semibold text-secondary small mb-1">Suhu Maksimal (°C)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-thermometer-half" viewBox="0 0 16 16">
                            <path d="M9.5 12.5a1.5 1.5 0 1 1-2-1.415V2.5a.5.5 0 0 1 1 0v8.585a1.5 1.5 0 0 1 1 1.415"/>
                            <path d="M5.5 2.5a2.5 2.5 0 0 1 5 0v7.55a3.5 3.5 0 1 1-5 0zM8 1a1.5 1.5 0 0 0-1.5 1.5v7.987l-.167.15a2.5 2.5 0 1 0 3.334 0l-.166-.15V2.5A1.5 1.5 0 0 0 8 1"/>
                        </svg>
                    </span>
                    <input type="number" step="0.1" class="form-control bg-light border-start-0 ps-0 shadow-none" id="temp_max" name="temp_max" placeholder="32" required>
                </div>
            </div>

            <!-- Asap/Gas Input -->
            <div class="col-md-5">
                <label for="gas_max" class="form-label fw-semibold text-secondary small mb-1">Nilai Asap/Gas Maksimal</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-haze2" viewBox="0 0 16 16">
                            <path d="M8.5 2a4 4 0 0 0-3.8 2.745.5.5 0 1 1-.949-.313 5 5 0 0 1 9.654 1.89A3 3 0 0 1 13 10.5H3.105a2.5 2.5 0 0 1-.186-4.997.5.5 0 0 1 .28.96 1.5 1.5 0 0 0 .093 2.987H13a2 2 0 0 0 .225-3.987.5.5 0 0 1-.41-.421A4 4 0 0 0 8.5 2"/>
                        </svg>
                    </span>
                    <input type="number" class="form-control bg-light border-start-0 ps-0 shadow-none" id="gas_max" name="gas_max" placeholder="1500" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-md-2">
                <button type="submit" id="btnSave" class="btn btn-primary w-100 fw-semibold shadow-sm py-2">
                    Simpan
                </button>
            </div>
        </form>

        <!-- Dynamic Success / Error Alert Badge -->
        <div id="saveStatus" class="mt-3"></div>
    </div>
</div>

<script>
// Load active thresholds on initial page view
function loadThresholds() {
    fetch('/api/sensor-data/latest')
        .then(res => res.json())
        .then(data => {
            if (data.thresholds) {
                document.getElementById('temp_max').value = data.thresholds.temp_max;
                document.getElementById('gas_max').value = data.thresholds.gas_max;
            }
        });
}

// Handle form submission to update settings dynamically
document.getElementById('thresholdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSave');
    const statusDiv = document.getElementById('saveStatus');
    const temp = document.getElementById('temp_max').value;
    const gas = document.getElementById('gas_max').value;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    fetch('/api/settings/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ temp_max: temp, gas_max: gas })
    })
    .then(res => res.json())
    .then(data => {
        statusDiv.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show mb-0 py-2 px-3 small" role="alert">
                <strong>Berhasil!</strong> ${data.message}
            </div>`;
        setTimeout(() => { statusDiv.innerHTML = ''; }, 3500);
    })
    .catch(() => {
        statusDiv.innerHTML = `
            <div class="alert alert-danger mb-0 py-2 px-3 small" role="alert">
                Gagal memperbarui ambang batas. Periksa koneksi server.
            </div>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Simpan';
    });
});

loadThresholds();
</script>

<script>
    
// Load active thresholds on initial page view
function loadThresholds() {
    fetch('/api/sensor-data/latest')
        .then(res => res.json())
        .then(data => {
            if (data.thresholds) {
                document.getElementById('temp_max').value = data.thresholds.temp_max;
                document.getElementById('gas_max').value = data.thresholds.gas_max;
            }
        });
}

    // Handle form submission to update settings dynamically
    document.getElementById('thresholdForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const temp = document.getElementById('temp_max').value;
        const gas = document.getElementById('gas_max').value;

        fetch('/api/settings/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ temp_max: temp, gas_max: gas })
        })
        .then(res => res.json())
        .then(data => {
            const statusSpan = document.getElementById('saveStatus');
            statusSpan.innerText = "✅ " + data.message;
            setTimeout(() => statusSpan.innerText = '', 3000);
        });
    });

    loadThresholds();
    </script>

    <!-- Server Rack 1 -->
    <div id="rack-1" class="mb-5">
        <div class="rack-header-bar rack-bar-1 mb-3">
            <a href="{{ route('rack1') }}" class="text-white text-decoration-none">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-database me-2"></i>Server Rack 1</h5>
            </a>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r1-temp-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">
                                TEMPERATURE 
                                <span id="r1-temp-badge" class="badge bg-danger ms-1 d-none">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>HIGH
                                </span>
                            </small>
                            <h3 class="fw-bold my-1" id="r1-temp">{{ $latest && $latest->rack1_temp !== null ? number_format($latest->rack1_temp, 1) . ' °C' : '-- °C' }}</h3>
                        </div>
                        <div class="icon-shape bg-purple"><i class="fa-solid fa-temperature-high fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r1-humidity-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">HUMIDITY</small>
                            <h3 class="fw-bold my-1" id="r1-humidity">{{ $latest && $latest->rack1_humidity !== null ? number_format($latest->rack1_humidity, 1) . ' %' : '-- %' }}</h3>
                        </div>
                        <div class="icon-shape bg-blue"><i class="fa-solid fa-droplet fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r1-gas-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">
                                GAS LEVEL
                                <span id="r1-gas-badge" class="badge bg-danger ms-1 d-none">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>HIGH
                                </span>
                            </small>
                            <h3 class="fw-bold my-1" id="r1-gas">{{ $latest && $latest->rack1_gas !== null ? $latest->rack1_gas : '--' }}</h3>
                        </div>
                        <div class="icon-shape bg-orange"><i class="fa-solid fa-gauge fs-5"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rack 1 Chart Card -->
        <div class="card border-0 shadow-sm p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h6 class="fw-bold text-secondary m-0"><i class="fa-solid fa-chart-area me-2"></i>Server Rack 1 Telemetry History</h6>
                
                <div class="control-box border d-flex align-items-center gap-3 flex-wrap">
                    <!-- Temp Controls -->
                    <div class="control-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold" style="color: #8a2be2;">Max Temp:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-secondary-subtle" 
                                       id="r1-temp-input" value="64" min="1" max="200" style="color: #8a2be2;" 
                                       oninput="updateScale('rack1', 'temp', this.value, 'input')">
                                <span class="small fw-bold ms-1" style="color: #8a2be2;">°C</span>
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r1-temp-slider" min="10" max="100" step="1" value="64" 
                               oninput="updateScale('rack1', 'temp', this.value, 'slider')">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-danger">Warning:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-danger-subtle text-danger" 
                                       id="r1-temp-warn-input" value="40" min="1" max="200" 
                                       oninput="updateWarnThreshold('rack1', 'temp', this.value, 'input')">
                                <span class="small fw-bold ms-1 text-danger">°C</span>
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r1-temp-warn-slider" min="10" max="100" step="1" value="40" 
                               oninput="updateWarnThreshold('rack1', 'temp', this.value, 'slider')">
                    </div>

                    <div class="vr bg-secondary opacity-25 d-none d-sm-block" style="height: 65px;"></div>

                    <!-- Gas Controls -->
                    <div class="control-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold" style="color: #ff8c00;">Max Gas:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-secondary-subtle" 
                                       id="r1-gas-input" value="1290" min="10" max="5000" style="color: #ff8c00;" 
                                       oninput="updateScale('rack1', 'gas', this.value, 'input')">
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r1-gas-slider" min="100" max="4000" step="10" value="1290" 
                               oninput="updateScale('rack1', 'gas', this.value, 'slider')">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-danger">Warning:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-danger-subtle text-danger" 
                                       id="r1-gas-warn-input" value="620" min="10" max="5000" 
                                       oninput="updateWarnThreshold('rack1', 'gas', this.value, 'input')">
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r1-gas-warn-slider" min="100" max="4000" step="10" value="620" 
                               oninput="updateWarnThreshold('rack1', 'gas', this.value, 'slider')">
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="rack1Chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Server Rack 2 -->
    <div id="rack-2" class="mb-5">
        <div class="rack-header-bar rack-bar-2 mb-3">
            <a href="{{ route('rack2') }}" class="text-white text-decoration-none">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-database me-2"></i>Server Rack 2</h5>
            </a>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r2-temp-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">
                                TEMPERATURE
                                <span id="r2-temp-badge" class="badge bg-danger ms-1 d-none">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>HIGH
                                </span>
                            </small>
                            <h3 class="fw-bold my-1" id="r2-temp">{{ $latest && $latest->rack2_temp !== null ? number_format($latest->rack2_temp, 1) . ' °C' : '-- °C' }}</h3>
                        </div>
                        <div class="icon-shape bg-purple"><i class="fa-solid fa-temperature-high fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r2-humidity-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">HUMIDITY</small>
                            <h3 class="fw-bold my-1" id="r2-humidity">{{ $latest && $latest->rack2_humidity !== null ? number_format($latest->rack2_humidity, 1) . ' %' : '-- %' }}</h3>
                        </div>
                        <div class="icon-shape bg-blue"><i class="fa-solid fa-droplet fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" id="r2-gas-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold d-block">
                                GAS LEVEL
                                <span id="r2-gas-badge" class="badge bg-danger ms-1 d-none">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>HIGH
                                </span>
                            </small>
                            <h3 class="fw-bold my-1" id="r2-gas">{{ $latest && $latest->rack2_gas !== null ? $latest->rack2_gas : '--' }}</h3>
                        </div>
                        <div class="icon-shape bg-orange"><i class="fa-solid fa-gauge fs-5"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rack 2 Chart Card -->
        <div class="card border-0 shadow-sm p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h6 class="fw-bold text-secondary m-0"><i class="fa-solid fa-chart-area me-2"></i>Server Rack 2 Telemetry History</h6>
                
                <div class="control-box border d-flex align-items-center gap-3 flex-wrap">
                    <!-- Temp Controls -->
                    <div class="control-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold" style="color: #8a2be2;">Max Temp:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-secondary-subtle" 
                                       id="r2-temp-input" value="64" min="1" max="200" style="color: #8a2be2;" 
                                       oninput="updateScale('rack2', 'temp', this.value, 'input')">
                                <span class="small fw-bold ms-1" style="color: #8a2be2;">°C</span>
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r2-temp-slider" min="10" max="100" step="1" value="64" 
                               oninput="updateScale('rack2', 'temp', this.value, 'slider')">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-danger">Warning:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-danger-subtle text-danger" 
                                       id="r2-temp-warn-input" value="40" min="1" max="200" 
                                       oninput="updateWarnThreshold('rack2', 'temp', this.value, 'input')">
                                <span class="small fw-bold ms-1 text-danger">°C</span>
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r2-temp-warn-slider" min="10" max="100" step="1" value="40" 
                               oninput="updateWarnThreshold('rack2', 'temp', this.value, 'slider')">
                    </div>

                    <div class="vr bg-secondary opacity-25 d-none d-sm-block" style="height: 65px;"></div>

                    <!-- Gas Controls -->
                    <div class="control-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold" style="color: #ff8c00;">Max Gas:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-secondary-subtle" 
                                       id="r2-gas-input" value="1290" min="10" max="5000" style="color: #ff8c00;" 
                                       oninput="updateScale('rack2', 'gas', this.value, 'input')">
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r2-gas-slider" min="100" max="4000" step="10" value="1290" 
                               oninput="updateScale('rack2', 'gas', this.value, 'slider')">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-danger">Warning:</span>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control scale-input border-danger-subtle text-danger" 
                                       id="r2-gas-warn-input" value="620" min="10" max="5000" 
                                       oninput="updateWarnThreshold('rack2', 'gas', this.value, 'input')">
                            </div>
                        </div>
                        <input type="range" class="form-range" id="r2-gas-warn-slider" min="100" max="4000" step="10" value="620" 
                               oninput="updateWarnThreshold('rack2', 'gas', this.value, 'slider')">
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="rack2Chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Logs Accordion -->
    <div id="recent-logs" class="card border-0 shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0"><i class="fa-solid fa-list me-2"></i>Recent Sensor Logs</h5>
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-2">
                <i class="fa-solid fa-circle-dot me-1"></i> Live Polling Active
            </span>
        </div>

        <div class="accordion border-0 d-flex flex-column gap-3" id="logsAccordion">
            <div class="accordion-item border rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="headingRack1">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRack1">
                        <i class="fa-solid fa-database me-2" style="color: #8a2be2;"></i> Server Rack 1 Logs
                    </button>
                </h2>
                <div id="collapseRack1" class="accordion-collapse collapse" data-bs-parent="#logsAccordion">
                    <div class="accordion-body p-0 border-top">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>TIMESTAMP</th>
                                        <th>TEMP (°C)</th>
                                        <th>HUMIDITY (%)</th>
                                        <th>GAS LEVEL</th>
                                    </tr>
                                </thead>
                                <tbody id="r1-logs-body">
                                    @forelse($readings as $row)
                                        <tr>
                                            <td class="fw-semibold text-secondary">{{ $row->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $row->rack1_temp }} °C</td>
                                            <td>{{ $row->rack1_humidity }} %</td>
                                            <td>{{ $row->rack1_gas }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No Rack 1 sensor data received yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item border rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="headingRack2">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRack2">
                        <i class="fa-solid fa-database me-2" style="color: #0d9488;"></i> Server Rack 2 Logs
                    </button>
                </h2>
                <div id="collapseRack2" class="accordion-collapse collapse" data-bs-parent="#logsAccordion">
                    <div class="accordion-body p-0 border-top">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>TIMESTAMP</th>
                                        <th>TEMP (°C)</th>
                                        <th>HUMIDITY (%)</th>
                                        <th>GAS LEVEL</th>
                                    </tr>
                                </thead>
                                <tbody id="r2-logs-body">
                                    @forelse($readings as $row)
                                        <tr>
                                            <td class="fw-semibold text-secondary">{{ $row->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $row->rack2_temp }} °C</td>
                                            <td>{{ $row->rack2_humidity }} %</td>
                                            <td>{{ $row->rack2_gas }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No Rack 2 sensor data received yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let latestTelemetry = null;

    const chartLimits = {
        rack1: {
            temp: parseInt(localStorage.getItem('r1_temp_max')) || 64,
            gas: parseInt(localStorage.getItem('r1_gas_max')) || 1290
        },
        rack2: {
            temp: parseInt(localStorage.getItem('r2_temp_max')) || 64,
            gas: parseInt(localStorage.getItem('r2_gas_max')) || 1290
        }
    };

    const warnThresholds = {
        rack1: {
            temp: parseFloat(localStorage.getItem('r1_temp_warn')) || 40,
            gas: parseInt(localStorage.getItem('r1_gas_warn')) || 620
        },
        rack2: {
            temp: parseFloat(localStorage.getItem('r2_temp_warn')) || 40,
            gas: parseInt(localStorage.getItem('r2_gas_warn')) || 620
        }
    };

    function createRackChartConfig(rackKey) {
        return {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Temp (°C)',
                        data: [],
                        borderColor: '#8a2be2',
                        backgroundColor: 'rgba(138, 43, 226, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'yTemp'
                    },
                    {
                        label: 'Gas',
                        data: [],
                        borderColor: '#ff8c00',
                        backgroundColor: 'rgba(255, 140, 0, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'yGas'
                    },
                    {
                        label: 'Humidity (%)',
                        data: [],
                        borderColor: '#00bfff',
                        backgroundColor: 'rgba(0, 191, 255, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'yHumidity'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false } },
                    yTemp: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Temp (°C)', color: '#8a2be2' },
                        ticks: { color: '#8a2be2' },
                        min: 0,
                        max: chartLimits[rackKey].temp
                    },
                    yGas: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Gas', color: '#ff8c00' },
                        ticks: { color: '#ff8c00' },
                        grid: { drawOnChartArea: false },
                        min: 0,
                        max: chartLimits[rackKey].gas
                    },
                    yHumidity: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Humidity (%)', color: '#00bfff' },
                        ticks: { color: '#00bfff' },
                        grid: { drawOnChartArea: false },
                        min: 0,
                        max: 100
                    }
                }
            }
        };
    }

    const rack1Chart = new Chart(document.getElementById('rack1Chart').getContext('2d'), createRackChartConfig('rack1'));
    const rack2Chart = new Chart(document.getElementById('rack2Chart').getContext('2d'), createRackChartConfig('rack2'));

    function initScaleControls() {
        ['rack1', 'rack2'].forEach(rack => {
            const prefix = rack === 'rack1' ? 'r1' : 'r2';
            
            const tempVal = chartLimits[rack].temp;
            document.getElementById(`${prefix}-temp-slider`).value = tempVal;
            document.getElementById(`${prefix}-temp-input`).value = tempVal;
            
            const gasVal = chartLimits[rack].gas;
            document.getElementById(`${prefix}-gas-slider`).value = gasVal;
            document.getElementById(`${prefix}-gas-input`).value = gasVal;

            const tempWarn = warnThresholds[rack].temp;
            document.getElementById(`${prefix}-temp-warn-slider`).value = tempWarn;
            document.getElementById(`${prefix}-temp-warn-input`).value = tempWarn;

            const gasWarn = warnThresholds[rack].gas;
            document.getElementById(`${prefix}-gas-warn-slider`).value = gasWarn;
            document.getElementById(`${prefix}-gas-warn-input`).value = gasWarn;
        });
    }

    function updateScale(rack, param, value, source) {
        let val = parseInt(value);
        if (isNaN(val) || val <= 0) return;

        chartLimits[rack][param] = val;
        const chart = rack === 'rack1' ? rack1Chart : rack2Chart;
        const prefix = rack === 'rack1' ? 'r1' : 'r2';
        
        localStorage.setItem(`${prefix}_${param}_max`, val);

        const slider = document.getElementById(`${prefix}-${param}-slider`);
        const numInput = document.getElementById(`${prefix}-${param}-input`);

        if (source !== 'slider' && slider) slider.value = val;
        if (source !== 'input' && numInput) numInput.value = val;

        if (param === 'temp') chart.options.scales.yTemp.max = val;
        else if (param === 'gas') chart.options.scales.yGas.max = val;
        chart.update();
    }

    function updateWarnThreshold(rack, param, value, source) {
        let val = parseFloat(value);
        if (isNaN(val) || val < 0) return;

        warnThresholds[rack][param] = val;
        const prefix = rack === 'rack1' ? 'r1' : 'r2';
        localStorage.setItem(`${prefix}_${param}_warn`, val);

        const slider = document.getElementById(`${prefix}-${param}-warn-slider`);
        const numInput = document.getElementById(`${prefix}-${param}-warn-input`);

        if (source !== 'slider' && slider) slider.value = val;
        if (source !== 'input' && numInput) numInput.value = val;

        evaluateWarnings();
    }

    function checkWarningState(metricVal, thresholdVal, cardId, badgeId) {
        const card = document.getElementById(cardId);
        const badge = document.getElementById(badgeId);

        if (metricVal !== null && !isNaN(metricVal) && metricVal >= thresholdVal) {
            if (card) card.classList.add('card-warning');
            if (badge) badge.classList.remove('d-none');
        } else {
            if (card) card.classList.remove('card-warning');
            if (badge) badge.classList.add('d-none');
        }
    }

    function evaluateWarnings() {
        if (!latestTelemetry) return;

        checkWarningState(latestTelemetry.rack1_temp, warnThresholds.rack1.temp, 'r1-temp-card', 'r1-temp-badge');
        checkWarningState(latestTelemetry.rack1_gas, warnThresholds.rack1.gas, 'r1-gas-card', 'r1-gas-badge');

        checkWarningState(latestTelemetry.rack2_temp, warnThresholds.rack2.temp, 'r2-temp-card', 'r2-temp-badge');
        checkWarningState(latestTelemetry.rack2_gas, warnThresholds.rack2.gas, 'r2-gas-card', 'r2-gas-badge');
    }

    window.addEventListener('storage', (e) => {
        if (e.key && (e.key.endsWith('_warn') || e.key.endsWith('_max'))) {
            chartLimits.rack1.temp = parseInt(localStorage.getItem('r1_temp_max')) || 64;
            chartLimits.rack1.gas = parseInt(localStorage.getItem('r1_gas_max')) || 1290;
            chartLimits.rack2.temp = parseInt(localStorage.getItem('r2_temp_max')) || 64;
            chartLimits.rack2.gas = parseInt(localStorage.getItem('r2_gas_max')) || 1290;

            warnThresholds.rack1.temp = parseFloat(localStorage.getItem('r1_temp_warn')) || 40;
            warnThresholds.rack1.gas = parseInt(localStorage.getItem('r1_gas_warn')) || 620;
            warnThresholds.rack2.temp = parseFloat(localStorage.getItem('r2_temp_warn')) || 40;
            warnThresholds.rack2.gas = parseInt(localStorage.getItem('r2_gas_warn')) || 620;

            initScaleControls();
            evaluateWarnings();
        }
    });

    async function fetchLiveData() {
        try {
            const response = await fetch('/api/latest-data');
            const data = await response.json();

            if (data.latest) {
                latestTelemetry = data.latest;

                document.getElementById('r1-temp').innerText = (data.latest.rack1_temp ?? '--') + ' °C';
                document.getElementById('r1-humidity').innerText = (data.latest.rack1_humidity ?? '--') + ' %';
                document.getElementById('r1-gas').innerText = data.latest.rack1_gas ?? '--';

                document.getElementById('r2-temp').innerText = (data.latest.rack2_temp ?? '--') + ' °C';
                document.getElementById('r2-humidity').innerText = (data.latest.rack2_humidity ?? '--') + ' %';
                document.getElementById('r2-gas').innerText = data.latest.rack2_gas ?? '--';

                evaluateWarnings();
            }

            if (data.readings && data.readings.length > 0) {
                const chronologicalReadings = [...data.readings].reverse();
                const timeLabels = chronologicalReadings.map(r => new Date(r.created_at).toLocaleTimeString());

                rack1Chart.data.labels = timeLabels;
                rack1Chart.data.datasets[0].data = chronologicalReadings.map(r => r.rack1_temp);
                rack1Chart.data.datasets[1].data = chronologicalReadings.map(r => r.rack1_gas);
                rack1Chart.data.datasets[2].data = chronologicalReadings.map(r => r.rack1_humidity);
                rack1Chart.update('none');

                rack2Chart.data.labels = timeLabels;
                rack2Chart.data.datasets[0].data = chronologicalReadings.map(r => r.rack2_temp);
                rack2Chart.data.datasets[1].data = chronologicalReadings.map(r => r.rack2_gas);
                rack2Chart.data.datasets[2].data = chronologicalReadings.map(r => r.rack2_humidity);
                rack2Chart.update('none');

                let r1RowsHtml = '';
                let r2RowsHtml = '';

                data.readings.forEach(row => {
                    const formattedTime = new Date(row.created_at).toISOString().replace('T', ' ').substring(0, 19);
                    
                    r1RowsHtml += `
                        <tr>
                            <td class="fw-semibold text-secondary">${formattedTime}</td>
                            <td>${row.rack1_temp ?? '--'} °C</td>
                            <td>${row.rack1_humidity ?? '--'} %</td>
                            <td>${row.rack1_gas ?? '--'}</td>
                        </tr>
                    `;

                    r2RowsHtml += `
                        <tr>
                            <td class="fw-semibold text-secondary">${formattedTime}</td>
                            <td>${row.rack2_temp ?? '--'} °C</td>
                            <td>${row.rack2_humidity ?? '--'} %</td>
                            <td>${row.rack2_gas ?? '--'}</td>
                        </tr>
                    `;
                });

                document.getElementById('r1-logs-body').innerHTML = r1RowsHtml;
                document.getElementById('r2-logs-body').innerHTML = r2RowsHtml;
            }
        } catch (error) {
            console.error("Failed fetching live telemetry:", error);
        }
    }

    initScaleControls();
    fetchLiveData();
    setInterval(fetchLiveData, 3000);
</script>
@endpush