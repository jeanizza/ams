@extends('layouts.admin')

@section('title', 'Add Record')
@section('page-title', 'Add Record')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add Record</div>
                <div class="card-body">
                    <form action="{{ route('gss.admin.store_add_record') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Property Type -->
                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <select class="form-control" id="property_type" name="property_type" required>
                                <option value="PAR">Property Acknowledgment Receipt</option>
                                <option value="ICS">Inventory Custodian Slip</option>
                            </select>
                        </div>
                        
                        <!-- Property Number -->
                        <div class="form-group">
                            <label for="property_number">Property Number</label>
                            <input type="text" class="form-control" id="property_number" name="property_number" required>
                        </div>
                        
                        <!-- Category -->
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="05-Agricultural And Forestry Equipment">05-Agricultural And Forestry Equipment</option>
                                <option value="05-Buildings">05-Buildings</option>
                                <option value="05-Communication Equipment">05-Communication Equipment</option>
                                <option value="05-Construction Equipment">05-Construction Equipment</option>
                                <option value="06-Furniture And Fixtures">06-Furniture And Fixtures</option>
                                <option value="05-Information And Communication Technology Equipment">05-Information And Communication Technology Equipment</option>
                                <option value="05-Marine And Fishery Equipment">05-Marine And Fishery Equipment</option>
                                <option value="05-Office Equipment">05-Office Equipment</option>
                                <option value="05-Other Land Improvements">05-Other Land Improvements</option>
                                <option value="05-Sports Equipment">05-Sports Equipment</option>
                                <option value="05-Technical And Scientific Equipment">05-Technical And Scientific Equipment</option>
                                <option value="06-Transportation Equipment">06-Transportation Equipment</option>
                                <option value="05-Watercrafts Equipment">05-Watercrafts Equipment</option>
                            </select>
                        </div>

                        <!-- particular -->
                        <div class="form-group">
                            <label for="particular">Particular</label>
                            <input type="text" class="form-control" id="particular" name="particular" required>
                        </div>

                          <!-- description -->
                          <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>

                        <!-- brand -->
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" required>
                        </div>

                        <!-- model -->
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>

                        <!-- serial_no -->
                        <div class="form-group">
                            <label for="serial_no">Serial No.</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" required>
                        </div>

                        <!-- amount -->
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" required>
                        </div>

                        <!-- po_number -->
                        <div class="form-group">
                            <label for="po_number">Purchase Order No.</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" required>
                        </div>

                        <!-- date_acquired -->
                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="text" class="form-control" id="date_acquired" name="date_acquired" required>
                        </div>

                        <!-- end_user -->
                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" name="end_user" required>
                        </div>

                        <!-- position -->
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        
                        <!-- Division -->
                        <div class="form-group">
                            <label for="division">Division</label>
                            <select class="form-control" id="division" name="division" required>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->div_id }}">{{ $division->div_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Section -->
                        <div class="form-group">
                            <label for="section">Section</label>
                            <select class="form-control" id="section" name="section" required>
                                <!-- Sections will be dynamically loaded based on the selected division -->
                            </select>
                        </div>

                        <!-- actual_user -->
                        <div class="form-group">
                            <label for="position">Actual User</label>
                            <input type="text" class="form-control" id="actual_user" name="actual_user" required>
                        </div>

                        <!-- position_actual_user -->
                        <div class="form-group">
                            <label for="position_actual_user">Actual User Position</label>
                            <input type="text" class="form-control" id="position_actual_user" name="position_actual_user" required>
                        </div>

                        <!-- remarks  -->
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <select class="form-control" id="remarks" name="remarks" required>
                                <option value="purchased">Purchased</option>
                                <option value="central office fund">Central Office Fund</option>
                                <option value="field office fund">Field Office Fund</option>
                            </select>
                        </div>

                        <!-- fund source  -->
                        <div class="form-group">
                            <label for="fund">Fund Source</label>
                            <select class="form-control" id="fund" name="fund" required>
                                <option value="101">101</option>
                                <option value="102">102</option>
                                <option value="158">158</option>
                                <option value="401">401</option>
                                <option value="trust fund">Trust Fund</option>
                                <option value="not applicable">Not Applicable</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lifespan">Lifespan</label>
                            <select class="form-control" id="lifespan" name="lifespan" required>
                                <option value="1">1</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="7">7</option>
                            </select>
                        </div>

                        <!-- Upload Image -->
                        <div class="form-group">
                            <label for="upload_image">Upload Image</label>
                            <input type="file" class="form-control-file" id="upload_image" name="upload_image" accept="image/png, image/jpeg" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#division').change(function() {
        var div_id = $(this).val();
        if (div_id) {
            $.ajax({
                url: '{{ url("sections") }}/' + div_id,
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
});
</script>
@endsection
