@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">Accounting User Dashboard</div>
                <div class="card-body">
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the accounting user dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
