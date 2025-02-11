@extends('layouts.admin')

@section('title', 'Reconciliation')
@section('page-title', 'Reconciliation')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Reconciliation</div>
                <div class="card-body">
                    
                    <!-- Filter Section -->
                    <form method="GET" action="{{ route('gss.admin.reconciliation') }}" id="filterForm">
                    <div class="row mb-3">
                            <!-- Status Dropdown -->
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Serviceable" {{ request('status') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                                    <option value="Transferred" {{ request('status') == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="Unserviceable" {{ request('status') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                                </select>
                            </div>

                            <!-- Equipment Type -->
                            <div class="col-md-3">
                                <label for="equipment_type">Equipment Type</label>
                                <select name="equipment_type" id="equipment_type" class="form-control">
                                    <option value="all" {{ request('equipment_type') == 'all' ? 'selected' : '' }}>All</option>
                                    <option value="semi-expendable" {{ request('equipment_type') == 'semi-expendable' ? 'selected' : '' }}>Semi-Expendables</option>
                                    <option value="ppe" {{ request('equipment_type') == 'ppe' ? 'selected' : '' }}>PPE</option>
                                </select>
                            </div>

                            <!-- Date Covered Period -->
                            <div class="col-md-3">
                                <label for="from_date">From</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date">To</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <!-- Filter & Reset Buttons -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('gss.admin.reconciliation') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Section -->
                    <div id="statistics">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box bg-dark text-white p-4 rounded shadow-lg">
                                    <div class="info-box-content text-center">
                                        <span class="info-box-text font-weight-bold">Total Semi-Expendable</span>
                                        <span id="totalSemiExpendable" class="info-box-number display-4">{{ number_format($totalAmountSemiExpendable, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-box bg-dark text-white p-4 rounded shadow-lg">
                                    <div class="info-box-content text-center">
                                        <span class="info-box-text font-weight-bold">Total PPE</span>
                                        <span id="totalPPE" class="info-box-number display-4">{{ number_format($totalAmountPPE, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-box bg-dark text-white p-4 rounded shadow-lg">
                                    <div class="info-box-content text-center">
                                        <span class="info-box-text font-weight-bold">Total for Selected Status</span>
                                        <span id="totalSelectedStatus" class="info-box-number display-4">{{ number_format($totalAmountForSelectedStatus, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    

                    <!-- Export & Print Buttons -->
                    <div class="mb-3">
                        <button id="exportExcel" class="btn btn-success">Download Excel</button>
                        <button id="printTable" class="btn btn-primary">Print Report</button>
                    </div>

                    <!-- Reconciliation Table -->
                    <table class="table table-bordered" id="reconciliationTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Property Number</th>
                                <th>Particular</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reconcileRecords as $record)
                                <tr>
                                    <td>{{ $loop->iteration + ($reconcileRecords->currentPage() - 1) * $reconcileRecords->perPage() }}</td>
                                    <td>{{ $record->property_number }}</td>
                                    <td>{{ $record->particular }}</td>
                                    <td>{{ number_format($record->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->date ?? '')->format('Y-m-d') }}</td>
                                    <td>{{ $record->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        {{ $reconcileRecords->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- SheetJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script>
    // Auto-submit form on filter change
    document.getElementById('filterForm').addEventListener('change', function() {
        this.submit();
    });

    document.getElementById("printTable").addEventListener("click", function () {
    let status = document.getElementById("status").value;
    let equipmentType = document.getElementById("equipment_type").value;
    let fromDate = document.getElementById("from_date").value;
    let toDate = document.getElementById("to_date").value;

    fetch(`{{ route('gss.admin.exportExcel') }}?status=${status}&equipment_type=${equipmentType}&from_date=${fromDate}&to_date=${toDate}`)
        .then(response => response.json())
        .then(data => {
            let printContent = `<h2>Reconciliation Report</h2><table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Property Number</th>
                        <th>Particular</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>`;

            data.data.forEach((record, index) => {
                printContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${record.property_number}</td>
                        <td>${record.particular}</td>
                        <td>${parseFloat(record.amount).toFixed(2)}</td>
                        <td>${new Date(record.date).toISOString().split('T')[0]}</td>
                        <td>${record.status}</td>
                    </tr>`;
            });

            printContent += `</tbody></table>`;

            let printWindow = window.open("", "", "width=900,height=600");
            printWindow.document.write("<html><head><title>Reconciliation Report</title>");
            printWindow.document.write("<style>body { font-family: Arial, sans-serif; } table { width: 100%; border-collapse: collapse; margin-top: 20px; } th, td { border: 1px solid #000; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style>");
            printWindow.document.write("</head><body>");
            printWindow.document.write(printContent);
            printWindow.document.write("</body></html>");
            printWindow.document.close();
            printWindow.print();
        })
        .catch(error => {
            alert("Error fetching data for print.");
            console.error(error);
        });
});
</script>

<script>
   document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("exportExcel").addEventListener("click", function () {
        let status = document.getElementById('status').value;
        let equipmentType = document.getElementById('equipment_type').value;
        let fromDate = document.getElementById('from_date').value;
        let toDate = document.getElementById('to_date').value;

        fetch(`{{ route('gss.admin.exportExcel') }}?status=${status}&equipment_type=${equipmentType}&from_date=${fromDate}&to_date=${toDate}`)
            .then(response => response.json())
            .then(data => {
                let wb = XLSX.utils.book_new();
                let wsData = [["ID", "Property Number", "Particular", "Amount", "Date", "Status"]];

                data.data.forEach(record => {
                    wsData.push([
                        record.id,
                        record.property_number,
                        record.particular,
                        parseFloat(record.amount).toFixed(2),
                        new Date(record.date).toISOString().split('T')[0], // Format Date
                        record.status
                    ]);
                });

                let ws = XLSX.utils.aoa_to_sheet(wsData);
                XLSX.utils.book_append_sheet(wb, ws, "Reconciliation");
                XLSX.writeFile(wb, "Reconciliation_Report.xlsx");
            })
            .catch(error => {
                alert("Error exporting data.");
                console.error(error);
            });
    });


    let statusDropdown = document.getElementById("status");
    let equipmentTypeDropdown = document.getElementById("equipment_type");
    

    function updateEquipmentTypeOptions() {
        let status = statusDropdown.value;
        
        // Define the available options
        let options = `
            <option value="all">All</option>
            <option value="semi-expendable">Semi-Expendables</option>
            <option value="ppe">PPE</option>
        `;

        // Apply options only when status is "Serviceable", "Transferred", or "Unserviceable"
        if (["Serviceable", "Transferred", "Unserviceable"].includes(status)) {
            equipmentTypeDropdown.innerHTML = options;
        } else {
            equipmentTypeDropdown.innerHTML = `<option value="all">All</option>`;
        }

        // Preserve selected value after refresh
        let selectedEquipmentType = "{{ request('equipment_type') }}";
        if (selectedEquipmentType) {
            equipmentTypeDropdown.value = selectedEquipmentType;
        }
    }

    // Call function on page load to set correct dropdown options
    updateEquipmentTypeOptions();

    // Change event for status dropdown
    statusDropdown.addEventListener("change", function () {
        updateEquipmentTypeOptions();
        setTimeout(() => filterForm.submit(), 1000); // Add a slight delay to prevent multiple submissions
    });


    let filterForm = document.getElementById("filterForm");

    filterForm.addEventListener("change", function () {
        let formData = new FormData(filterForm);

        fetch("{{ route('gss.admin.reconciliation') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("totalSemiExpendable").innerText = new Intl.NumberFormat().format(data.totalAmountSemiExpendable);
            document.getElementById("totalPPE").innerText = new Intl.NumberFormat().format(data.totalAmountPPE);
            document.getElementById("totalSelectedStatus").innerText = new Intl.NumberFormat().format(data.totalAmountForSelectedStatus);
        })
        .catch(error => console.error("Error updating statistics:", error));
    });

    let fromDateInput = document.getElementById("from_date");
    let toDateInput = document.getElementById("to_date");
    let filterForm = document.getElementById("filterForm");

    function debounce(func, timeout = 500){
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    function submitForm() {
        filterForm.submit();
    }

    let debouncedSubmit = debounce(submitForm, 800); // Ensures the form submits only after typing stops

    fromDateInput.addEventListener("input", debouncedSubmit);
    toDateInput.addEventListener("input", debouncedSubmit);

});

</script>
@endsection
