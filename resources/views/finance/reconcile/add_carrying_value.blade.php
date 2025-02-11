@extends('layouts.finance')

@section('title', 'Add Carrying Value')
@section('page-title', 'Add Carrying Value')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add Carrying Value</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Carrying Value Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Property Number</th>
                                <th>Description</th>
                                <th>Unit Price</th>
                                <th>Status</th>
                                <th>Date Acquired</th>
                                <th>Carrying Value</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($unserviceableRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->property_number }}</td>
                                <td>{{ $record->item_description }}</td>
                                <td>{{ number_format($record->unit_price, 2) }}</td>
                                <td>{{ $record->status }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->date_acquired)->format('Y-m-d') }}</td>
                                <td>
                                    <form action="{{ route('finance.store_carrying_value') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="property_number" value="{{ $record->property_number }}">
                                        <input type="hidden" name="unserviceable_id" value="{{ $record->id }}">
                                        <input type="text" name="carrying_value" class="form-control" placeholder="Enter Carrying Value" required>
                                </td>
                                <td>
                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        {{ $unserviceableRecords->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
