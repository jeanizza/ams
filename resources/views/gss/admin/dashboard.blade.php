@extends('layouts.admin')

@section('title', 'GSS Admin Dashboard')
@section('page-title', 'GSS Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">GSS Admin Dashboard</div>
                <div class="card-body">
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the GSS admin dashboard.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the table partial below the welcome box -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include('gss.admin.table_date_end', ['equipmentItems' => $equipmentItems, 'divisions' => $divisions])
        </div>
    </div>
</div>
@endsection
