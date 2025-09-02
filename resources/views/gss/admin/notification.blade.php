@extends('layouts.admin')

@section('title', 'Request Notification')
@section('page-title', 'Request Notification')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Request Notification</div>
                <div class="card-body">
                    
                <form method="GET" action="{{ route('gss.admin.notification') }}">
                    <div class="row mb-3">
                        <!-- Search Bar -->
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search property number..." value="{{ request('search') }}">
                        </div>

                        <!-- Division Dropdown -->
                        <div class="col-md-3">
                            <select name="division" class="form-control">
                                <option value="">All Divisions</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division }}" {{ request('division') == $division ? 'selected' : '' }}>
                                        {{ $division }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-md-2">
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                    <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width: 5%;">No.</th>
                            <th style="width: 10%;">Property Number</th>
                            <th style="width: 12%;">Particular</th>
                            <th style="width: 18%;">Description</th>
                            <th style="width: 10%;">Amount</th>
                            <th style="width: 10%;">Division</th>
                            <th style="width: 10%;">Reasons</th>
                            <th style="width: 10%;">Source Table</th>
                            <th class="text-center" style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $index => $item)
                        <tr style="height: 100px; vertical-align: middle;">
                            <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->property_number }}</td>
                            <td>{{ $item->particular }}</td>
                            <td class="text-truncate" style="max-height: 120px; white-space: normal; word-wrap: break-word; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical;" title="{{ $item->description }}">
                                {{ $item->description }}
                            </td>
                            <td>{{ $item->amount }}</td>
                            <td>{{ $item->division }}</td>
                            <td>{{ $item->reason }}</td>
                            <td>{{ $item->source_table }}</td> 
                            <td>
                                <a href="{{ route('serviceables.update_serviceable', ['id' => $item->equipment_id]) }}" class="btn btn-success btn-sm">Update</a>
                                <a href="{{ route('serviceables.transfer_serviceable', $item->equipment_id) }}" class="btn btn-warning btn-sm" style="background-color: #ffcc00; border-color: #ffcc00;">Transfer</a>
                                <a href="{{ route('serviceables.unserviceable_form', $item->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection