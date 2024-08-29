@extends('layouts.admin')

@section('title', 'Add Maintenance Ledger')
@section('page-title', 'Add Maintenance Ledger')

@section('content')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add Maintenance Ledger</div>
                <div class="card-body">
                    <h2>Add Maintenance Details</h2>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('gss.admin.store_maintenance_details') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="property_number">Property Number</label>
                            <select class="form-control" id="property_number" name="property_number" required>
                                <option value="">Select a property number</option>
                                @foreach($propertyNumbers as $property)
                                    <option value="{{ $property->property_number }}"
                                            data-particular="{{ $property->particular }}"
                                            data-division="{{ $property->division }}"
                                            data-end_user="{{ $property->end_user }}"
                                            data-serial_no="{{ $property->serial_no }}"
                                            data-date_acquired="{{ $property->date_acquired }}"
                                            data-amount="{{ $property->amount }}"
                                            data-model="{{ $property->model }}">
                                        {{ $property->property_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_created">Date Created</label>
                            <input type="date" class="form-control" id="date_created" name="date_created" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                        <div class="form-group">
                            <label for="particular">Particular</label>
                            <input type="text" class="form-control" id="particular" name="particular" required>
                        </div>
                        <div class="form-group">
                            <label for="defects">Defects</label>
                            <textarea class="form-control" id="defects" name="defects"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="po_number">PO Number</label>
                            <input type="text" class="form-control" id="po_number" name="po_number">
                        </div>
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <input type="text" class="form-control" id="supplier" name="supplier">
                        </div>
                        <div class="form-group">
                            <label for="unit_cost">Unit Cost</label>
                            <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" required>
                        </div>
                        <div class="form-group">
                            <label for="total_amount">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="division">Division</label>
                            <input type="text" class="form-control" id="division" readonly>
                        </div>
                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" readonly>
                        </div>
                        <div class="form-group">
                            <label for="serial_no">Serial Number</label>
                            <input type="text" class="form-control" id="serial_no" readonly>
                        </div>
                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="date" class="form-control" id="date_acquired" readonly>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" readonly>
                        </div>
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#property_number').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            $('#particular').val(selectedOption.data('particular'));
            $('#division').val(selectedOption.data('division'));
            $('#end_user').val(selectedOption.data('end_user'));
            $('#serial_no').val(selectedOption.data('serial_no'));
            $('#date_acquired').val(selectedOption.data('date_acquired'));
            $('#amount').val(selectedOption.data('amount'));
            $('#model').val(selectedOption.data('model'));
        });
    });
</script>


<!-- Bootstrap 4 -->
<script src="http://ams.local/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="http://ams.local/js/adminlte.min.js"></script>
@endsection
