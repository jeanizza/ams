@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">Dashboard</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">Card 1</div>
                                <div class="card-body">
                                    <h5 class="card-title">Success card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-header">Card 2</div>
                                <div class="card-body">
                                    <h5 class="card-title">Danger card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-header">Card 3</div>
                                <div class="card-body">
                                    <h5 class="card-title">Warning card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Card 4</div>
                                <div class="card-body">
                                    <h5 class="card-title">Info card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">Main Content</div>
                                <div class="card-body">
                                    <h5 class="card-title">Primary card title</h5>
                                    <p class="card-text">Some more content goes here. You can add tables, charts, or other widgets as needed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
