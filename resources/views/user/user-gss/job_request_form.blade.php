@extends('layouts.user')

@section('title', 'Job Request Form')
@section('page-title', 'Job Request Form')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Job Request</div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="modal fade show" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true" style="display: block;">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ session('message') }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="ok-button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.general-services.store_job_request_form') }}">
                        @csrf

                        <div class="form-container">
                            <!-- Type (Simple or Complex) -->
                            <div class="form-group">
                                <label>Type:</label>
                                <div class="checkbox-row">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="Simple" name="Type[]" value="Simple" class="custom-checkbox">
                                        <label for="Simple">{{ __('Simple') }}</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="Complex" name="Type[]" value="Complex" class="custom-checkbox">
                                        <label for="Complex">{{ __('Complex') }}</label>
                                    </div>
                                </div>
                                @error('Type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Date and Time Requested -->
                            <div class="form-group">
                                <label for="date_time">Date and Time Requested:</label>
                                <input type="text" id="date_time" class="form-control" value="{{ now()->setTimezone('Asia/Manila')->format('Y-m-d h:i:s A') }}" readonly>
                            </div>

                            <!-- Name -->
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <!-- Office / Division -->
                            <div class="form-group">
                                <label for="division">Office / Division:</label>
                                <input type="text" id="division" name="division" class="form-control" value="{{ Auth::user()->div_name }}" readonly>
                            </div>

                            <!-- Type of Request -->
                            <div class="form-group">
                                <label for="TypeOfRequest">Type of Request:</label>
                                <div class="checkbox-row">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="Repair" name="TypeOfRequest[]" value="Repair" class="custom-checkbox">
                                        <label for="Repair">{{ __('Repair') }}</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="Maintenance" name="TypeOfRequest[]" value="Maintenance" class="custom-checkbox">
                                        <label for="Maintenance">{{ __('Maintenance') }}</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="Others" name="TypeOfRequest[]" value="Others" class="custom-checkbox">
                                        <label for="Others">{{ __('Others') }}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Specify">Specify (If Others):</label>
                                    <input id="Specify" class="form-control" type="text" name="Specify" value="{{ old('Specify') }}" autocomplete="Specify" disabled>
                                </div>
                                @error('TypeOfRequest')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Job Description -->
                            <div class="form-group">
                                <label for="job_description">Job Description:</label>
                                <textarea id="job_description" name="job_description" class="form-control" rows="5"></textarea>
                            </div>

                            <div class="button-container">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                                <button type="button" class="btn btn-secondary">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const othersCheckbox = document.getElementById('Others');
    const specifyInput = document.getElementById('Specify');

    // Enable/Disable Specify input based on Others checkbox state
    othersCheckbox.addEventListener('change', function() {
        if (this.checked) {
            specifyInput.disabled = false;
        } else {
            specifyInput.disabled = true;
            specifyInput.value = ''; // Clear the input when disabled
        }
    });

    // Show the success modal if the session has success
    if ("{{ session('success') }}") {
        $('#successModal').modal('show');
    }
});
</script>

<!-- Include Bootstrap and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
