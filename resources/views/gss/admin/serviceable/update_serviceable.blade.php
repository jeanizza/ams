@extends('layouts.admin')

@section('title', 'Update Serviceable')
@section('page-title', 'Update Serviceable')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Update Serviceable</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('serviceables.update', $serviceable->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="property_number">Property Number</label>
                            <input type="text" name="property_number" class="form-control" value="{{ $serviceable->property_number }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" name="description" class="form-control" value="{{ $serviceable->description }}" required>
                        </div>

                        <div class="form-group">
                            <label for="office">Office</label>
                            <input type="text" name="office" class="form-control" value="{{ $serviceable->office }}" required>
                        </div>

                        <!-- Add other fields as necessary -->

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('gss.admin.list_serviceable') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
