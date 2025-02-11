@extends('layouts.user')

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
                    <form method="GET" action="{{ route('user.general-services.inventory') }}" id="search-form">
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
                        <tbody>
                            @foreach($serviceables as $item)
                                <tr>
                                    <td>{{ $item->equipment_id }}</td>
                                    <td>{{ $item->property_number }}</td>
                                    <td>{{ $item->particular }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->office }}</td>
                                    <td>{{ $item->end_user }}</td>
                                    <td>{{ $item->division }}</td>
                                    <td>{{ $item->section }}</td>
                                    <td>{{ number_format((float)$item->amount, 2, '.',',') }}</td>
                                    <td style="width: 100px; height: 100px;">
                                        @if($item->upload_image)
                                            <!-- Use the correct storage URL for public access -->
                                            <img src="{{ asset('storage/' . $item->upload_image) }}" alt="Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset('storage/' . $item->upload_image) }}">
                                            @else
                                            No image
                                        @endif
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        <button type="submit" class="btn btn-danger btn-sm">Unserviceable</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        {{ $serviceables->appends(request()->only('search'))->links('pagination::bootstrap-4') }}
                    </div>
                    <!-- Pagination Info -->
                    <div class="d-flex justify-content-between">
                        <p>Showing page {{ $serviceables->currentPage() }} of {{ $serviceables->lastPage() }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" alt="Preview Image" style="max-height: 500px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Handle the click event to show the image in the modal
    // Ensure modal image loads correctly
    $('img[data-bs-toggle="modal"]').on('click', function() {
        var imageSrc = $(this).data('image');
        console.log(imageSrc); // Debugging line to check if the imageSrc is correct
        $('#modalImage').attr('src', imageSrc);
    });

    // Fix for column size
    $('img').css({
        'width': '100px',
        'height': '100px',
        'object-fit': 'cover'
    });
});

// Search and pagination handling
    function fetch_data(page, query) {
        $.ajax({
            url: "{{ route('user.general-services.inventory') }}",
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

@endsection
