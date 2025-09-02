@extends('layouts.admin')

@section('title', 'Transfer Serviceable')
@section('page-title', 'Transfer Serviceable')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Transfer Serviceable</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('serviceables.transfer', $serviceable->equipment_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <input type="text" class="form-control" id="property_type" name="property_type" value="{{ $serviceable->property_type }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="property_number">Property Number</label>
                            <input type="text" class="form-control" id="property_number" name="property_number" value="{{ $serviceable->property_number }}" readonly>
                        </div>
                    
                        <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control" id="category" name="category" value="{{ $serviceable->category }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="particular">Particular</label>
                            <input type="text" class="form-control" id="particular" name="particular" value="{{ $serviceable->particular }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $serviceable->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="{{ $serviceable->brand }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="{{ $serviceable->model }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="serial_no">Serial No.</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" value="{{ $serviceable->serial_no }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" 
                                value="{{ isset($serviceable->amount) ? number_format($serviceable->amount, 2) : '' }}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="po_number">Purchase Order No.</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" value="{{ $serviceable->po_number }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="date" class="form-control" id="date_acquired" name="date_acquired" value="{{ $serviceable->date_acquired }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" name="end_user" value="{{ $serviceable->end_user }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" value="{{ $serviceable->position }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="office">Office</label>
                            <input type="text" class="form-control" id="office" name="office" value="{{ $serviceable->office }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="division">Division</label>
                            <input type="text" class="form-control" id="division" name="division" value="{{ $serviceable->division }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="section">Section</label>
                            <input type="text" class="form-control" id="section" name="section" value="{{ $serviceable->section }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $serviceable->remarks }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="fund">Fund Source</label>
                            <input type="text" class="form-control" id="fund" name="fund" value="{{ $serviceable->fund }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="lifespan">Estimated Useful Life</label>
                            <input type="text" class="form-control" id="lifespan" name="lifespan" value="{{ $serviceable->lifespan }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="date_end">Date Terminous</label>
                            <input type="text" class="form-control" id="date_end" name="date_end" value="{{ $serviceable->date_end }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="uploaded_by">Uploaded By</label>
                            <input type="text" class="form-control" id="uploaded_by" name="uploaded_by" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <!-- Upload Image -->
                        <div class="form-group">
                            <label for="upload_image">Upload Image</label>
                            @if($serviceable->upload_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $serviceable->upload_image) }}" alt="Uploaded Image" class="img-fluid img-thumbnail" style="max-width: 100%; max-height: 300px;">
                                </div>
                            @endif
                        </div>

                        <!-- Transfer Details -->
                        <div class="form-group">
                        <label for="transfer_office">Transfer to Office</label>
                        <select class="form-control" id="transfer_office" name="transfer_office" required>
                            <option value="" disabled selected hidden>Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office }}" 
                                        {{ isset($transfer->transfer_office) && $transfer->transfer_office == $office ? 'selected' : '' }}>
                                    {{ $office }}
                                </option>
                            @endforeach
                        </select>
                    </div>
 
                        <div class="form-group">
                            <label for="transfer_enduser">Transfer To</label>
                            <input type="text" class="form-control" id="transfer_enduser" name="transfer_enduser" 
                                value="{{ isset($transfer->transfer_enduser) ? $transfer->transfer_enduser : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="transfer_position">Position</label>
                            <input type="text" class="form-control" id="transfer_position" name="transfer_position" 
                                value="{{ isset($transfer->transfer_position) ? $transfer->transfer_position : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="transfer_condition">Condition</label>
                            <select class="form-control" id="transfer_condition" name="transfer_condition">
                                <option value="" disabled selected hidden>Select Condition</option>
                                <option value="Serviceable" {{ isset($transfer->transfer_condition) && $transfer->transfer_condition == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                                <option value="Brand New" {{ isset($transfer->transfer_condition) && $transfer->transfer_condition == 'Brand New' ? 'selected' : '' }}>Brand New</option>
                                <option value="Slightly Used" {{ isset($transfer->transfer_condition) && $transfer->transfer_condition == 'Slightly Used' ? 'selected' : '' }}>Slightly Used</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reason_transfer">Reason</label>
                            <textarea class="form-control" id="reason_transfer" name="reason_transfer">{{ isset($transfer->reason_transfer) ? $transfer->reason_transfer : '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="date_transfer">Date Transfer</label>
                            <input type="date" class="form-control" id="date_transfer" name="date_transfer" 
                                value="{{ isset($transfer->date_transfer) ? $transfer->date_transfer : '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ isset($transfer) ? 'Update' : 'Transfer' }}</button>
                        <a href="{{ route('gss.admin.transferred_items') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Handle division change event to fetch sections
    $('#division').change(function() {
        var div_name = $(this).val();
        if (div_name) {
            $.ajax({
                url: '{{ url("get-sections") }}/' + div_name,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#section').empty();
                    $.each(data, function(key, value) {
                        $('#section').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        } else {
            $('#section').empty();
        }
    });


    // Show success modal and handle PDF download
    @if(session('success'))
        $('#successModal').modal('show');
        $('#download-pdf').click(function() {
            let propertyNumber = '{{ session("propertyNumber") }}';
            window.location.href = '{{ url("/generate-pdf") }}/' + propertyNumber;
        });
        $('#ok-button').click(function() {
            window.location.href = '{{ route("gss.admin.list_serviceable") }}';
        });
    @endif
});

$(document).ready(function() {
        // Get the current office value
        const currentOffice = '{{ $serviceable->office }}';

        // Remove the current office from the transfer_office dropdown
        $('#transfer_office option').each(function() {
            if ($(this).val() === currentOffice) {
                $(this).remove();
            }
        });
    });
</script>
@endsection
