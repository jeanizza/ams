@extends('layouts.finance')

@section('title', 'Reconcile Items')
@section('page-title', 'Reconcile Items')

@section('content')
<div class="container-fluid reconcile-items">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Statistics Section -->
            <div id="statistics">
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount All (Semi-expendable)</span>
                                <span class="info-box-number">
                                    {{ number_format($totalAmountSemiExpendable, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount All (PPE)</span>
                                <span class="info-box-number">
                                    {{ number_format($totalAmountPPE, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount (Year Selected)</span>
                                <span class="info-box-number">
                                    {{ number_format($reconcileTotalAmountYear, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year and Remarks Dropdown -->
            <form method="GET" action="{{ route('finance.reconcile_items') }}">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year">Select Year</label>
                            <select id="year" name="year" class="form-control" onchange="this.form.submit()">
                                <option value="" disabled selected hidden>Select</option>
                                @foreach ($years as $yearOption)
                                    <option value="{{ $yearOption }}" {{ request()->get('year') == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="remarks">Select Remarks</label>
                            <select id="remarks" name="remarks" class="form-control" onchange="this.form.submit()">
                                <option value="" disabled selected hidden>Select</option>
                                <option value="Semi-expendable" {{ request()->get('remarks') == 'Semi-expendable' ? 'selected' : '' }}>Semi-expendable</option>
                                <option value="PPE" {{ request()->get('remarks') == 'PPE' ? 'selected' : '' }}>PPE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Search Form -->
            <form method="GET" action="{{ route('finance.reconcile_items') }}" id="search-form" class="mb-4">
                <div class="input-group mb-3">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ $search }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-header">{{ $year || $remarks || $search ? 'Reconcile Items' : 'Add New Reconcile Items' }}</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($year || $remarks || $search)
                        <!-- Reconcile Items Table (Update Functionality) -->
                        <table class="table table-bordered" id="reconcile-items-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Particular</th>
                                    <th>Description</th>
                                    <th>Office</th>
                                    <th>Division</th>
                                    <th>Amount</th>
                                    <th>PO Number</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="table_data">
                                @foreach($reconcileItems as $item)
                                    <tr>
                                        <td>{{ $item->reconcile_id }}</td>
                                        <td>{{ $item->property_number }}</td>
                                        <td>{{ $item->particular }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->office }}</td>
                                        <td>{{ $item->division }}</td>
                                        <td>{{ number_format((float)$item->amount, 2) }}</td>
                                        <td>{{ $item->po_number }}</td>
                                        <td>{{ $item->remarks_reconcile }}</td>
                                        <td>
                                            <form action="{{ route('finance.update_reconcile') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="reconcile_id" value="{{ $item->reconcile_id }}">
                                                <input type="hidden" name="property_number" value="{{ $item->property_number }}">
                                                <input type="hidden" name="po_number" value="{{ $item->po_number }}">
                                                <input type="hidden" name="amount" value="{{ $item->amount }}">
                                                <input type="hidden" name="date_acquired" value="{{ $item->date_acquired }}">
                                                <input type="hidden" name="user" value="{{ Auth::user()->name }}">
                                                <select name="remarks" class="form-control">
                                                    <option value="" disabled selected hidden>Select</option>
                                                    <option value="Semi-expendable">Semi-expendable</option>
                                                    <option value="PPE">PPE</option>
                                                </select>
                                        </td>
                                        <td>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination for Reconcile Items -->
                        <div class="page-navigation mt-4" id="pagination_links">
                            {{ $reconcileItems->appends(request()->only('search', 'year', 'remarks'))->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <!-- Default Equipment Items Table (Add Functionality) -->
                        <table class="table table-bordered" id="equipment-items-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property Number</th>
                                    <th>Particular</th>
                                    <th>Description</th>
                                    <th>Office</th>
                                    <th>Division</th>
                                    <th>Amount</th>
                                    <th>PO Number</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="table_data">
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->equipment_id }}</td>
                                        <td>{{ $item->property_number }}</td>
                                        <td>{{ $item->particular }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->office }}</td>
                                        <td>{{ $item->division }}</td>
                                        <td>{{ number_format((float)$item->amount, 2) }}</td>
                                        <td>{{ $item->po_number }}</td>
                                        <td>{{ $item->remarks }}</td>
                                        <td>
                                            <form action="{{ route('finance.add_reconcile') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="property_number" value="{{ $item->property_number }}">
                                                <input type="hidden" name="po_number" value="{{ $item->po_number }}">
                                                <input type="hidden" name="amount" value="{{ $item->amount }}">
                                                <input type="hidden" name="date_acquired" value="{{ $item->date_acquired }}">
                                                <input type="hidden" name="user" value="{{ Auth::user()->name }}">
                                                <select name="remarks" class="form-control">
                                                    <option value="" disabled selected hidden>Select</option>
                                                    <option value="Semi-expendable">Semi-expendable</option>
                                                    <option value="PPE">PPE</option>
                                                </select>
                                        </td>
                                        <td>
                                                <button type="submit" class="btn btn-primary">Add</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination for Equipment Items -->
                        <div class="page-navigation mt-4" id="pagination_links">
                            {{ $items->appends(request()->only('search'))->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to reset the form and clear URL parameters on page refresh -->
<script>
    if (performance.navigation.type == 1) {
        window.location.href = '{{ url("finance/reconcile-items") }}';
    }
</script>
@endsection
