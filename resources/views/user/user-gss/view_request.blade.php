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

                    <!-- Display the requests -->
                    @if($requests->isEmpty())
                        <p>No pending requests found.</p>
                    @else
                        <table class="table table-bordered" id="serviceable-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->property_number }}</td>
                                        <td>{{ $request->description }}</td>
                                        <td>{{ $request->status ?? 'Pending' }}</td>
                                        <td>{{ ucfirst($request->source) }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            @if($request->source == 'complaints_defects')
                                                <a href="{{ route('user.general-services.defects_and_complaints_form', ['id' => $request->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @elseif($request->source == 'job_requests')
                                                <a href="{{ route('user.general-services.job_request_form', ['id' => $request->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @elseif($request->source == 'unserviceable')
                                                <a href="{{ route('user.general-services.returned_unserviceable_form', ['id' => $request->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination Links -->
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
