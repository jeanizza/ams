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
                                <span class="info-box-text">Total Amount (Semi-expendable)</span>
                                <span class="info-box-number">
                                    {{ number_format($totalAmountSemiExpendable, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount (PPE)</span>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year">Select Year</label>
                            <select id="year" name="year" class="form-control" onchange="this.form.submit()">
                                <option value="">Select</option>
                                @foreach ($years as $yearOption)
                                    <option value="{{ $yearOption }}" {{ request('year') == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="remarks">Select Remarks</label>
                            <select id="remarks" name="remarks" class="form-control" onchange="this.form.submit()">
                                <option value="" {{ request('remarks') == '' ? 'selected' : '' }}>All</option>
                                <option value="Semi-expendable" {{ request('remarks') == 'Semi-expendable' ? 'selected' : '' }}>Semi-expendable</option>
                                <option value="PPE" {{ request('remarks') == 'PPE' ? 'selected' : '' }}>PPE</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Reconcile Status</label>
                            <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="reconciled" {{ request('status') == 'reconciled' ? 'selected' : '' }}>Reconciled</option>
                                <option value="for_reconcile" {{ request('status') == 'for_reconcile' ? 'selected' : '' }}>For Reconcile</option>
                            </select>
                        </div>
                    </div>

                </div>
            </form>

            <!-- Search Form -->
            <form method="GET" action="{{ route('finance.reconcile_items') }}" id="search-form" class="mb-4">
                <div class="input-group mb-3">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by Property Number or Particular" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <button type="button" class="btn btn-secondary" id="resetSearch">Reset</button>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-header">Serviceable Items</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Items Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
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
                        <tbody>
                            @foreach($allItems as $item)
                                <tr>
                                    <td>{{ $allItems->firstItem() + $loop->index }}</td>
                                    <td>{{ $item->property_number }}</td>
                                    <td>{{ $item->particular }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->office }}</td>
                                    <td>{{ $item->division }}</td>
                                    <td>{{ number_format((float)$item->amount, 2) }}</td>
                                    <td>{{ $item->po_number }}</td>
                                    <td>{{ $item->reconcile_status ?? '' }}</td>
                                    <td>
                                        <form action="{{ route('finance.' . ($item->reconcile_status ? 'update_reconcile' : 'add_reconcile')) }}" method="POST">
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
                                            <button type="submit" class="btn {{ $item->reconcile_status ? 'btn-primary' : 'btn-success' }}">
                                                {{ $item->reconcile_status ? 'Update' : 'Add' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="page-navigation mt-4">
                        {{ $allItems->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Reset Search -->
<script>
    document.getElementById('resetSearch').addEventListener('click', function() {
        window.location.href = '{{ route("finance.reconcile_items") }}';
    });

    document.addEventListener("DOMContentLoaded", function () {
        let filterForm = document.getElementById('filterForm');

        document.getElementById('year').addEventListener('change', function () {
            filterForm.submit();
        });

        document.getElementById('remarks').addEventListener('change', function () {
            filterForm.submit();
        });

        document.getElementById('status').addEventListener('change', function () {
            filterForm.submit();
        });
    });
    
</script>
@endsection
