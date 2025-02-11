@extends('layouts.finance')

@section('title', 'Disposal Details')
@section('page-title', 'Disposal Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Disposal Details</div>
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
                                    <th>Disposal Value</th>
                                    <th>Carrying Value</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <td>{{ number_format($detail->disposal_value, 2) }}</td>
                                        <td>{{ number_format($detail->carrying_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
