@extends('layouts.user')

@section('title', 'List of Requests')
@section('page-title', 'List of Requests')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">View Requests</div>
                <div class="card-body">

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('user.general-services.view_request') }}" id="search-form" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ request()->get('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    @if($requests->isEmpty())
                        <p>No pending requests found.</p>
                    @else
                        <table class="table table-bordered" id="serviceable-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Property Number</th>
                                    <th>Description</th>
                                    <th>Equipment Description</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $requests->firstItem() + $loop->index }}</td>
                                        <td>{{ $request->property_number }}</td>
                                        <td>{{ $request->description }}</td>
                                        <td>{{ $request->equipment_description ?? 'N/A' }}</td>
                                        <td>{{ $request->status ?? 'Pending' }}</td>
                                        <td>
                                            @switch($request->source)
                                                @case('Request for Transfer')
                                                    Request for Transfer
                                                    @break
                                                @case('Request for Update')
                                                    Request for Update
                                                    @break
                                                @case('Request for Return')
                                                    Request for Return
                                                    @break
                                                @default
                                                    {{ ucfirst($request->source) }}
                                            @endswitch
                                        </td>
                                        <td>
                                            @if(in_array($request->source, ['Request for Update', 'Request for Transfer', 'Request for Return']))
                                                <a href="{{ route('requests.request_update', $request->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
                                                <a href="{{ route('requests.request_transfer', $request->equipment_id) }}" class="btn btn-warning btn-sm">Transfer</a>
                                                <a href="{{ route('requests.request_unserviceable', $request->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between">
                            {{ $requests->appends(request()->only('search'))->links('pagination::bootstrap-4') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetch_data(page, query) {
        $.ajax({
            url: "{{ route('user.general-services.view_request') }}",
            method: "GET",
            data: {
                page: page,
                search: query
            },
            success: function(data) {
                $('#serviceable-table tbody').html(data.table_data);
                $('.pagination').html(data.pagination);
            }
        });
    }

    $(document).on('keyup', '#search', function() {
        var query = $('#search').val();
        fetch_data(1, query);
    });

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('#search').val();
        fetch_data(page, query);
    });
});
</script>
@endsection