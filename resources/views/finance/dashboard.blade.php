@extends('layouts.finance')

@section('title', 'Finance Dashboard')
@section('page-title', 'Finance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">Finance Dashboard</div>
                <div class="card-body">
                    <h5>Welcome, {{ $user->name }}! {{ $user->office }}</h5>
                    <p>This is the Finance dashboard.</p>
                </div>
            </div>
            
            <!-- Notification Cards -->
            <div class="row mt-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Route Documents</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <a href="#routeDocumentsTable" class="small-box-footer" data-toggle="collapse" data-parent="#accordion">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>53<sup style="font-size: 20px">%</sup></h3>
                            <p>Bounce Rate</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#bounceRateTable" class="small-box-footer" data-toggle="collapse" data-parent="#accordion">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $equipmentCount }}</h3>
                            <p>Equipment Near Date End</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <a href="#equipmentNearEndDateTable" class="small-box-footer" data-toggle="collapse" data-parent="#accordion">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>65</h3>
                            <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#uniqueVisitorsTable" class="small-box-footer" data-toggle="collapse" data-parent="#accordion">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Accordion -->
            <div id="accordion">
                <!-- Route Documents Table -->
                <div id="routeDocumentsTable" class="collapse">
                    <div class="card">
                        <div class="card-body">
                            <h5>Route Documents</h5>
                            <table class="table table-bordered">
                                <!-- Add your table content here -->
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bounce Rate Table -->
                <div id="bounceRateTable" class="collapse">
                    <div class="card">
                        <div class="card-body">
                            <h5>Bounce Rate</h5>
                            <table class="table table-bordered">
                                <!-- Add your table content here -->
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Include Equipment Near Date End Table -->
                <div id="equipmentNearEndDateTable" class="collapse">
                    @include('finance.dashboard.equipment_near_end', ['equipmentItems' => $equipmentItems])
                </div>

                <!-- Unique Visitors Table -->
                <div id="uniqueVisitorsTable" class="collapse">
                    <div class="card">
                        <div class="card-body">
                            <h5>Unique Visitors</h5>
                            <table class="table table-bordered">
                                <!-- Add your table content here -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle card click
    $('.small-box-footer').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        // Collapse all other sections
        $('.collapse').not(target).collapse('hide');
        // Toggle the target section
        $(target).collapse('toggle');
    });


});
</script>
@endsection
