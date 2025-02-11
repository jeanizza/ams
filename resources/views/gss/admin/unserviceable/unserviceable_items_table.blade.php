@forelse ($unserviceableItems as $index => $item)
    <tr>

        <td>{{ ($unserviceableItems->currentPage() - 1) * $unserviceableItems->perPage() + $loop->iteration }}</td>
        <td>{{ $item->property_number ?? 'N/A' }}</td>
        <td>{{ $item->particular ?? 'N/A' }}</td>
        <td>{{ $item->description ?? 'N/A' }}</td>
        <td>{{ $item->end_user ?? 'N/A' }}</td>
        <td>{{ $item->division ?? 'N/A' }}</td>
        <td>{{ $item->date_acquired ?? 'N/A' }}</td>
        <td>{{ $item->lifespan ?? 'N/A' }}</td>
        <td>
            {{ $item->amount ? number_format(floatval($item->amount), 2) : '0.00' }}
        </td>
        <td>{{ $item->unserviceable_condition ?? 'Unknown' }}</td>
        <td>
            @if($item->unserviceable_image)
                <img src="{{ asset('storage/' . $item->unserviceable_image) }}" alt="Image" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                No Image
            @endif
        </td>
        <!-- Returned Date <td>{{ $item->date_returned ? \Carbon\Carbon::parse($item->date_returned)->format('F d, Y') : 'Not Returned' }}</td> -->
        <td>{{ $item->date_returned ?? 'No Data' }}</td>
        <td>
            <a href="{{ route('serviceables.unserviceable_form', $item->equipment_id) }}" class="btn btn-sm btn-primary">
                 Update
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" class="text-center text-muted">No unserviceable items found for the selected criteria.</td>
    </tr>
@endforelse
