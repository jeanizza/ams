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
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }} - {{ $user->div_name }}</h5>
                    <p>This is the User dashboard.</p>



                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search & Reset Form -->
                    <form method="GET" action="{{ route('user.dashboard') }}" class="mb-3 d-flex justify-content-between">
                        <div class="input-group" style="width: 50%;">
                            <input type="text" id="search-input" name="search" class="form-control" placeholder="Search by Property Number, Particular, or End User">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary text-white px-4 d-flex align-items-center justify-content-center">Reset</a>
                        </div>
                    </form>

                    <h5>Equipment Near Date End</h5>
                    @if($equipmentItems->isEmpty())
                        <p>No equipment items are due within the next 15 days.</p>
                    @else
                        <p>Found {{ $equipmentItems->total() }} items:</p>
                        
                        <div class="table-responsive">
                            <table class="table text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Property Number</th>
                                        <th>Particular</th>
                                        <th>End User</th>
                                        <th>Office</th>
                                        <th>Division</th>
                                        <th>Date Acquired</th>
                                        <th>Date End</th>
                                        <th>Image</th>
                                        <th>Request Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipmentItems as $index => $item)
                                        <tr>
                                            <td>{{ $equipmentItems->firstItem() + $index }}</td> <!-- Continuous numbering -->
                                            <td>{{ $item->equipment_id }}</td>
                                            <td>{{ $item->property_number }}</td>
                                            <td>{{ $item->particular }}</td>
                                            <td>{{ $item->end_user }}</td>
                                            <td>{{ $item->office }}</td>
                                            <td>{{ $item->division }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->date_acquired)->format('Y-m-d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->date_end)->format('Y-m-d') }}</td>
                                            <td>
                                                @if($item->upload_image)
                                                    <img src="{{ asset('storage/' . $item->upload_image) }}" alt="Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">No Image</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical">
                                                    <a href="{{ route('requests.request_update', $item->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
                                                    <a href="{{ route('requests.request_transfer', $item->equipment_id) }}" class="btn btn-warning btn-sm">Transfer</a>
                                                    <a href="{{ route('requests.request_unserviceable', $item->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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



<style>
    .btn-group-vertical a {
        margin-bottom: 5px;
    }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $("#search-input").on("input", function () {
            let searchValue = $(this).val();

            $.ajax({
                url: "{{ route('user.dashboard') }}",
                type: "GET",
                data: { search: searchValue },
                success: function (response) {
                    let tableBody = $("tbody");
                    tableBody.empty(); // Clear existing rows

                    if (response.length > 0) {
                        $.each(response, function (index, item) {
                            let row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.equipment_id}</td>
                                    <td>${item.property_number}</td>
                                    <td>${item.particular}</td>
                                    <td>${item.end_user}</td>
                                    <td>${item.office}</td>
                                    <td>${item.division}</td>
                                    <td>${item.date_acquired}</td>
                                    <td>${item.date_end}</td>
                                    <td>
                                        ${item.upload_image ? `<img src="/storage/${item.upload_image}" alt="Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">` : `<span class="text-muted">No Image</span>`}
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical">
                                            <a href="/requests/request_update/${item.equipment_id}" class="btn btn-success btn-sm">Update</a>
                                            <a href="/requests/request_transfer/${item.equipment_id}" class="btn btn-warning btn-sm">Transfer</a>
                                            <a href="/requests/request_unserviceable/${item.equipment_id}" class="btn btn-danger btn-sm">Unserviceable</a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append(`<tr><td colspan="11" class="text-center">No results found.</td></tr>`);
                    }
                }
            });
        });
    });
</script>





@endsection
