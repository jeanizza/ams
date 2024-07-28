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
                            <th>Upload Image</th>
                            <!-- Add other columns as necessary -->
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
    var fetchUrl = @json(route('gss.admin.list_serviceable'));

    $('#search').on('keyup', function() {
        var query = $(this).val();
        fetchServiceables(query);
    });

    function fetchServiceables(query = '') {
        $.ajax({
            url: fetchUrl,
            method: 'GET',
            data: {search: query},
            dataType: 'json',
            success: function(data) {
                $('#serviceable-table tbody').html(data.table_data);
                $('#pagination-links').html(data.pagination);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }
});
</script>
@endsection
@endsection
