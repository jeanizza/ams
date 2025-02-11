<!-- resources/views/gss/admin/transferred/transferred_items_table.blade.php -->
@forelse ($transferredItems as $item)
    <tr>
        <td>{{ ($transferredItems->currentPage() - 1) * $transferredItems->perPage() + $loop->iteration }}</td>
        <td>{{ $item->property_number }}</td>
        <td>{{ $item->particular }}</td>
        <td>{{ $item->description }}</td>
        <td>{{ $item->end_user }}</td>
        <td>{{ $item->office }}</td>
        <td>{{ $item->transfer_office }}</td>
        <td>{{ $item->transfer_enduser }}</td>
        <td>{{ number_format(floatval($item->amount), 2) }}</td>
        <td>
            @if($item->upload_image)
                <img src="{{ asset('storage/' . $item->upload_image) }}" alt="Image" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                No Image
            @endif
        </td>
        <td>
            <a href="{{ route('serviceables.transfer_serviceable', $item->equipment_id) }}" class="btn btn-primary btn-sm">
                Update
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11">No transferred items found.</td>
    </tr>
@endforelse
