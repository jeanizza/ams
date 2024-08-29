@extends('layouts.admin')

@section('title', 'Maintenance Ledger')
@section('page-title', 'Maintenance Ledger')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Maintenance Ledger</div>
                <div class="card-body">
                    <!-- Search form -->
                    <form method="GET" action="{{ route('gss.admin.ledger.search') }}">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="property_number" placeholder="Enter Property Number" aria-label="Property Number" aria-describedby="button-search">
                            <button class="btn btn-outline-secondary" type="submit" id="button-search">Search</button>
                        </div>
                    </form>

                    <!-- Display ledger details -->
                    @if(isset($ledgerDetails))
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Purchase Order No.</th>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Supplier</th>
                                    <th>Defects</th>
                                    <th>Particulars</th>
                                    <th>Unit Cost</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ledgerDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->po_number }}</td>
                                        <td>{{ $detail->date_created }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $detail->unit }}</td>
                                        <td>{{ $detail->supplier }}</td>
                                        <td>{{ $detail->defects }}</td>
                                        <td>{{ $detail->particular }}</td>
                                        <td>{{ $detail->unit_cost }}</td>
                                        <td>{{ $detail->total_amount }}</td>
                                        <td>{{ $detail->remarks }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-end">Total:</td>
                                    <td>{{ $ledgerDetails->sum('total_amount') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        @if(session('message'))
                            <div class="alert alert-warning">
                                {{ session('message') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
