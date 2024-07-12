@extends('layouts.admin')

@section('title', 'GSS Admin Dashboard')
@section('page-title', 'GSS Admin Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
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
</div>
@endsection
