@extends('layouts.user')

@section('title', 'Receipt of Returned Unserviceable, Property, Plant, and Equipment (RRUPPE)')
@section('page-title', 'Receipt of Returned Unserviceable, Property, Plant, and Equipment (RRUPPE)')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">RRUPPE</div>
                <div class="card-body">

                @if(session('success'))
                <div class="modal fade" id="successModal_return" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                This is a test modal.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

                @endif

                    <form method="POST" action="{{ route('user.general-services.store_returned_unserviceable_form') }}">
                        @csrf

                        <div class="form-container">
                            <!-- Date -->
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="text" id="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" readonly>
                            </div>

                            <!-- Property Number -->
                            <div class="form-group">
                                <label for="property_number">Property Number:</label>
                                <input type="text" id="property_number" name="property_number" class="form-control">
                            </div>

                            <!-- Property Type -->
                            <div class="form-group">
                                <label for="property_type">Property Type:</label>
                                <input type="text" id="property_type" name="property_type" class="form-control" readonly>
                            </div>

                            <!-- Item Description -->
                            <div class="form-group">
                                <label for="item_description">Item Description:</label>
                                <input type="text" id="item_description" name="item_description" class="form-control" readonly>
                            </div>

                            <!-- Unit Price -->
                            <div class="form-group">
                                <label for="unit_price">Unit Price:</label>
                                <input type="text" id="unit_price" name="unit_price" class="form-control" readonly>
                            </div>

                            <!-- Date Acquired -->
                            <div class="form-group">
                                <label for="date_acquired">Date Acquired:</label>
                                <input type="text" id="date_acquired" name="date_acquired" class="form-control" readonly>
                            </div>

                            <!-- Quantity -->
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" class="form-control">
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks:</label>
                                <input type="text" id="remarks" name="remarks" class="form-control">
                            </div>

                            <!-- Returned By -->
                            <div class="form-group">
                                <label for="returned_by">Returned By:</label>
                                <input type="text" id="returned_by" name="returned_by" class="form-control">
                            </div>

                            <div class="button-container">
                                <button type="submit" class="btn btn-primary custom-button">
                                    {{ __('Submit') }}
                                </button>
                                <button type="button" class="btn btn-secondary edit-button">
                                    {{ __('Edit') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    @if(session('success'))
        $('#successModal_return').modal('show');
    @endif

    function fetchEquipmentDetails(propertyNumber) {
        if (propertyNumber.length > 0) {
            $.ajax({
                url: "{{ route('user.general-services.returned_unserviceable_form') }}",
                method: 'GET',
                data: { property_number: propertyNumber },
                success: function(response) {
                    if(response) {
                        $('#property_type').val(response.property_type);
                        $('#item_description').val(response.particular);
                        
                        // Format amount with commas
                        var formattedAmount = new Intl.NumberFormat().format(response.amount);
                        $('#unit_price').val(formattedAmount);
                        
                        $('#date_acquired').val(response.date_acquired);
                    } else {
                        // Clear the fields if the property number is not found
                        $('#property_type').val('');
                        $('#item_description').val('');
                        $('#unit_price').val('');
                        $('#date_acquired').val('');
                    }
                }
            });
        }
    }

    // Trigger fetch on 'Enter' key press or when input field loses focus
    $('#property_number').on('blur keyup', function(e) {
        if (e.type === 'blur' || (e.type === 'keyup' && e.key === 'Enter')) {
            var propertyNumber = $(this).val();
            fetchEquipmentDetails(propertyNumber);
        }
    });
});
</script>
@endsection
