@extends('layouts.admin')

@section('title', 'Unserviceable')
@section('page-title', 'Unserviceable')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Unserviceable</div>
                <div class="card-body">
                <form method="POST" action="{{ route('serviceables.unserviceable_update', $serviceable->equipment_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <input type="text" class="form-control" id="property_type" name="property_type" value="{{ $serviceable->property_type }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="property_number">Property Number</label>
                            <input type="text" class="form-control" id="property_number" name="property_number" value="{{ $serviceable->property_number }}" readonly>
                        </div>
                    
                        <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control" id="category" name="category" value="{{ $serviceable->category }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="particular">Particular</label>
                            <input type="text" class="form-control" id="particular" name="particular" value="{{ $serviceable->particular }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $serviceable->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="{{ $serviceable->brand }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="{{ $serviceable->model }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="serial_no">Serial No.</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" value="{{ $serviceable->serial_no }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" 
                                value="{{ isset($serviceable->amount) ? number_format($serviceable->amount, 2) : '' }}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="po_number">Purchase Order No.</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" value="{{ $serviceable->po_number }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="date" class="form-control" id="date_acquired" name="date_acquired" value="{{ $serviceable->date_acquired }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" name="end_user" value="{{ $serviceable->end_user }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" value="{{ $serviceable->position }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="office">Office</label>
                            <input type="text" class="form-control" id="office" name="office" value="{{ $serviceable->office }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="division">Division</label>
                            <input type="text" class="form-control" id="division" name="division" value="{{ $serviceable->division }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="section">Section</label>
                            <input type="text" class="form-control" id="section" name="section" value="{{ $serviceable->section }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $serviceable->remarks }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="fund">Fund Source</label>
                            <input type="text" class="form-control" id="fund" name="fund" value="{{ $serviceable->fund }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="lifespan">Estimated Useful Life</label>
                            <input type="text" class="form-control" id="lifespan" name="lifespan" value="{{ $serviceable->lifespan }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="date_end">Date Terminous</label>
                            <input type="text" class="form-control" id="date_end" name="date_end" value="{{ $serviceable->date_end }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="uploaded_by">Uploaded By</label>
                            <input type="text" class="form-control" id="uploaded_by" name="uploaded_by" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <!-- Upload Image -->
                        <div class="form-group">
                            <label for="upload_image">Upload Image</label>
                            @if($serviceable->upload_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $serviceable->upload_image) }}" alt="Uploaded Image" class="img-fluid img-thumbnail" style="max-width: 100%; max-height: 300px;">
                                </div>
                            @endif
                        </div>

                        <!-- Unserviceable Details -->
                        <div class="form-group">
                            <label for="unserviceable_condition">Condition</label>
                            <select class="form-control" id="unserviceable_condition" name="unserviceable_condition">
                                <option value="" disabled selected hidden>Select Condition</option>
                                <option value="Unserviceable" {{ isset($unserviceable->unserviceable_condition) && $unserviceable->unserviceable_condition == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                                <option value="Damage" {{ isset($unserviceable->unserviceable_condition) && $unserviceable->unserviceable_condition == 'Damage' ? 'selected' : '' }}>Damage</option>
                                <option value="Lost" {{ isset($unserviceable->unserviceable_condition) && $unserviceable->unserviceable_condition == 'Lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>


                        <!-- Upload Unserviceable Image -->
                        <div class="form-group">
                            <label for="unserviceable_image">Upload Unserviceable Image</label>
                            @if(isset($unserviceable) && $unserviceable->unserviceable_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $unserviceable->unserviceable_image) }}" alt="Unserviceable Image" class="img-fluid img-thumbnail" style="max-width: 300px;">
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="unserviceable_image" name="unserviceable_image" accept="image/png, image/jpeg">
                        </div>

                        <!-- Uploaded By -->
                        <div class="form-group">
                            <label for="updated_by">Updated By</label>
                            <input type="text" class="form-control" id="updated_by" name="updated_by" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <!-- Returned By -->
                        <div class="form-group">
                            <label for="returned_by">Returned By</label>
                            <input type="text" class="form-control" id="returned_by" name="returned_by" value="{{ isset($unserviceable->returned_by) ? $unserviceable->returned_by : '' }}" >
                        </div>

                        <div class="form-group">
                            <label for="date_returned">Date Returned</label>
                            <input type="date" class="form-control" id="date_returned" name="date_returned" 
                                value="{{ isset($unserviceable->date_returned) ? $unserviceable->date_returned : '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ isset($unserviceable) ? 'Update' : 'Unserviceable' }}</button>
                        <a href="{{ route('gss.admin.unserviceable_items') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection