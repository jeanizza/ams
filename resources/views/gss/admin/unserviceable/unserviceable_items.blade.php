@extends('layouts.admin')

@section('title', 'Unserviceable Items')
@section('page-title', 'Unserviceable Items')

@section('content')
<div class="container-fluid unserviceable-items">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Statistics Section -->
            <div id="statistics">
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount All</span>
                                <span class="info-box-number">{{ number_format($totalAmountAll, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box bg-dark text-white">
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount (Year Selected)</span>
                                <span class="info-box-number">{{ number_format($totalAmountYear, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Dropdown -->
            <form method="POST" action="{{ route('gss.admin.unserviceable_items') }}">
                @csrf
                <div class="form-group">
                    <label for="year">Select Year</label>
                    <select id="year" name="year" class="form-control" onchange="this.form.submit()">
                        <option value="">Select Year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request()->get('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="card">
                <div class="card-header">Unserviceable Items</div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('gss.admin.unserviceable_items') }}" id="search-form">
                        <div class="input-group mb-3">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ request()->get('search') }}">
                        </div>
                    </form>

                    <table class="table table-bordered" id="unserviceable-items-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Property Number</th>
                                <th>Particular</th>
                                <th>Description</th>
                                <th>Office</th>
                                <th>Division</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="table_data">
                            @include('gss.admin.unserviceable.unserviceable_items_table', ['unserviceableItems' => $unserviceableItems])
                        </tbody>
                    </table>

                    <div class="page-navigation" id="pagination_links">
                        @include('gss.admin.unserviceable.unserviceable_items_pagination', ['unserviceableItems' => $unserviceableItems])
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
