@extends('layouts.admin')

@section('title', 'List of Serviceable')
@section('page-title', 'List of Serviceable')

@section('content')
<div class="container-fluid list-serviceable">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">List of Serviceable</div>
                <div class="card-body"> 

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('gss.admin.list_serviceable') }}" id="search-form">
                        <div class="input-group mb-3">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ request()->get('search') }}">
                        </div>
                    </form>
                    <table class="table table-bordered" id="serviceable-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Property Number</th>
                            <th>Particular</th>
                            <th>Description</th>
                            <th>Office</th>
                            <th>End User</th>
                            <th>Division</th>
                            <th>Section</th>
                            <th>Amount</th>
                            <th>Upload Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <!-- <div id="table-container"> -->
                        <tbody>
                        @include('gss.admin.serviceable.table', ['serviceables' => $serviceables])
                        </tbody>
                    <!-- </div> -->
                    </table>
                    
<div class="Page navigation">
    <!-- Pagination Links -->
    {{ $serviceables->appends(request()->only('search'))->links('pagination::bootstrap-4') }}
    <!-- Pagination Info and Links -->
    <div class="d-flex justify-content-between">
                        <div>
                            <!-- Display the current page and the total number of pages -->
                            <p>Showing page {{ $serviceables->currentPage() }} of {{ $serviceables->lastPage() }}</p>
                        </div>
                    </div>
</div>

                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetch_data(page, query, sort_by, sort_direction) {
        $.ajax({
            url: "{{ route('gss.admin.list_serviceable') }}",
            method: "GET",
            data: {
                page: page,
                search: query,
                sort_by: sort_by,
                sort_direction: sort_direction
            },
            success: function(data) {
                $('#table_data').html(data.table_data);
                $('#pagination_links').html(data.pagination);
            }
        });
    }

    $(document).on('keyup', '#search', function() {
        var query = $('#search').val();
        var sort_by = $('#sort_by').val();
        var sort_direction = $('#sort_direction').val();
        fetch_data(1, query, sort_by, sort_direction);
    });

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('#search').val();
        var sort_by = $('#sort_by').val();
        var sort_direction = $('#sort_direction').val();
        fetch_data(page, query, sort_by, sort_direction);
    });

    $(document).on('change', '#sort_by, #sort_direction', function() {
        var query = $('#search').val();
        var sort_by = $('#sort_by').val();
        var sort_direction = $('#sort_direction').val();
        fetch_data(1, query, sort_by, sort_direction);
    });
});
</script>
@endsection
@endsection
