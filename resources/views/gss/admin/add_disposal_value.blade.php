@extends('layouts.admin')

@section('title', 'Add Disposal Value')
@section('page-title', 'Add Disposal Value')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add Disposal Value</div>
                <div class="card-body">

                 <!-- Flash Messages -->
                 @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Disposal Value Form -->
                    <form id="disposalForm" action="{{ route('gss.admin.store_disposal_value') }}" method="POST">
                        @csrf

                        <!-- Search & Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Search</label>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by Property Number, Particular">
                            </div>
                            <div class="col-md-3">
                                <label>Equipment Type</label>
                                <select id="equipmentType" class="form-control">
                                    <option value="all">All</option>
                                    <option value="PPE">PPE (≥50,000)</option>
                                    <option value="Semi-Expendables">Semi-Expendables (<50,000)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>From</label>
                                <input type="date" id="fromDate" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>To</label>
                                <input type="date" id="toDate" class="form-control">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button id="resetFilters" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Particular</th>
                                    <th>Amount</th>
                                    <th>Lifespan</th>
                                    <th>Date Acquired</th>
                                    <th>Date End</th>
                                    <th>Date Returned</th>
                                    <th>Years, Months, Days</th>
                                    <th class="accumulated-depreciation">Accumulated Depreciation</th>
                                    <th class="carrying-value">Carrying Value</th>
                                </tr>
                            </thead>
                            <tbody id="recordsTable">
                                @foreach($recordsWithDepreciation as $index => $record)
                                    <tr>
                                        <td><input type="checkbox" name="selected_items[]" class="select-item" value="{{ $record['unserviceable_id'] }}"></td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $record['unserviceable_id'] }}</td>
                                        <td>{{ $record['property_number'] }}</td>
                                        <td>{{ $record['particular'] }}</td>
                                        <td class="amount">{{ number_format((float) str_replace(',', '', $record['amount']), 2) }}</td>
                                        <td>{{ $record['lifespan'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($record['date_acquired'])->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($record['date_end'])->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($record['date_returned'])->format('Y-m-d') }}</td>
                                        <td>{{ $record['years_months_days'] }}</td>
                                        <td><input type="text" name="accumulated_depreciation[{{ $record['unserviceable_id'] }}]" class="form-control accumulated-depreciation" value="{{ $record['amount'] >= 50000 ? $record['accumulated_depreciation'] : 'N/A' }}" {{ $record['amount'] < 50000 ? 'disabled' : '' }}></td>
                                        <td><input type="text" name="carrying_values[{{ $record['unserviceable_id'] }}]" class="form-control carrying-value" value="{{ $record['amount'] >= 50000 ? $record['carrying_value'] : 'N/A' }}" {{ $record['amount'] < 50000 ? 'disabled' : '' }}></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Submit Button -->
                        <button type="submit" id="submitDisposal" class="btn btn-primary">Add Disposal Value</button>
                    </form>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between" id="paginationContainer">
                        {{ $unserviceableRecords->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Warning</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Please select at least one item before adding disposal value.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Disposal values added successfully!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>



<!-- JavaScript -->

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById("searchInput");
    let equipmentTypeDropdown = document.getElementById("equipmentType");
    let fromDateInput = document.getElementById("fromDate");
    let toDateInput = document.getElementById("toDate");
    let disposalForm = document.getElementById("disposalForm");
    let warningModalElement = document.getElementById("warningModal");
    let successModalElement = document.getElementById("successModal");
    let selectAllCheckbox = document.getElementById("selectAll");

    // ✅ Ensure modals exist before initializing
    let warningModal = warningModalElement ? new bootstrap.Modal(warningModalElement) : null;
    let successModal = successModalElement ? new bootstrap.Modal(successModalElement) : null;

    // ✅ Fix: "Select All" checkbox functionality
    selectAllCheckbox.addEventListener("change", function () {
        document.querySelectorAll(".select-item").forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    // ✅ Function to fetch and display filtered data dynamically
    function fetchFilteredData() {
        let searchValue = searchInput.value.trim();
        let selectedType = equipmentTypeDropdown.value;
        let fromDate = fromDateInput.value;
        let toDate = toDateInput.value;

        let url = "{{ route('gss.admin.add_disposal_value') }}";
        let params = new URLSearchParams({
            search: searchValue,
            equipment_type: selectedType,
            from_date: fromDate,
            to_date: toDate
        });

        fetch(`${url}?${params}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data.records);

            let tableBody = document.querySelector("#recordsTable");
            if (!data.records || !Array.isArray(data.records)) {
                console.error("Error: Expected an array but got", data.records);
                return;
            }

            // ✅ Update table dynamically
            tableBody.innerHTML = data.records.length > 0
                ? data.records.map((record, index) => `
                    <tr>
                        <td><input type="checkbox" name="selected_items[]" value="${record.unserviceable_id}" class="select-item"></td>
                        <td>${index + 1}</td>
                        <td>${record.unserviceable_id}</td>
                        <td>${record.property_number || "N/A"}</td>
                        <td>${record.particular || "N/A"}</td>
                        <td class="amount">${record.amount ? parseFloat(record.amount).toLocaleString() : "0.00"}</td>
                        <td>${record.lifespan || "N/A"}</td>
                        <td>${record.date_acquired || "N/A"}</td>
                        <td>${record.date_end || "N/A"}</td>
                        <td>${record.date_returned || "N/A"}</td>
                        <td>${record.years_months_days || "N/A"}</td>
                        <td><input type="text" name="accumulated_depreciation[${record.unserviceable_id}]" class="form-control accumulated-depreciation" value="${record.amount >= 50000 ? record.accumulated_depreciation : 'N/A'}" ${record.amount < 50000 ? 'disabled' : ''}></td>
                        <td><input type="text" name="carrying_values[${record.unserviceable_id}]" class="form-control carrying-value" value="${record.amount >= 50000 ? record.carrying_value : 'N/A'}" ${record.amount < 50000 ? 'disabled' : ''}></td>
                    </tr>
                `).join("")
                : "<tr><td colspan='13' class='text-center'>No records found.</td></tr>";

        })
        .catch(error => console.error("Error fetching data:", error));
    }

    // ✅ Debounce function to avoid too many requests
    function debounce(func, timeout = 500) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    let debouncedFetch = debounce(fetchFilteredData, 800);

    // ✅ Attach event listeners for real-time filtering
    searchInput.addEventListener("keyup", debouncedFetch);
    equipmentTypeDropdown.addEventListener("change", debouncedFetch);
    fromDateInput.addEventListener("change", debouncedFetch);
    toDateInput.addEventListener("change", debouncedFetch);

    fetchFilteredData(); // ✅ Fetch initial data on page load

    // ✅ Ensure "N/A" is applied to Accumulated Depreciation & Carrying Value for amounts below 50,000
    function updateFields() {
        document.querySelectorAll("tbody tr").forEach(row => {
            let amountEl = row.querySelector(".amount");
            let accDepEl = row.querySelector(".accumulated-depreciation");
            let carryingValEl = row.querySelector(".carrying-value");

            if (!amountEl || !accDepEl || !carryingValEl) {
                console.error("Missing table column in row:", row.innerHTML);
                return;
            }

            let amount = parseFloat(amountEl.textContent.replace(/,/g, "")) || 0;

            if (amount < 50000) {
                accDepEl.value = "N/A";
                accDepEl.disabled = true;
                carryingValEl.value = "N/A";
                carryingValEl.disabled = true;
            } else {
                accDepEl.disabled = false;
                carryingValEl.disabled = false;
            }
        });
    }

    setTimeout(updateFields, 1000);

    // ✅ Handle Disposal Form Submission
    disposalForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission
        
        let checkedBoxes = document.querySelectorAll(".select-item:checked").length;
        if (checkedBoxes === 0) {
            if (warningModal) warningModal.show();
            return;
        }

        let formData = new FormData(disposalForm);
        fetch(disposalForm.action, {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Disposal values added successfully.");
                
                // ✅ Show success modal instead of redirecting
                if (successModal) successModal.show();

                // ✅ Refresh page after success modal is shown for a few seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert("Error: " + (data.message || "Something went wrong."));
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // ✅ Ensure reset button does not trigger warning modal
    let resetButton = document.getElementById("resetFilters");
    resetButton.addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("searchInput").value = "";
        document.getElementById("equipmentType").value = "all";
        document.getElementById("fromDate").value = "";
        document.getElementById("toDate").value = "";
        fetchFilteredData();
    });

    // ✅ Prevent form submission on Enter key inside search bar
    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            fetchFilteredData();
        }
    });

});
</script>


@endsection
