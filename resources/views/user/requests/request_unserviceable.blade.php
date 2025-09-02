@extends('layouts.user')

@section('title', 'Request Unserviceable')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-danger text-white">Declare as Unserviceable</div>
        <div class="card-body">
            <form action="{{ route('requests.store_unserviceable', $equipment->equipment_id) }}" method="POST">
                @csrf
                <p>Are you sure you want to mark <strong>{{ $equipment->property_number }}</strong> as unserviceable?</p>


                <div class="mb-3">
                    <label for="property_number" class="form-label">Property Number</label>
                    <input type="text" name="property_number" class="form-control" value="{{ $equipment->property_number }}" required>
                </div>
                <div class="mb-3">
                    <label for="particular" class="form-label">Particular</label>
                    <input type="text" name="particular" class="form-control" value="{{ $equipment->particular }}" required>
                </div>
                <div class="form-group">
                            <label for="unserviceable_condition">Condition</label>
                            <select class="form-control" id="unserviceable_condition" name="unserviceable_condition">
                                <option value="" disabled selected hidden>Select Condition</option>
                                <option value="Unserviceable" >Unserviceable</option>
                                <option value="Damage" >Damage</option>
                                <option value="Lost" >Lost</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">Confirm</button>

                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
