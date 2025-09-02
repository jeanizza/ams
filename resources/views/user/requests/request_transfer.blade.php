@extends('layouts.user')

@section('title', 'Request Transfer')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-warning text-white">Transfer Equipment</div>
        <div class="card-body">
            <form action="{{ route('requests.store_transfer', $equipment->equipment_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="property_number" class="form-label">Property Number</label>
                    <input type="text" name="property_number" class="form-control" value="{{ $equipment->property_number }}" required>
                </div>
                <div class="mb-3">
                    <label for="particular" class="form-label">Particular</label>
                    <input type="text" name="particular" class="form-control" value="{{ $equipment->particular }}" required>
                </div>
                <div class="mb-3">
                    <label for="division" class="form-label">Division</label>
                    <input type="text" name="division" class="form-control" value="{{ $equipment->division }}" required>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" name="section" class="form-control" value="{{ $equipment->section }}" >
                </div>
                <div class="form-group">
                            <label for="transfer_office">Transfer to New Office</label>
                            <select class="form-control" id="transfer_office" name="transfer_office" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="PENRO Camiguin">PENRO Camiguin</option>
                                <option value="PENRO Bukidnon">PENRO Bukidnon</option>
                                <option value="CENRO Don Carlos">CENRO Don Carlos</option>
                                <option value="CENRO Manolo Fortich">CENRO Manolo Fortich</option>
                                <option value="CENRO Talakag">CENRO Talakag</option>
                                <option value="CENRO Valencia">CENRO Valencia</option>
                                <option value="PENRO Lanao del Norte">PENRO Lanao del Norte</option>
                                <option value="CENRO Iligan">CENRO Iligan</option>
                                <option value="CENRO Kolambugan">CENRO Kolambugan</option>
                                <option value="PENRO Misamis Occidental">PENRO Misamis Occidental</option>
                                <option value="CENRO Oroquieta">CENRO Oroquieta</option>
                                <option value="CENRO Ozamis">CENRO Ozamis</option>
                                <option value="PENRO Misamis Oriental">PENRO Misamis Oriental</option>
                                <option value="CENRO Gingoog">CENRO Gingoog</option>
                                <option value="CENRO Initao">CENRO Initao</option>
                            </select>
                        </div>
                <div class="mb-3">
                    <label for="transfer_enduser" class="form-label">Transfer to New End User</label>
                <input type="text"name="transfer_enduser" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="transfer_position" class="form-label">New End User Position</label>
                <input type="text"name="transfer_position" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="reason_transfer" class="form-label">Purpose</label>
                    <textarea name="reason_transfer" id="reason_transfer" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-warning">Submit Request</button>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
