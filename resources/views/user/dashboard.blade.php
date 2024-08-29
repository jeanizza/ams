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
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the User dashboard.</p>

                    <h5>Equipment Near Date End</h5>
                    @if($equipmentItems->isEmpty())
                        <p>No equipment items are due within the next 15 days.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Property Number</th>
                                    <th>Item Description</th>
                                    <th>Date End</th>
                                    <th>Days Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipmentItems as $item)
                                    <tr>
                                        <td>{{ $item->property_number }}</td>
                                        <td>{{ $item->particular }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date_end)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date_end)->diffInDays($today) }}</td>
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
