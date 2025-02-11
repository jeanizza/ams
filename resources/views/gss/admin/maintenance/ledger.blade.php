@extends('layouts.admin')

@section('title', 'Maintenance Ledger')
@section('page-title', 'Maintenance Ledger')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Maintenance Ledger</span>
                </div>
                <div class="card-body">
                    <!-- Search form -->
                    <form method="GET" action="{{ route('gss.admin.ledger.search') }}">
                        <div class="input-group mb-3">
                            <!-- Add Button for Opening Modal -->
                            <button type="button" class="btn btn-sm btn-success" id="openMaintenanceModal">
                                <i class="fas fa-plus"></i> Add Maintenance Ledger
                            </button>

                            <!-- Waste Material Button (Hidden Initially) -->
                            <button type="button" class="btn btn-sm btn-danger ms-2 d-none" id="processWasteMaterial">
                                <i class="fas fa-trash"></i> Process Waste Material
                            </button>

                            <!-- Print Waste Material Button (Hidden Initially) -->
                            <button type="button" class="btn btn-sm btn-secondary ms-2 d-none" id="printWasteMaterial">
                                <i class="fas fa-print"></i> Print Waste Material
                            </button>

                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="property_number" placeholder="Enter Property Number">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>

                    <!-- Display ledger details -->
                    @if(isset($ledgerDetails))
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Purchase Order No.</th>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Supplier</th>
                                    <th>Defects</th>
                                    <th>Particulars</th>
                                    <th>Unit Cost</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $hasWasteMaterial = false; @endphp
                                @forelse($ledgerDetails as $detail)
                                    <tr>
                                        <td>{{ ($ledgerDetails->currentPage() - 1) * $ledgerDetails->perPage() + $loop->iteration }}</td>
                                        <td>{{ $detail->po_number }}</td>
                                        <td>{{ $detail->date_delivered }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $detail->unit }}</td>
                                        <td>{{ $detail->supplier }}</td>
                                        <td>{{ $detail->defects }}</td>
                                        <td>{{ $detail->particular_ledger }}</td>
                                        <td>{{ number_format(floatval($detail->unit_cost), 2) }}</td>
                                        <td>{{ number_format(floatval($detail->total_amount), 2) }}</td>
                                        <td class="remarks">{{ $detail->remarks }}</td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-sm openMaintenanceModal"
                                            data-id="{{ $detail->maintenance_ledger_id }}">Update</a>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="waste-material-checkbox" value="{{ $detail->maintenance_ledger_id }}"
                                                data-remarks="{{ $detail->remarks }}">
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10" class="text-end">Total:</td>
                                    <td>{{ number_format($ledgerDetails->sum('total_amount'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Maintenance Ledger Modal -->
<div class="modal fade" id="addMaintenanceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaintenanceModalLabel">Add / Update Maintenance Ledger</h5>
                <button type="button" class="btn-close allow-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <!-- The form will be dynamically loaded here -->
            </div>
            
        </div>
    </div>
</div>

<!-- Include jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>




<script>
$(document).ready(function () {
    function openMaintenanceModal(maintenanceId = '') {
        let url = "{{ url('gss/admin/maintenance/add-details') }}";
        if (maintenanceId) {
            url += "/" + maintenanceId;
        }

        console.log("Opening modal with URL:", url);

        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function () {
                $('#modal-body-content').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
            },
            success: function (response) {
                console.log("AJAX Response:", response);
                $('#modal-body-content').html(response); // Inject form into modal

                let maintenanceModal = new bootstrap.Modal(document.getElementById('addMaintenanceModal'), {
                    backdrop: 'static', // Prevent closing on outside click
                    keyboard: false     // Prevent closing with ESC
                });

                maintenanceModal.show();

                // Store modal instance globally for later use
                $('#addMaintenanceModal').data('bs.modal', maintenanceModal);
            },
            error: function (xhr) {
                console.error("Error loading modal:", xhr.responseText);
                $('#modal-body-content').html('<div class="alert alert-danger">Error loading content.</div>');
            }
        });
    }

    // Open modal for adding a new ledger
    $("#openMaintenanceModal").on("click", function (e) {
        e.preventDefault();
        openMaintenanceModal();
    });

    // Open modal for updating an existing ledger
    $(document).on("click", ".openMaintenanceModal", function (e) {
        e.preventDefault();
        let maintenanceId = $(this).data("id");
        openMaintenanceModal(maintenanceId);
    });

    // Ensure modal does NOT close when clicking outside or pressing ESC
    $('#addMaintenanceModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    // Close modal when clicking Cancel button or X button
    $(document).on("click", ".allow-close, .btn-close", function () {
        let maintenanceModal = $('#addMaintenanceModal').data('bs.modal');
        if (maintenanceModal) {
            maintenanceModal.hide();
        }
    });
});
</script>

<script>
$(document).ready(function () {
    let processWasteMaterialBtn = $('#processWasteMaterial');
    let printWasteMaterialBtn = $('#printWasteMaterial');

    // Hide buttons initially
    processWasteMaterialBtn.addClass('d-none');
    printWasteMaterialBtn.addClass('d-none');

    // Function to update the button based on selections
    function updateWasteMaterialButton() {
        let selectedItems = $('.waste-material-checkbox:checked');
        let selectedRemarks = selectedItems.map(function () {
            return $(this).data('remarks');
        }).get();

        let processedCount = selectedRemarks.filter(remark => remark.includes("Processed as Waste Material")).length;
        let unprocessedCount = selectedRemarks.length - processedCount;

        if (selectedItems.length === 0) {
            // Hide buttons when no items are selected
            processWasteMaterialBtn.addClass('d-none');
            printWasteMaterialBtn.addClass('d-none');
        } else if (processedCount > unprocessedCount) {
            // If majority are "Processed as Waste Material", show Print button
            processWasteMaterialBtn.addClass('d-none');
            printWasteMaterialBtn.removeClass('d-none');
        } else {
            // If at least one is not processed, show Process button
            processWasteMaterialBtn.removeClass('d-none');
            printWasteMaterialBtn.addClass('d-none');
        }
    }

    // Event listener for checkbox change
    $(document).on('change', '.waste-material-checkbox', function () {
        updateWasteMaterialButton();
    });

    // Process Waste Material Function
    $('#processWasteMaterial').on('click', function () {
        let selectedItems = $('.waste-material-checkbox:checked').map(function () {
        let row = $(this).closest('tr');
        let remarks = row.find("td:nth-child(11)").text().trim(); // Adjust index if needed

        // Ensure only items that are NOT processed yet
        if (!remarks.includes("Processed as Waste Material")) {
            return {
                id: $(this).val(),
               // po_number: row.find("td:nth-child(2)").text().trim(), // Purchase Order No.
              //  date: row.find("td:nth-child(3)").text().trim(), // Date
                quantity: row.find("td:nth-child(4)").text().trim(), // Quantity
                unit: row.find("td:nth-child(5)").text().trim(), // Unit
              //  supplier: row.find("td:nth-child(6)").text().trim(), // Supplier
              //  defects: row.find("td:nth-child(7)").text().trim(), // Defects
                particulars: row.find("td:nth-child(8)").text().trim() // Particulars
            };
        }
    }).get();

    if (selectedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Items Selected',
            text: 'Please select items to process as waste material.',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You are about to process selected items as waste material.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Process Them!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('gss.admin.process_waste_material') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    maintenance_ledger_ids: selectedItems.map(item => item.id)
                },
                beforeSend: function () {
                    $('#processWasteMaterial').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                },
                success: function (response) {
                    // Generate Excel after successful processing
                    generateExcel(selectedItems);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Selected items have been processed as waste material.',
                        confirmButtonColor: '#28a745',
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Processing Failed!',
                        text: 'An error occurred while processing waste material.',
                        confirmButtonColor: '#d33',
                    });
                },
                complete: function () {
                    $('#processWasteMaterial').prop('disabled', false).html('<i class="fas fa-trash"></i> Process Waste Material');
                }
            });
        }
    });
});

// Function to Generate Excel File
function generateExcel(data) {
    let wb = XLSX.utils.book_new();
    let wsData = [["No.", "Quantity", "Unit", "Particulars"]]; // Headers

    data.forEach((item, index) => {
        wsData.push([index + 1, item.quantity, item.unit, item.particulars]);
    });

    let ws = XLSX.utils.aoa_to_sheet(wsData);
    XLSX.utils.book_append_sheet(wb, ws, "Waste Materials");

    let fileName = "Waste_Material_Report.xlsx";
    XLSX.writeFile(wb, fileName);
}

    // Print Waste Material Function
    $('#printWasteMaterial').on('click', function () {
        let printContent = `
            <html>
                <head>
                    <title>Waste Material Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f4f4f4; }
                    </style>
                </head>
                <body>
                    <h2>Waste Material Report</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Item No.</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Particulars</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        let itemCount = 0;

        $('.waste-material-checkbox:checked').each(function () {
            let row = $(this).closest('tr');
            let remarks = row.find("td:nth-child(11)").text().trim();

            // Ensure only "Processed as Waste Material" items are included
            if (remarks.includes("Processed as Waste Material")) {
                itemCount++;
                printContent += `
                    <tr>
                        <td>${itemCount}</td>
                        <td>${row.find("td:nth-child(4)").text()}</td>
                        <td>${row.find("td:nth-child(5)").text()}</td>
                        <td>${row.find("td:nth-child(8)").text()}</td>
                    </tr>
                `;
            }
        });

        printContent += `
                        </tbody>
                    </table>
                </body>
            </html>
        `;

        let printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
        });

});
</script>

@endsection
