@extends('layouts.admin')

@section('title', 'GSS Admin Dashboard')
<div class="gss-title">@section('page-title', 'GSS Admin Dashboard')</div>

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">GSS Admin Dashboard</div>
                <div class="card-body">
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the GSS admin dashboard.</p>

                    <h4>Equipment Items Due Soon</h4>

                    <form method="GET" action="{{ route('gss.admin.dashboard') }}">
                        <div class="form-group">
                            <label for="division">Select Division:</label>
                            <select class="form-control" id="division" name="division" onchange="this.form.submit()">
                                <option value="">All Divisions</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->division }}" {{ request('division') == $division->division ? 'selected' : '' }}>
                                        {{ $division->division }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($equipmentItems->isEmpty())
                        <p>No equipment items are due within the next 5 days.</p>
                    @else
                        <h5>Found {{ $equipmentItems->count() }} items:</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Property Number</th>
                                    <th>Office</th>
                                    <th>Date End</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipmentItems as $item)
                                    <tr>
                                        <td>{{ $item->property_number }}</td>
                                        <td>{{ $item->office }}</td>
                                        <td>{{ $item->date_end }}</td>
                                        <td>
                                            <a href="{{ route('serviceables.update_serviceable', $item->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
                                            <a href="{{ route('serviceables.transfer_serviceable', $item->equipment_id) }}" class="btn btn-warning btn-sm" style="background-color: #ffcc00; border-color: #ffcc00;">Transfer</a>
                                            <a href="{{ route('serviceables.unserviceable_form', $item->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                                        </td>
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
