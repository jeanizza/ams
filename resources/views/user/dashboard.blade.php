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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
