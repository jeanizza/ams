       @forelse ($serviceables as $serviceable)
            <tr>
                <td>{{ $serviceable->equipment_id }}</td>
                <td>{{ $serviceable->property_number }}</td>
                <td>{{ $serviceable->particular }}</td>
                <td>{{ $serviceable->description }}</td>
                <td>{{ $serviceable->office }}</td>
                <td>{{ $serviceable->end_user }}</td>
                <td>{{ $serviceable->division }}</td>
                <td>{{ $serviceable->section }}</td>
                <td>@if($serviceable->amount)
                        {{ number_format((float)$serviceable->amount, 2) }}
                    @endif
                </td>
                <td>
                    @if($serviceable->upload_image)
                        <img src="{{ asset('storage/' . $serviceable->upload_image) }}" alt="Image" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        No image
                    @endif
                </td>
                <td>
                    <div>
                        <a href="{{ route('serviceables.update_serviceable', $serviceable->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
                    </div>
                    <div>
                        <a href="{{ route('serviceables.transfer_serviceable', $serviceable->equipment_id) }}" class="btn btn-warning btn-sm" style="background-color: #ffcc00; border-color: #ffcc00;">Transfer</a>
                    </div>
                    <div>
                        <a href="{{ route('serviceables.unserviceable_form', $serviceable->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No serviceable items found.</td>
            </tr>
        @endforelse

