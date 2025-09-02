@extends('layouts.admin')

@section('title', 'Add Record')
@section('page-title', 'Add Record')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add Record</div>
                <div class="card-body"> 

                    <!-- @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                        <button class="btn btn-primary" id="download-excel">Download Excel</button>
                        <button class="btn btn-secondary" id="ok-button">Okay</button>
                    </div>
                    @endif --> 

                    @if(session('success'))
                        <div class="modal" id="successModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Success</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Record added successfully. Property Number: {{ session("propertyNumber") }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <!-- <button type="button" id="download-pdf" class="btn btn-primary">Download PDF</button> -->
                                        <a href="{{ route('generate.pdf', session('propertyNumber')) }}" class="btn btn-primary" id="download-pdf">Download PDF</a>
                                        <button type="button" id="ok-button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('gss.admin.store_add_record') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Property Type Dropdown -->
                    <div class="form-group">
                        <label for="property_type">Property Type</label>
                        <select class="form-control" id="property_type" name="property_type" required>
                            <option value="" disabled selected hidden>Select Property Type</option>
                            <option value="PAR">PAR</option>
                            <option value="ICS">ICS</option>
                        </select>
                    </div>

                    <!-- High Value / Low Value Dropdown -->
                    <div class="form-group" id="value_type_group" style="display: none;">
                        <label for="value_type">Value Type</label>
                        <select class="form-control" id="value_type" name="value_type">
                            <option value="" disabled selected hidden>Select Value Type</option>
                            <option value="High Value">High Value</option>
                            <option value="Low Value">Low Value</option>
                        </select>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select class="form-control" id="year" name="year" required>
                            <option value="" disabled selected hidden>Select Year</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Counter Display -->
                    <div class="form-group">
                        <label>Counter Result</label>
                        <div id="counter_result" class="badge badge-primary">Count: 0</div>
                    </div>
                    

                    

                    <!-- Property Number -->
                    <div class="form-group">
                        <label for="property_number">Property Number</label>
                        <input type="text" class="form-control" id="property_number" name="property_number" required>
                    </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="05-Agricultural & Forestry Equipment">05-Agricultural & Forestry Equipment</option>
                                <option value="05-Buildings">05-Buildings</option>
                                <option value="05-Communication Equipment">05-Communication Equipment</option>
                                <option value="05-Construction Equipment">05-Construction Equipment</option>
                                <option value="06-Furniture & Fixtures">06-Furniture & Fixtures</option>
                                <option value="05-Information & Communication Technology Equipment (ICT)">05-Information & Communication Technology Equipment</option>
                                <option value="05-Marine & Fishery Equipment">05-Marine & Fishery Equipment</option>
                                <option value="05-Office Equipment">05-Office Equipment</option>
                                <option value="05-Other Land Improvements">05-Other Land Improvements</option>
                                <option value="05-Sports Equipment">05-Sports Equipment</option>
                                <option value="05-Technical & Scientific Equipment">05-Technical & Scientific Equipment</option>
                                <option value="06-Transportation Equipment">06-Transportation Equipment</option>
                                <option value="05-Watercrafts Equipment">05-Watercrafts Equipment</option>
                            </select>
                        </div>

                        <!-- Particular -->
                        <div class="form-group">
                            <label for="particular">Particular</label>
                            <input type="text" class="form-control" id="particular" name="particular" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <!-- Brand -->
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" required>
                        </div>

                        <!-- Model -->
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>

                        <!-- Serial Number -->
                        <div class="form-group">
                            <label for="serial_no">Serial No.</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" required>
                        </div>

                        <!-- Amount -->
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>

                        <!-- Purchase Order Number -->
                        <div class="form-group">
                            <label for="po_number">Purchase Order No.</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" required>
                        </div>

                        <!-- Date Acquired -->
                        <div class="form-group">
                            <label for="date_acquired">Date Acquired</label>
                            <input type="date" class="form-control" id="date_acquired" name="date_acquired" required>
                        </div>

                        <!-- End User -->
                        <div class="form-group">
                            <label for="end_user">End User</label>
                            <input type="text" class="form-control" id="end_user" name="end_user" required>
                        </div>

                        <!-- Position -->
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>

                         <!-- Office -->
                         <div class="form-group">
                            <label for="office">Office</label>
                            <select class="form-control" id="office" name="office" required>
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

                        <!-- Division -->
                        <div class="form-group">
                            <label for="division">Division</label>
                            <select class="form-control" id="division" name="division" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->div_name }}" data-div-id="{{ $division->div_id }}">{{ $division->div_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="division_id" name="division_id" value="">
                        </div>

                        <!-- Section -->
                        <div class="form-group">
                            <label for="section">Section</label>
                            <select class="form-control" id="section" name="section" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <!-- Sections will be dynamically loaded based on the selected division -->
                            </select>
                        </div>

                        <!-- Actual User -->
                        <div class="form-group">
                            <label for="actual_user">Actual User</label>
                            <input type="text" class="form-control" id="actual_user" name="actual_user" required>
                        </div>

                        <!-- Actual User Position -->
                        <div class="form-group">
                            <label for="position_actual_user">Actual User Position</label>
                            <input type="text" class="form-control" id="position_actual_user" name="position_actual_user" required>
                        </div>

                        <!-- Remarks -->
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <select class="form-control" id="remarks" name="remarks" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="purchased">Purchased</option>
                                <option value="central office fund">Central Office Fund</option>
                                <option value="field office fund">Field Office Fund</option>
                            </select>
                        </div>

                        <!-- Fund Source -->
                        <div class="form-group">
                            <label for="fund">Fund Source</label>
                            <select class="form-control" id="fund" name="fund" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="101">101</option>
                                <option value="102">102</option>
                                <option value="158">158</option>
                                <option value="401">401</option>
                                <option value="trust fund">Trust Fund</option>
                                <option value="not applicable">Not Applicable</option>
                            </select>
                        </div>

                        <!-- Lifespan -->
                        <div class="form-group">
                            <label for="lifespan">Estimated Useful Life</label>
                            <select class="form-control" id="lifespan" name="lifespan" required>
                                <option value="" disabled selected hidden>Choose an Option</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="7">7</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                            </select>
                        </div>

                        <!-- Date End -->
                        <div class="form-group">
                            <label for="date_end">Date Terminous</label>
                            <input type="text" class="form-control" id="date_end" name="date_end" readonly>
                        </div>

                        <!-- Uploaded By -->
                        <div class="form-group">
                            <label for="uploaded_by">Uploaded By</label>
                            <input type="text" class="form-control" id="uploaded_by" name="uploaded_by" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <!-- Upload Image -->
                        <div class="form-group">
                            <label for="upload_image">Upload Image</label>
                            <input type="file" class="form-control-file" id="upload_image" name="upload_image" accept="image/png, image/jpeg" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>


            <!-- Error Modal -->
            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="errorModalLabel">Validation Error</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Error message will be injected dynamically -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
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
    $('#division').change(function() {
        const divName = $(this).val();
        if (divName) {
            const url = '{{ route("fetch.sections", ":division") }}'.replace(':division', divName);

            $.get(url, function(data) {
                $('#section').empty().append('<option>Select Section</option>');
                data.forEach(section => {
                    $('#section').append(`<option value="${section}">${section}</option>`);
                });
            }).fail(() => alert('Failed to fetch sections.'));
        } else {
            $('#section').empty();
        }
    });
});

    //date_end
    $('#date_acquired, #lifespan').change(function() {
        var dateAcquired = $('#date_acquired').val();
        var lifespan = $('#lifespan').val();
        if (dateAcquired && lifespan) {
            var dateEnd = moment(dateAcquired).add(lifespan, 'years').format('YYYY-MM-DD');
            $('#date_end').val(dateEnd);
        } else {
            $('#date_end').val('');
        }
    });

    $(document).ready(function () {
    // Show or hide Value Type dropdown based on Property Type selection
    $('#property_type').change(function () {
        const propertyType = $(this).val();
        if (propertyType === 'ICS') {
            $('#value_type_group').show();
        } else {
            $('#value_type_group').hide();
            $('#value_type').val(''); // Reset value type if not ICS
        }
        updateCounter(); // Update the counter when the property type changes
    });

    // Update Counter Display when Value Type or Year dropdown changes
    $('#value_type, #year').change(function () {
        updateCounter();
    });

    // Function to update the counter
    function updateCounter() {
        const propertyType = $('#property_type').val(); // Get selected property type
        const valueType = $('#value_type').val(); // Get selected value type
        const year = $('#year').val(); // Get selected year

        // Check if property type and year are selected
        if (propertyType && year) {
            $.ajax({
                url: '{{ route("fetch.counters") }}', // Route to fetch counter
                type: 'GET',
                data: { property_type: propertyType, value_type: valueType, year: year }, // Data to send
                success: function (response) {
                    if (response && response.count !== undefined) {
                        $('#counter_result').text(`Count: ${response.count}`);
                    } else {
                        $('#counter_result').text('Count: 0');
                    }
                },
                error: function () {
                    $('#counter_result').text('Count: 0'); // Default on error
                }
            });
        } else {
            $('#counter_result').text('Count: 0'); // Default if inputs are invalid
        }
    }
});

    // Enhanced Property Number Generation
function generatePropertyNumber(parCount, lvCount, hvCount) {
    const office = $('#office').val() || '';
    const propertyType = $('#property_type').val() || '';
    const category = $('#category').val() || '';
    const amount = parseFloat($('#amount').val()) || 0;
    const divisionId = $('#division_id').val() || '';
    const currentDate = new Date();
    const startDate = new Date('2025-01-22');
    const year = startDate.getFullYear();

    // Determine LV or HV
    let lvhv = 'LV';
    if (amount >= 5000 && amount <= 49999.99) {
        lvhv = 'HV';
    }

    // Extract category code
    const categoryCode = category ? category.substring(0, 2) : '';

    // Determine General Ledger Account
    const generalLedgerAccount = determineGeneralLedgerAccount(category);

    // Determine office code
    const officeCode = determineOfficeCode(office);

    // Calculate series number based on the counts
    let seriesNumber = '0000';
    if (propertyType === 'PAR') {
        seriesNumber = String(parCount + 1).padStart(4, '0');
    } else if (lvhv === 'LV') {
        seriesNumber = String(lvCount + 1).padStart(4, '0');
    } else {
        seriesNumber = String(hvCount + 1).padStart(4, '0');
    }

    // Construct the property number
    let propertyNumber = '';
    if (propertyType === 'PAR') {
        propertyNumber = `${officeCode}-${year}-${categoryCode}-${generalLedgerAccount}-${seriesNumber}-${divisionId}`.trim();
    } else if (propertyType === 'ICS') {
        propertyNumber = `${officeCode}-${lvhv}-${year}-${categoryCode}-${generalLedgerAccount}-${seriesNumber}-${divisionId}`.trim();
    }

    // Debugging logs
    console.log('Generated Property Number:', propertyNumber);
    console.log('Amount:', amount, '| LV/HV:', lvhv);

    // Update the property number field
    $('#property_number').val(propertyNumber);
}

// General Ledger Account Determination
function determineGeneralLedgerAccount(category) {
    if (category.includes('Buildings')) return '01';
    if (category.includes('Hostel & Dorm')) return '06';
    if (category.includes('Office Equipment')) return '02';
    if (category.includes('ICT')) return '03';
    if (category.includes('Agricultural & Forestry')) return '04';
    if (category.includes('Marine & Fishery')) return '05';
    if (category.includes('Communication')) return '07';
    if (category.includes('Technical & Scientific')) return '14';
    if (category.startsWith('06')) return '99';
    return '00';
}

// Office Code Determination
function determineOfficeCode(office) {
    const officeCodes = {
        'Regional Office': 'RX',
        'PENRO Camiguin': 'PC',
        'PENRO Bukidnon': 'PB',
        'CENRO Don Carlos': 'CD',
        'CENRO Manolo Fortich': 'CM',
        'CENRO Talakag': 'CT',
        'CENRO Valencia': 'CV',
        'PENRO Lanao del Norte': 'PL',
        'CENRO Iligan': 'CIG',
        'CENRO Kolambugan': 'CK',
        'PENRO Misamis Occidental': 'PMC',
        'CENRO Oroquieta': 'COR',
        'CENRO Ozamiz': 'COZ',
        'PENRO Misamis Oriental': 'PMR',
        'CENRO Gingoog': 'CG',
        'CENRO Initao': 'CIN',
    };
    return officeCodes[office] || 'XX';
}

// Event Listeners
$('#property_type, #category, #amount, #division, #office').change(function () {
    generatePropertyNumber(
        {{ $parCount ?? 0 }},
        {{ $lvCount ?? 0 }},
        {{ $hvCount ?? 0 }}
    );
});

$('#division').change(function () {
    const selectedOption = $(this).find('option:selected');
    const divisionId = selectedOption.data('div-id');
    $('#division_id').val(divisionId);
    generatePropertyNumber(
        {{ $parCount ?? 0 }},
        {{ $lvCount ?? 0 }},
        {{ $hvCount ?? 0 }}
    );
});

$(document).ready(function() {
    @if(session('success'))
        $('#successModal').modal('show'); // Show the modal

        $('#download-pdf').click(function() {
            let propertyNumber = '{{ session("propertyNumber") }}';
            console.log("Property number in session: " + propertyNumber + " (length: " + propertyNumber.length + ")");
            let encodedPropertyNumber = encodeURIComponent(propertyNumber);
            window.location.href = '{{ url("/generate-pdf") }}/' + encodedPropertyNumber;
        });

        $('#ok-button').click(function() {
            window.location.href = '{{ route("gss.admin.add_record") }}';
        });
    @endif
});
</script>
@endsection 