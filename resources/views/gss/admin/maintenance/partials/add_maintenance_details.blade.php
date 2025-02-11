<form id="maintenanceForm"
      action="{{ isset($ledger) ? route('gss.admin.update_maintenance_details', $ledger->maintenance_ledger_id) : route('gss.admin.store_maintenance_details') }}"
            method="POST">
            @csrf
            @if(isset($ledger))
                @method('PUT')
            @endif

            <input type="hidden" name="maintenance_ledger_id" value="{{ $ledger->maintenance_ledger_id ?? '' }}">

                        <!-- Property Number Input -->
                        <div class="mb-3">
                            <label for="property_number" class="form-label"><strong>Property Number</strong></label>
                            <input type="text" class="form-control" id="property_number" name="property_number" value="{{ old('property_number', $ledger->property_number ?? '') }}" required autofocus {{ isset($ledger) ? 'readonly' : '' }}>
                            <small id="property_number_error" class="text-danger d-none">Property Number not found!</small>
                        </div>

                        <!-- Auto-filled fields -->

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="particular" class="form-label"><strong>Particular</strong></label>
                                <input type="text" class="form-control" id="particular" name="particular"  value="{{ old('particular', $ledger->particular_ledger ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label"><strong>Description</strong></label>
                                <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $ledger->description ?? '') }}" readonly>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="end_user" class="form-label"><strong>End User</strong></label>
                                <input type="text" class="form-control" id="end_user" value="{{ old('end_user', $ledger->end_user ?? '') }}" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="division" class="form-label"><strong>Division</strong></label>
                                <input type="text" class="form-control" id="division" value="{{ old('division', $ledger->division ?? '') }}" readonly required>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="serial_no" class="form-label"><strong>Serial No</strong></label>
                                <input type="text" class="form-control" id="serial_no" value="{{ old('serial_no', $ledger->serial_no ?? '') }}" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="model" class="form-label"><strong>Model</strong></label>
                                <input type="text" class="form-control" id="model" value="{{ old('model', $ledger->model ?? '') }}" readonly required>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <label for="date_acquired" class="form-label"><strong>Date Acquired</strong></label>
                                <input type="date" class="form-control" id="date_acquired" value="{{ old('date_acquired', $ledger->date_acquired ?? '') }}" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label"><strong>Amount</strong></label>
                                <input type="number" step="0.01" class="form-control" id="amount" value="{{ old('amount', $ledger->amount ?? '') }}" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="remarks" class="form-label"><strong>Remarks</strong></label>
                                <input type="text" class="form-control" id="remarks" value="{{ old('remarks', $ledger->remarks ?? '') }}" readonly>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="po_number" class="form-label"><strong>PO Number</strong></label>
                                <input type="text" class="form-control" id="po_number" name="po_number" value="{{ old('po_number', $ledger->po_number ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date_delivered" class="form-label"><strong>Date Delivered</strong></label>
                                <input type="date" class="form-control" id="date_delivered" name="date_delivered" value="{{ old('date_delivered', $ledger->date_delivered ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="supplier" class="form-label"><strong>Supplier</strong></label>
                                <input type="text" class="form-control" id="supplier" name="supplier"  value="{{ old('supplier', $ledger->supplier ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label for="quantity" class="form-label"><strong>Quantity</strong></label>
                                <input type="text" class="form-control numeric-input" id="quantity" name="quantity" value="{{ old('quantity', $ledger->quantity ?? '') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="unit" class="form-label"><strong>Unit</strong></label>
                                <input type="text" class="form-control" id="unit" name="unit" value="pcs" value="{{ old('unit', $ledger->unit ?? '') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="unit_cost" class="form-label"><strong>Unit Cost</strong></label>
                                <input type="text" class="form-control numeric-input" id="unit_cost" name="unit_cost" value="{{ old('unit_cost', $ledger->unit_cost ?? '') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="total_amount" class="form-label"><strong>Total Amount</strong></label>
                                <input type="text" step="0.01" class="form-control numeric-input" id="total_amount" name="total_amount" value="{{ old('total_amount', $ledger->total_amount ?? '') }}" readonly required>
                            </div>
                        </div>

                        <div class="mb-3 mt-2">
                            <label for="defects" class="form-label"><strong>Defects</strong></label>
                            <input type="text" class="form-control" id="defects" name="defects" value="{{ old('defects', $ledger->defects ?? '') }}" >
                        </div>

                        <!-- Submit & Close Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2 allow-close">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save"></i> {{ isset($ledger) ? 'Update' : 'Submit' }}
                        </button>
                        </div>
                    </form>
                

<script>
    $(document).ready(function () {

        function formatNumber(value) {
            return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
        }

        function calculateTotal() {
            let quantity = parseFloat($('#quantity').val().replace(/,/g, '')) || 0;
            let unitCost = parseFloat($('#unit_cost').val().replace(/,/g, '')) || 0;
            let totalAmount = quantity * unitCost;

            $('#total_amount').val(formatNumber(totalAmount)); // Format total amount with commas
        }

        // Automatically format number with commas when leaving the field
        $('#unit_cost').on('blur', function () {
            let value = $(this).val().replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                $(this).val(formatNumber(parseFloat(value)));
            }
            calculateTotal();
        });

        $('#quantity').on('input blur keydown', function (event) {
            if (event.type === "keydown" && event.key !== "Enter") return;
            calculateTotal();
        });

        $('#unit_cost').on('input keydown', function (event) {
            if (event.type === "keydown" && event.key !== "Enter") return;
            calculateTotal();
        });

        // Ensure total amount updates when modal opens
        $('#addMaintenanceModal').on('shown.bs.modal', function () {
            calculateTotal(); // Run calculation when the modal is shown
        });

        

        $('#property_number').on('input', function () {
            var propertyNumber = $(this).val().trim();

            // Disable submit button while checking
            $('#submitBtn').prop('disabled', true);
            $('#property_number_error').addClass('d-none');

            if (propertyNumber !== '') {
                $.ajax({
                    url: '{{ route("gss.admin.property_numbers") }}',
                    type: 'GET',
                    data: { property_number: propertyNumber },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#property_number').addClass('loading');
                    },
                    success: function (response) {
                        if (response.exists) {
                            $('#particular').val(response.data.particular);
                            $('#description').val(response.data.description);
                            $('#division').val(response.data.division);
                            $('#end_user').val(response.data.end_user);
                            $('#serial_no').val(response.data.serial_no);
                            
                            // Convert date format to YYYY-MM-DD for date input
                            let dateAcquired = response.data.date_acquired;
                            if (dateAcquired) {
                                dateAcquired = new Date(dateAcquired).toISOString().split('T')[0];
                            }

                            $('#date_acquired').val(dateAcquired);
                            $('#amount').val(response.data.amount);
                            $('#model').val(response.data.model);
                            $('#remarks').val(response.data.remarks);

                            // Hide error message and enable submit button
                            $('#property_number_error').addClass('d-none');
                            $('#submitBtn').prop('disabled', false);
                        } else {
                            // Show error message and clear inputs
                            $('#property_number_error').removeClass('d-none');
                            $('#particular, #division, #end_user, #serial_no, #date_acquired, #amount, #model').val('');
                            $('#submitBtn').prop('disabled', true);
                        }
                    },
                    error: function () {
                        alert('An error occurred while fetching property details.');
                        $('#submitBtn').prop('disabled', true);
                    },
                    complete: function() {
                        $('#property_number').removeClass('loading');
                    }
                });
            } else {
                // If input is empty, reset fields and disable submit button
                $('#property_number_error').addClass('d-none');
                $('#particular, #division, #end_user, #serial_no, #date_acquired, #amount, #model').val('');
                $('#submitBtn').prop('disabled', true);
            }
        });

        // Show success message and close modal after submission
        $('#maintenanceForm').on('submit', function(event) {
            event.preventDefault();

            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    $('#modal-body-content').html('<div class="alert alert-success text-center"><i class="fas fa-check-circle"></i> Maintenance details added successfully!</div>');
                    
                    setTimeout(function() {
                        $('#addMaintenanceModal').modal('hide');
                        location.reload();
                    }, 2000);
                },
                error: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Submit');
                    alert('An error occurred. Please try again.');
                }
            });
        });


        function loadPropertyDetails(propertyNumber) {
        if (!propertyNumber) return;

        $.ajax({
            url: '{{ route("gss.admin.property_numbers") }}',
            type: 'GET',
            data: { property_number: propertyNumber },
            dataType: 'json',
            beforeSend: function () {
                $('#property_number').addClass('loading');
                $('#submitBtn').prop('disabled', true);
                $('#property_number_error').addClass('d-none');
            },
            success: function (response) {
                if (response.exists) {
                    $('#particular').val(response.data.particular);
                    $('#description').val(response.data.description);
                    $('#division').val(response.data.division);
                    $('#end_user').val(response.data.end_user);
                    $('#serial_no').val(response.data.serial_no);

                    let dateAcquired = response.data.date_acquired ? new Date(response.data.date_acquired).toISOString().split('T')[0] : '';
                    $('#date_acquired').val(dateAcquired);
                    
                    $('#amount').val(response.data.amount);
                    $('#model').val(response.data.model);
                    $('#remarks').val(response.data.remarks);

                    $('#property_number_error').addClass('d-none');
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#property_number_error').removeClass('d-none');
                    $('#particular, #description, #division, #end_user, #serial_no, #date_acquired, #amount, #model, #remarks').val('');
                    $('#submitBtn').prop('disabled', true);
                }
            },
            error: function () {
                alert('Error fetching property details.');
                $('#submitBtn').prop('disabled', true);
            },
            complete: function() {
                $('#property_number').removeClass('loading');
            }
        });
    }

    // Load property details when the page is in Update mode
    let existingPropertyNumber = $('#property_number').val();
    if (existingPropertyNumber) {
        loadPropertyDetails(existingPropertyNumber);
    }

    // Load property details when a property number is entered
    $('#property_number').on('input', function () {
        let propertyNumber = $(this).val().trim();
        if (propertyNumber) {
            loadPropertyDetails(propertyNumber);
        }
    });

    // Handle form submission
    $('#maintenanceForm').on('submit', function(event) {
        event.preventDefault();

        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                $('#modal-body-content').html('<div class="alert alert-success text-center"><i class="fas fa-check-circle"></i> Successfully saved!</div>');
                setTimeout(function() {
                    $('#addMaintenanceModal').modal('hide');
                    location.reload();
                }, 1500);
            },
            error: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Submit');
                alert('An error occurred. Please try again.');
            }
        });
    });


    

    });

    

   
</script>
