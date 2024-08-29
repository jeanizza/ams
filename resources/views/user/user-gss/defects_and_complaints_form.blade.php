@extends('layouts.user')

@section('title', 'Defects and Complaints')
@section('page-title', 'Defects and Complaints')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Defects and Complaints</div>
                <div class="card-body">

                @if(session('success'))
                <div class="modal" id="successModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Success</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>{{ session('success') }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                    <form method="POST" action="{{ route('user.general-services.store_defects_and_complaints_form') }}">
                        @csrf

                        <div class="form-container">
                            <!-- Property Number -->
                            <div class="form-group">
                                <label for="PropertyNumber">Property Number</label>
                                <input id="PropertyNumber" class="form-control custom-input" type="text" name="PropertyNumber" value="{{ old('PropertyNumber') }}" required autofocus autocomplete="PropertyNumber" />
                                @error('PropertyNumber')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Type of Equipment -->
                            <div class="form-group">
                                <label for="TypeOfEquipment">Type of Equipment</label>
                                <input id="TypeOfEquipment" class="form-control custom-input" type="text" name="TypeOfEquipment" value="{{ old('TypeOfEquipment') }}" readonly />
                                @error('TypeOfEquipment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Serial No. -->
                            <div class="form-group">
                                <label for="SerialNo">Serial No.</label>
                                <input id="SerialNo" class="form-control custom-input" type="text" name="SerialNo" value="{{ old('SerialNo') }}" readonly />
                                @error('SerialNo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Division -->
                            <div class="form-group">
                                <label for="Division">Division</label>
                                <input id="Division" class="form-control custom-input" type="text" name="Division" value="{{ Auth::user()->div_name }}" readonly />
                                @error('Division')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Complaints -->
                            <div class="form-group">
                                <label for="Complaints">Complaints</label>
                                <textarea id="Complaints" class="form-control custom-input" name="Complaints" rows="3" required>{{ old('Complaints') }}</textarea>
                                @error('Complaints')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Defects -->
                            <div class="form-group">
                                <label for="Defects">Defects</label>
                                <textarea id="Defects" class="form-control custom-input" name="Defects" rows="3" required>{{ old('Defects') }}</textarea>
                                @error('Defects')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Parts to be Repaired/Replaced -->
                            <div class="form-group">
                                <label for="PartsToBeRepaired">Parts to be Repaired/Replaced</label>
                                <textarea id="PartsToBeRepaired" class="form-control custom-input" name="PartsToBeRepaired" rows="3" required>{{ old('PartsToBeRepaired') }}</textarea>
                                @error('PartsToBeRepaired')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remarks/Recommendations -->
                            <div class="form-group">
                                <label for="Remarks">Remarks/Recommendations</label>
                                <textarea id="Remarks" class="form-control custom-input" name="Remarks" rows="3" required>{{ old('Remarks') }}</textarea>
                                @error('Remarks')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Check for session success and show the modal if it's set
    @if(session('success'))
        $('#successModal').modal('show');
    @endif

    // AJAX call to fetch equipment details based on Property Number
    $('#PropertyNumber').on('blur', function() {
        var propertyNumber = $(this).val();
        if (propertyNumber) {
            $.ajax({
                url: '{{ route('user.getEquipmentDetails') }}',
                type: 'GET',
                data: { property_number: propertyNumber },
                success: function(response) {
                    if(response) {
                        $('#TypeOfEquipment').val(response.particular);
                        $('#SerialNo').val(response.serial_no);
                    } else {
                        alert('No data found for this Property Number.');
                    }
                },
                error: function() {
                    alert('An error occurred while fetching the equipment details.');
                }
            });
        }
    });
});
</script>
@endsection