<!-- resources/views/finance/dashboard/equipment_near_end.blade.php -->
<div id="equipmentNearEndDateTable" class="collapse">
    <div class="card">
        <div class="card-body">
            <h5>Equipment Near Date End</h5>
            @if($equipmentItems->isEmpty())
                <p>No equipment items are due within the next 5 days.</p>
            @else
                <h5>Found {{ $equipmentItems->count() }} items:</h5>
                <table class="table table-bordered" id="equipmentTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Property Number</th>
                            <th>Office</th>
                            <th>Division</th>
                            <th>Date End</th>
                            <th>Date Acquired</th>
                            <th>Remarks</th>
                            <th>Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipmentItems as $item)
                            <tr>
                                <td>{{ $item->equipment_id }}</td>
                                <td>{{ $item->property_number }}</td>
                                <td>{{ $item->office }}</td>
                                <td>{{ $item->division }}</td>
                                <td>{{ $item->date_acquired }}</td>
                                <td>{{ $item->date_end }}</td>
                                <td><span style="background-color: yellow;">{{ $item->remarks }}</span></td>
                                <td>
                                    <button class="btn btn-warning">Unserviceable</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
