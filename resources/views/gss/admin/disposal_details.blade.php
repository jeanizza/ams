@extends('layouts.admin')

@section('title', 'Disposal Details')
@section('page-title', 'Disposal Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Disposal Details
                    <form method="GET" id="filterForm" class="form-inline float-right">
                        <input type="text" id="search" name="search" class="form-control mr-2" placeholder="Search" value="{{ request('search') }}">
                        <input type="date" id="from_date" name="from_date" class="form-control mr-2" value="{{ request('from_date') }}">
                        <input type="date" id="to_date" name="to_date" class="form-control mr-2" value="{{ request('to_date') }}">
                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ route('gss.admin.disposal_details') }}" class="btn btn-secondary">Reset</a>
                        <button type="button" id="exportExcel" class="btn btn-success">Download Excel</button>
                    </form>
                </div>
                <div class="card-body">
                    @if($disposalDetails->isEmpty())
                        <p>No disposal details found.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Property Type</th>
                                    <th>Property Number</th>
                                    <th>Particular</th>
                                    <th>Description</th>
                                    <th>Division</th>
                                    <th>Amount</th>
                                    <th>PO Number</th>
                                    <th>Date Acquired</th>
                                    <th>Date End</th>
                                    <th>Date Returned</th>
                                    <th>Accumulated Depreciation</th>
                                    <th>Carrying Value</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach($disposalDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->property_type }}</td>
                                        <td>{{ $detail->property_number }}</td>
                                        <td>{{ $detail->particular }}</td>
                                        <td>{{ $detail->description }}</td>
                                        <td>{{ $detail->division }}</td>
                                        <td>{{ number_format($detail->amount, 2) }}</td>
                                        <td>{{ $detail->po_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->date_acquired)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->date_end)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->date_returned)->format('Y-m-d') }}</td>
                                        <td>{{ number_format($detail->accumulated_depreciation, 2) }}</td>
                                        <td>{{ number_format($detail->carrying_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $disposalDetails->links() }} <!-- Pagination -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("exportExcel").addEventListener("click", function () {
    let search = document.getElementById("search").value || '';
    let fromDate = document.getElementById("from_date").value || '';
    let toDate = document.getElementById("to_date").value || '';

    let exportUrl = `{{ route('gss.admin.exportDisposal') }}?search=${encodeURIComponent(search)}&from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}`;

    window.location.href = exportUrl;
});
</script>

@endsection
