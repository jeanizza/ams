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

                    <!-- ✅ Live Search & Auto-Filtering Form -->
                    <form method="GET" action="{{ route('user.general-services.inventory') }}" id="filterForm">
                        <div class="d-flex flex-wrap mb-3">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search..." style="width: 20%;" 
                                value="{{ request('search') }}" id="searchInput">

                            <input type="date" name="date_from" class="form-control me-2" style="width: 15%;" 
                                value="{{ request('date_from') }}" id="dateFrom">

                            <input type="date" name="date_to" class="form-control me-2" style="width: 15%;" 
                                value="{{ request('date_to') }}" id="dateTo">

                            <select name="ppe_category" class="form-control me-2" style="width: 15%;" id="ppeCategory">
                                <option value="">All Categories</option>
                                <option value="ppe" {{ request('ppe_category') == 'ppe' ? 'selected' : '' }}>PPE (≥50,000)</option>
                                <option value="semi_expendables" {{ request('ppe_category') == 'semi_expendables' ? 'selected' : '' }}>Semi-Expendables (<50,000)</option>
                            </select>

                            <a href="{{ route('user.general-services.inventory.export', request()->query()) }}" class="btn btn-success">
                                Download Excel
                            </a>



                            <!-- ✅ Reset Filters -->
                            <a href="{{ route('user.general-services.inventory.export', request()->query()) }}" class="btn btn-danger">Reset</a>
                        </div>
                    </form>

                    <!-- ✅ Table Data -->
                    <div class="table-responsive">
    <table class="table table-bordered" id="serviceable-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Particular</th>
                                    <th>Description</th>
                                    <th>End User</th>
                                    <th>Office</th>
                                    <th>Division</th>
                                    <th>Section</th>
                                    <th>Amount</th>
                                    <th>Upload Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventory-table-body">
                                @foreach($serviceables as $index => $item)
                                <tr>
                                    <td>{{ $serviceables->firstItem() + $index }}</td>
                                    <td>{{ $item->equipment_id }}</td>
                                    <td>{{ $item->property_number }}</td>
                                    <td>{{ $item->particular }}</td>
                                    <td>{!! nl2br(e($item->description)) !!}</td>
                                    <td>{{ $item->end_user }}</td>
                                    <td>{{ $item->office }}</td>
                                    <td>{{ $item->division }}</td>
                                    <td>{{ $item->section }}</td>
                                    <td>{{ number_format((float)$item->amount, 2, '.', ',') }}</td>
                                    <td>
                                        @if($item->upload_image)
                                            <img src="{{ asset('storage/' . $item->upload_image) }}" 
                                                alt="Image" 
                                                class="img-thumbnail preview-image" 
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal"
                                                data-image="{{ asset('storage/' . $item->upload_image) }}">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical">
                                            <a href="{{ route('requests.request_update', $item->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
                                            <a href="{{ route('requests.request_transfer', $item->equipment_id) }}" class="btn btn-warning btn-sm">Transfer</a>
                                            <a href="{{ route('requests.request_unserviceable',$item->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ✅ Pagination (Preserves Filters) -->
<div class="d-flex justify-content-center mt-4 pagination-container">
    {{ $serviceables->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- ✅ Image Preview Modal -->
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
    function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            const submitForm = debounce(() => filterForm.submit(), 500);

            const searchInput = document.getElementById('searchInput');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const ppeCategory = document.getElementById('ppeCategory');

            if (searchInput) searchInput.addEventListener('input', submitForm);
            if (dateFrom) dateFrom.addEventListener('change', submitForm);
            if (dateTo) dateTo.addEventListener('change', submitForm);
            if (ppeCategory) ppeCategory.addEventListener('change', submitForm);
        }

        // ✅ Fix Image Preview (Works after Pagination)
        document.addEventListener('click', function (event) {
            const target = event.target.closest('.preview-image');
            if (target) {
                const modalImage = document.getElementById('modalImage');
                if (modalImage) {
                    modalImage.src = target.getAttribute('data-image');
                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                }
            }
        });

        // ✅ Ensure DataTable reloads after closing the image preview modal
        const imagePreviewModal = document.getElementById('imagePreviewModal');
        if (imagePreviewModal) {
            $('#imagePreviewModal').on('hidden.bs.modal', function () {
                const table = $('table').DataTable();
                if (table) table.ajax.reload(null, false); // Reload without resetting pagination
            });
        }
    });
</script>

@endsection
