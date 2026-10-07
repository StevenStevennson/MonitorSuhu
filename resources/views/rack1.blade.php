@extends('layouts.app')

@section('title', 'Server Rack 1 - MonitorSuhu')

@push('styles')
<style>
    .edit-active .editable-val {
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px dashed #198754;
        background-color: #f8f9fa;
        transition: all 0.15s ease-in-out;
    }
    .edit-active .editable-val:hover {
        background-color: #e2e6ea;
    }
    .warning-slider::-webkit-slider-thumb {
        background: #dc3545 !important;
    }
</style>
@endpush

@section('content')
    <div class="mb-4">
        <span class="text-muted small fw-semibold">/ Server List / Server Rack 1</span>
        <h3 class="fw-bold m-0 mt-1">Server Rack 1 Overview</h3>
    </div>

    <!-- Rack Specifications & Safety Limits Card -->
    <div class="card border-0 shadow-sm p-4 mb-4" id="rackSpecsCard">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark m-0">
                <i class="fa-solid fa-circle-info text-primary me-2"></i>Rack Specifications & Safety Controls
            </h5>
            <button id="toggleEditBtn" class="btn btn-primary btn-sm shadow-sm rounded-2 px-3">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Details
            </button>
        </div>

        <div id="editHintBanner" class="alert alert-success py-2 px-3 small d-none mb-3">
            <i class="fa-solid fa-i-cursor me-2"></i> <strong>Edit Mode Active:</strong> Double-click any field to edit. Click "Save Changes" when done.
        </div>

        <div class="row g-4">
            <!-- Left Column: General Info -->
            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-sliders me-2"></i>General Information</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Location / Room:</span>
                        <span class="fw-semibold editable-val" data-field="location">{{ $rack->location }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Assigned ESP32 Node:</span>
                        <span class="fw-semibold editable-val" data-field="esp_node">{{ $rack->esp_node }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">IP Address:</span>
                        <span class="fw-semibold editable-val" data-field="ip_address">{{ $rack->ip_address }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Primary Tech Contact:</span>
                        <span class="fw-semibold editable-val" data-field="tech_contact">{{ $rack->tech_contact }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Last Maintenance:</span>
                        <span class="fw-semibold editable-val" data-field="last_maintenance">{{ $rack->last_maintenance }}</span>
                    </li>
                </ul>
            </div>

            <!-- Right Column: Live Warning Threshold Sliders -->
            <div class="col-md-6">
                <h6 class="fw-bold text-danger mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i>Live Warning Thresholds</h6>
                
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <!-- Warning Temperature Control -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-danger m-0">Warning Temperature:</label>
                            <div class="d-flex align-items-center">
                                <input type="number" id="detail-warn-temp-input" class="form-control form-control-sm text-center fw-bold border-danger-subtle text-danger" 
                                       style="width: 70px;" min="10" max="100" 
                                       oninput="updateWarnTemp(this.value, 'input')">
                                <span class="small fw-bold text-danger ms-1">°C</span>
                            </div>
                        </div>
                        <input type="range" class="form-range warning-slider" id="detail-warn-temp-slider" min="10" max="100" step="1" 
                               oninput="updateWarnTemp(this.value, 'slider')">
                    </div>

                    <!-- Warning Gas Control -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-danger m-0">Warning Gas:</label>
                            <div class="d-flex align-items-center">
                                <input type="number" id="detail-warn-gas-input" class="form-control form-control-sm text-center fw-bold border-danger-subtle text-danger" 
                                       style="width: 80px;" min="100" max="4000" step="10" 
                                       oninput="updateWarnGas(this.value, 'input')">
                                <span class="small fw-bold text-danger ms-1">PPM</span>
                            </div>
                        </div>
                        <input type="range" class="form-range warning-slider" id="detail-warn-gas-slider" min="100" max="4000" step="10" 
                               oninput="updateWarnGas(this.value, 'slider')">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize thresholds from localStorage or set defaults
    function loadThresholds() {
        const warnTemp = parseFloat(localStorage.getItem('r1_temp_warn')) || 35;
        const warnGas = parseInt(localStorage.getItem('r1_gas_warn')) || 400;

        document.getElementById('detail-warn-temp-input').value = warnTemp;
        document.getElementById('detail-warn-temp-slider').value = warnTemp;
        document.getElementById('detail-warn-gas-input').value = warnGas;
        document.getElementById('detail-warn-gas-slider').value = warnGas;
    }

    // Function to update Temp threshold locally & broadcast to localStorage
    function updateWarnTemp(val, source) {
        let numVal = parseFloat(val);
        if (isNaN(numVal)) return;

        localStorage.setItem('r1_temp_warn', numVal);

        if (source !== 'slider') document.getElementById('detail-warn-temp-slider').value = numVal;
        if (source !== 'input') document.getElementById('detail-warn-temp-input').value = numVal;
    }

    // Function to update Gas threshold locally & broadcast to localStorage
    function updateWarnGas(val, source) {
        let numVal = parseInt(val);
        if (isNaN(numVal)) return;

        localStorage.setItem('r1_gas_warn', numVal);

        if (source !== 'slider') document.getElementById('detail-warn-gas-slider').value = numVal;
        if (source !== 'input') document.getElementById('detail-warn-gas-input').value = numVal;
    }

    // Automatically sync when changed on the main dashboard or another browser tab
    window.addEventListener('storage', (e) => {
        if (e.key === 'r1_temp_warn') {
            const newTemp = localStorage.getItem('r1_temp_warn');
            if (newTemp !== null) {
                document.getElementById('detail-warn-temp-input').value = newTemp;
                document.getElementById('detail-warn-temp-slider').value = newTemp;
            }
        }
        if (e.key === 'r1_gas_warn') {
            const newGas = localStorage.getItem('r1_gas_warn');
            if (newGas !== null) {
                document.getElementById('detail-warn-gas-input').value = newGas;
                document.getElementById('detail-warn-gas-slider').value = newGas;
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadThresholds();

        const editBtn = document.getElementById('toggleEditBtn');
        const editBanner = document.getElementById('editHintBanner');
        const specsCard = document.getElementById('rackSpecsCard');
        let isEditMode = false;

        // 1. Toggle Button
        editBtn.addEventListener('click', function () {
            isEditMode = !isEditMode;

            if (isEditMode) {
                editBtn.classList.replace('btn-primary', 'btn-success');
                editBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Save Changes';
                editBanner.classList.remove('d-none');
                specsCard.classList.add('edit-active');
            } else {
                editBtn.classList.replace('btn-success', 'btn-primary');
                editBtn.innerHTML = '<i class="fa-solid fa-pen-to-square me-1"></i> Edit Details';
                editBanner.classList.add('d-none');
                specsCard.classList.remove('edit-active');

                // Close active inputs
                specsCard.querySelectorAll('input.editable-input').forEach(input => commitInput(input));

                // Gather and send payload
                const payload = {};
                specsCard.querySelectorAll('.editable-val').forEach(el => {
                    payload[el.getAttribute('data-field')] = el.innerText.trim();
                });

                fetch('{{ route("racks.update", $rack->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log('Saved successfully to database!');
                    }
                })
                .catch(err => console.error('Save failed:', err));
            }
        });

        // 2. Double Click Handling
        specsCard.addEventListener('dblclick', function (e) {
            const target = e.target.closest('.editable-val');
            if (!target || !isEditMode || target.tagName === 'INPUT') return;

            const currentText = target.innerText;
            const classes = target.className;
            const fieldName = target.getAttribute('data-field');

            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentText;
            input.className = 'form-control form-control-sm fw-semibold editable-input shadow-sm';
            input.style.width = '200px';
            input.style.display = 'inline-block';
            input.setAttribute('data-original-classes', classes);
            input.setAttribute('data-field', fieldName);

            input.addEventListener('blur', () => commitInput(input));
            input.addEventListener('keydown', (evt) => {
                if (evt.key === 'Enter') commitInput(input);
            });

            target.replaceWith(input);
            input.focus();
            input.select();
        });

        function commitInput(input) {
            const span = document.createElement('span');
            span.className = input.getAttribute('data-original-classes');
            span.setAttribute('data-field', input.getAttribute('data-field'));
            span.innerText = input.value.trim() || '—';
            input.replaceWith(span);
        }
    });
</script>
@endpush