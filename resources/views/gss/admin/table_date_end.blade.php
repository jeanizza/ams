@extends('layouts.admin')

@section('title', 'GSS Admin Dashboard')

@section('page-title', 'GSS Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">GSS Admin Dashboard</div>
                <div class="card-body">
                    <h5 class="mb-3">Welcome, {{ $user->name }}! <small class="text-muted">{{ $user->office }}</small></h5>
                    <p class="text-muted">This is the GSS admin dashboard.</p>

                    <h4 class="mt-4">Equipment Items Near End Date</h4>

                    <!-- Division Filter Form -->
                    <form method="GET" action="{{ route('gss.admin.dashboard') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="division" class="fw-bold">Select Division:</label>
                                <select class="form-control" id="division" name="division" onchange="this.form.submit()">
                                    <option value="">All Divisions</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->division }}" {{ request('division') == $division->division ? 'selected' : '' }}>
                                            {{ $division->division }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    @if($equipmentItems->isEmpty())
                        <div class="alert alert-warning text-center">
                            <strong>No equipment items</strong> are due within the next 5 days.
                        </div>
                    @else
                        <h5 class="mt-3">Found <strong>{{ $equipmentItems->total() }}</strong> items:</h5>

                        <!-- Export Button -->
                        @if(!$equipmentItems->isEmpty())
                            <div class="d-flex justify-content-end mb-3">
                                <button onclick="exportTableToExcel()" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Download Excel
                                </button>
                            </div>
                        @endif



                        <!-- Responsive Table -->
                        <div class="table-responsive">
                            <table id="equipmentTable" class="table table-bordered table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No</th>
                                        <th style="width: 10%;">Property Number</th>
                                        <th style="width: 12%;">Particular</th>
                                        <th style="width: 18%;">Description</th>
                                        <th style="width: 10%;">Division</th>
                                        <th style="width: 10%;">Section</th>
                                        <th style="width: 10%;">Date Acquired</th>
                                        <th style="width: 5%;">Lifespan</th>
                                        <th style="width: 10%;">Date End</th>
                                        <th class="text-center" style="width: 10%;">Image</th>
                                        <th class="text-center" style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipmentItems as $item)
                                        <tr style="height: 120px; vertical-align: middle;">  
                                            <td class="text-center">{{ $equipmentItems->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->property_number }}</td>
                                            <td>{{ $item->particular }}</td>
                                            <td class="text-truncate" style="max-height: 120px; white-space: normal; word-wrap: break-word; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical;" title="{{ $item->description }}">
                                                {{ $item->description }}
                                            </td>
                                            <td>{{ $item->division }}</td>
                                            <td>{{ $item->section }}</td>
                                            <td>{{ $item->date_acquired }}</td>
                                            <td>{{ $item->lifespan }}</td>
                                            <td class="fw-bold text-danger">{{ $item->date_end }}</td>
                                            <td class="text-center">
                                                @if($item->upload_image)
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $loop->index }}">
                                                        <img src="{{ asset('storage/' . $item->upload_image) }}" 
                                                            alt="Image" class="img-thumbnail" 
                                                            style="width: 100px; height: 100px; object-fit: cover;">
                                                    </a>

                                                    <!-- Image Preview Modal -->
                                                    <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $loop->index }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Image Preview</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ asset('storage/' . $item->upload_image) }}" 
                                                                        alt="Image" class="img-fluid">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">No image</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('serviceables.update_serviceable', $item->equipment_id) }}" class="btn btn-success btn-sm w-100 mb-1">Update</a>
                                                <a href="{{ route('serviceables.transfer_serviceable', $item->equipment_id) }}" class="btn btn-warning btn-sm w-100 mb-1" style="background-color: #ffcc00; border-color: #ffcc00;">Transfer</a>
                                                <a href="{{ route('serviceables.unserviceable_form', $item->equipment_id) }}" class="btn btn-danger btn-sm w-100">Unserviceable</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="text-muted">Showing page <strong>{{ $equipmentItems->currentPage() }}</strong> of <strong>{{ $equipmentItems->lastPage() }}</strong></p>
                            </div>
                            <div id="pagination_links">
                                {{ $equipmentItems->appends(request()->only('search', 'division'))->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SheetJS Library for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
    function exportTableToExcel() {
        console.log("Fetching displayed data for export...");

        // Get selected division filter
        let division = document.getElementById("division").value;

        // Fetch all displayed equipment records (matching table filters)
        fetch(`/gss/admin/fetch-displayed-equipment?division=${division}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    alert("No data to export!");
                    return;
                }

                console.log("Displayed data fetched:", data); // Debugging

                let workbook = XLSX.utils.book_new();

                // Define headers (excluding Image & Actions columns)
                let headers = [
                    "Property Number", "Particular", "Description", "Amount",
                    "Division", "Section", "Date Acquired", "Lifespan", "Date End"
                ];

                // Convert JSON data to array format for Excel
                let rows = data.map(item => [
                    item.property_number,
                    item.particular,
                    item.description,
                    item.amount,
                    item.division,
                    item.section,
                    item.date_acquired,
                    item.lifespan,
                    item.date_end
                ]);

                // Create worksheet
                let worksheet = XLSX.utils.aoa_to_sheet([headers, ...rows]);

                // Append worksheet to workbook
                XLSX.utils.book_append_sheet(workbook, worksheet, "Equipment Data");

                // Save file
                XLSX.writeFile(workbook, "displayed_equipment.xlsx");

                console.log("Excel file generated successfully!");
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                alert("Failed to fetch data. Please try again.");
            });
    }
</script>




@endsection
