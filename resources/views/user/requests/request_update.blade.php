@extends('layouts.user')

@section('title', 'Request Update')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">Update Equipment</div>
        <div class="card-body">
            <form action="{{ route('requests.store_update', $equipment->equipment_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="property_number" class="form-label">Property Number</label>
                    <input type="text" name="property_number" class="form-control" value="{{ $equipment->property_number }}" required>
                </div>
                <div class="mb-3">
                    <label for="particular" class="form-label">Particular</label>
                    <input type="text" name="particular" class="form-control" value="{{ $equipment->particular }}" required>
                </div>
                <div class="mb-3">
                    <label for="division" class="form-label">Division</label>
                    <input type="text" name="division" class="form-control" value="{{ $equipment->division }}" required>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" name="section" class="form-control" value="{{ $equipment->section }}" >
                </div>
                <div class="mb-3">
                    <label for="reasons" class="form-label">Reasons for Update</label>
                    <input type="text" name="reasons" class="form-control" value="{{ $equipment->reasons }}" required>
                </div>
                <button type="submit" class="btn btn-success">Submit</button>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
