@extends('layouts.user')

@section('title', 'Gate Pass')
@section('page-title', 'Gate Pass')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Gate Pass</div>
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
                                        <p>{{ session('message') }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="ok-button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.general-services.store_gate_pass_form') }}">
                        @csrf

                        <div class="form-container">
                            <!-- Gate Pass No. -->
                            <div class="form-group">
                                <label for="GatePassNo">Gate Pass No.</label>
                                <input id="GatePassNo" class="form-control custom-input" type="text" name="GatePassNo" value="{{ old('GatePassNo') }}" required autofocus autocomplete="GatePassNo" />
                                @error('GatePassNo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Memorandum To -->
                            <div class="form-group">
                                <label for="MemorandumTo">Memorandum To</label>
                                <input id="MemorandumTo" class="form-control custom-input" type="text" name="MemorandumTo" value="The Security Guards - DENR 10, Cagayan de Oro City" readonly />
                                @error('MemorandumTo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Other Fields -->
                            <!-- Add other fields like Date, Bearer Name, Borrowed Items, etc., following the same structure. -->

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
        $('#successModal').modal('show');
    @endif
});
</script>
@endsection
