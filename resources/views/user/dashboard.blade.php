@extends('layouts.user')

@section('title', 'User Dashboard')
@section('page-title', 'User Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">User Dashboard</div>
                <div class="card-body">
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the User dashboard.</p>

                    <h5>Equipment Near Date End</h5>
                    @if($equipmentItems->isEmpty())
                        <p>No equipment items are due within the next 15 days.</p>
                    @else
                        <p>Found {{ $equipmentItems->total() }} items:</p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Office</th>
                                    <th>Division</th>
                                    <th>Date End</th>
                                    <th>Date Acquired</th>
                                    <th>Remarks</th>
                                    <th>Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipmentItems as $item)
                                    <tr>
                                        <td>{{ $item->equipment_id }}</td>
                                        <td>{{ $item->property_number }}</td>
                                        <td>{{ $item->office }}</td>
                                        <td>{{ $item->division }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date_end)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date_acquired)->format('Y-m-d') }}</td>
                                        <td class="text-center"><span style="background-color: yellow; padding: 2px 4px;">For Update</span></td>
                                        <td class="text-center">
                                                <button type="submit" class="btn btn-warning">Unserviceable</button>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination links -->
                        <div class="d-flex justify-content-between">
                            {{ $equipmentItems->links('pagination::bootstrap-4') }}
                        </div>
                        <p>Showing {{ $equipmentItems->firstItem() }} to {{ $equipmentItems->lastItem() }} of {{ $equipmentItems->total() }} results</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
