@extends('layouts.admin')

@section('title', 'Transfer Serviceable')
@section('page-title', 'Transfer Serviceable')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Transfer Serviceable</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('serviceables.transfer', $serviceable->id) }}">
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
                            <select class="form-control" id="category" name="category" disabled>
                                <option value="05-Agricultural & Forestry Equipment" {{ $serviceable->category == '05-Agricultural & Forestry Equipment' ? 'selected' : '' }}>05-Agricultural & Forestry Equipment</option>
                                <option value="05-Buildings" {{ $serviceable->category == '05-Buildings' ? 'selected' : '' }}>05-Buildings</option>
                                <option value="05-Communication Equipment" {{ $serviceable->category == '05-Communication Equipment' ? 'selected' : '' }}>05-Communication Equipment</option>
                                <option value="05-Construction Equipment" {{ $serviceable->category == '05-Construction Equipment' ? 'selected' : '' }}>05-Construction Equipment</option>
                                <option value="06-Furniture & Fixtures" {{ $serviceable->category == '06-Furniture & Fixtures' ? 'selected' : '' }}>06-Furniture & Fixtures</option>
                                <option value="05-Information & Communication Technology Equipment (ICT)" {{ $serviceable->category == '05-Information & Communication Technology Equipment (ICT)' ? 'selected' : '' }}>05-Information & Communication Technology Equipment</option>
                                <option value="05-Marine & Fishery Equipment" {{ $serviceable->category == '05-Marine & Fishery Equipment' ? 'selected' : '' }}>05-Marine & Fishery Equipment</option>
                                <option value="05-Office Equipment" {{ $serviceable->category == '05-Office Equipment' ? 'selected' : '' }}>05-Office Equipment</option>
                                <option value="05-Other Land Improvements" {{ $serviceable->category == 'example_category' ? 'selected' : '' }}>05-Other Land Improvements</option>
                                <option value="05-Sports Equipment" {{ $serviceable->category == 'example_category' ? 'selected' : '' }}>05-Sports Equipment</option>
                                <option value="05-Technical & Scientific Equipment" {{ $serviceable->category == 'example_category' ? 'selected' : '' }}>05-Technical & Scientific Equipment</option>
                                <option value="06-Transportation Equipment" {{ $serviceable->category == 'example_category' ? 'selected' : '' }}>06-Transportation Equipment</option>
                                <option value="05-Watercrafts Equipment" {{ $serviceable->category == 'example_category' ? 'selected' : '' }}>05-Watercrafts Equipment</option>
                            </select>
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
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="{{ isset($serviceable->amount) ? str_replace(',', '', $serviceable->amount) : '' }}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="po_number">Purchase Order No.</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" value="{{ $serviceable->po_number }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="date" class="form-control" id="date_acquired" name="date_acquired" value="{{ $serviceable->date_acquired }}">
                        </div>

                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" name="end_user" value="{{ $serviceable->end_user }}">
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" value="{{ $serviceable->position }}">
                        </div>

                        <div class="form-group">
                            <label for="office">Office</label>
                            <!-- <input type="text" class="form-control" id="office" name="office" value="{{ $serviceable->office }}" > -->
                            <select class="form-control" id="office" name="office" required>
                                <option value="Regional Office" {{ $serviceable->office == 'Regional Office' ? 'selected' : '' }}>Regional Office</option>
                                <option value="PENRO Camiguin" {{ $serviceable->office == 'PENRO Camiguin' ? 'selected' : '' }}>PENRO Camiguin</option>
                                <option value="PENRO Bukidnon" {{ $serviceable->office == 'PENRO Bukidnon' ? 'selected' : '' }}>PENRO Bukidnon</option>
                                <option value="CENRO Don Carlos" {{ $serviceable->office == 'CENRO Don Carlos' ? 'selected' : '' }}>CENRO Don Carlos</option>
                                <option value="CENRO Manolo Fortich" {{ $serviceable->office == 'CENRO Manolo Fortich' ? 'selected' : '' }}>CENRO Manolo Fortich</option>
                                <option value="CENRO Talakag" {{ $serviceable->office == 'CENRO Talakag' ? 'selected' : '' }}>CENRO Talakag</option>
                                <option value="CENRO Valencia" {{ $serviceable->office == 'CENRO Valencia' ? 'selected' : '' }}>CENRO Valencia</option>
                                <option value="PENRO Lanao del Norte" {{ $serviceable->office == 'PENRO Lanao del Norte' ? 'selected' : '' }}>PENRO Lanao del Norte</option>
                                <option value="CENRO Iligan" {{ $serviceable->office == 'CENRO Iligan' ? 'selected' : '' }}>CENRO Iligan</option>
                                <option value="CENRO Kolambugan" {{ $serviceable->office == 'CENRO Kolambugan' ? 'selected' : '' }}>CENRO Kolambugan</option>
                                <option value="PENRO Misamis Occidental" {{ $serviceable->office == 'PENRO Misamis Occidental' ? 'selected' : '' }}>PENRO Misamis Occidental</option>
                                <option value="CENRO Oroquieta" {{ $serviceable->office == 'CENRO Oroquieta' ? 'selected' : '' }}>CENRO Oroquieta</option>
                                <option value="CENRO Ozamis" {{ $serviceable->office == 'CENRO Ozamis' ? 'selected' : '' }}>CENRO Ozamis</option>
                                <option value="PENRO Misamis Oriental" {{ $serviceable->office == 'PENRO Misamis Oriental' ? 'selected' : '' }}>PENRO Misamis Oriental</option>
                                <option value="CENRO Gingoog" {{ $serviceable->office == 'CENRO Gingoog' ? 'selected' : '' }}>CENRO Gingoog</option>
                                <option value="CENRO Initao" {{ $serviceable->office == 'CENRO Initao' ? 'selected' : '' }}>CENRO Initao</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="division">Division</label>
                                <select class="form-control" id="division" name="division">
                                    @foreach($divisions->chunk($divisions->count()/2) as $chunk)
                                        @foreach($chunk as $division)
                                            <option value="{{ $division->div_name }}" {{ $serviceable->division == $division->div_name ? 'selected' : '' }}>
                                                {{ $division->div_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="section">Section</label>
                                <select class="form-control" id="section" name="section">
                                    @foreach($sections->chunk($sections->count()/2) as $chunk)
                                        @foreach($chunk as $section)
                                            <option value="{{ $section->sec_name }}" {{ $serviceable->section == $section->sec_name ? 'selected' : '' }}>
                                                {{ $section->sec_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <select class="form-control" id="remarks" name="remarks" disabled>
                                <option value="purchased" {{ $serviceable->remarks == 'purchased' ? 'selected' : '' }}>Purchased</option>
                                <option value="central office fund" {{ $serviceable->remarks == 'central office fund' ? 'selected' : '' }}>Central Office Fund</option>
                                <option value="field office fund" {{ $serviceable->remarks == 'field office fund' ? 'selected' : '' }}>Field Office Fund</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fund">Fund Source</label>
                            <select class="form-control" id="fund" name="fund" disabled>
                                <option value="101" {{ $serviceable->fund == '101' ? 'selected' : '' }}>101</option>
                                <option value="102" {{ $serviceable->fund == '102' ? 'selected' : '' }}>102</option>
                                <option value="158" {{ $serviceable->fund == '158' ? 'selected' : '' }}>158</option>
                                <option value="401" {{ $serviceable->fund == '401' ? 'selected' : '' }}>401</option>
                                <option value="trust fund" {{ $serviceable->fund == 'trust fund' ? 'selected' : '' }}>Trust Fund</option>
                                <option value="not applicable" {{ $serviceable->fund == 'not applicable' ? 'selected' : '' }}>Not Applicable</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lifespan">Estimated Useful Life</label>
                            <select class="form-control" id="lifespan" name="lifespan" disabled>
                                <option value="2" {{ $serviceable->lifespan == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $serviceable->lifespan == '3' ? 'selected' : '' }}>3</option>
                                <option value="5" {{ $serviceable->lifespan == '5' ? 'selected' : '' }}>5</option>
                                <option value="7" {{ $serviceable->lifespan == '7' ? 'selected' : '' }}>7</option>
                                <option value="10" {{ $serviceable->lifespan == '10' ? 'selected' : '' }}>10</option>
                                <option value="15" {{ $serviceable->lifespan == '15' ? 'selected' : '' }}>15</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_renewed">Date Terminous</label>
                            <input type="text" class="form-control" id="date_renewed" name="date_renewed" value="{{ $serviceable->date_renewed }}" readonly>
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

                        <div class="form-group">
                            <label for="transfer_enduser">Transfer To</label>
                            <input type="text" class="form-control" id="transfer_enduser" name="transfer_enduser">
                        </div>

                        <div class="form-group">
                            <label for="transfer_position">Position</label>
                            <input type="text" class="form-control" id="transfer_position" name="transfer_position">
                        </div>

                        <div class="form-group">
                            <label for="transfer_condition">Condition</label>
                            <select class="form-control" id="transfer_condition" name="transfer_condition" >
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="Serviceable">Serviceable</option>
                                <option value="Brand New">Brand New</option>
                                <option value="Slightly Used">Slightly Used</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reason_transfer">Reason</label>
                            <textarea type="text" class="form-control" id="reason_transfer" name="reason_transfer"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="date_transfer">Date Transfer</label>
                            <input type="date" class="form-control" id="date_transfer" name="date_transfer">
                        </div>



                        <button type="submit" class="btn btn-primary">Transfer</button>
                        <a href="{{ route('gss.admin.list_serviceable') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Handle division change event to fetch sections
    $('#division').change(function() {
        var div_name = $(this).val();
        if (div_name) {
            $.ajax({
                url: '{{ url("get-sections") }}/' + div_name,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#section').empty();
                    $.each(data, function(key, value) {
                        $('#section').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        } else {
            $('#section').empty();
        }
    });

  /*  // Calculate and set date renewed
    $('#date_acquired, #lifespan').change(function() {
        var dateAcquired = $('#date_acquired').val();
        var lifespan = $('#lifespan').val();
        if (dateAcquired && lifespan) {
            var dateRenewed = moment(dateAcquired).add(lifespan, 'years').format('YYYY-MM-DD');
            $('#date_renewed').val(dateRenewed);
        } else {
            $('#date_renewed').val('');
        }
    });
  */  

    // Show success modal and handle PDF download
    @if(session('success'))
        $('#successModal').modal('show');
        $('#download-pdf').click(function() {
            let propertyNumber = '{{ session("propertyNumber") }}';
            window.location.href = '{{ url("/generate-pdf") }}/' + propertyNumber;
        });
        $('#ok-button').click(function() {
            window.location.href = '{{ route("gss.admin.list_serviceable") }}';
        });
    @endif
});
</script>
@endsection
