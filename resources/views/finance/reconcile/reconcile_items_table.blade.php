<!-- resources/views/gss/admin/unserviceable/unserviceable_items_table.blade.php -->
@forelse ($unserviceableItems as $item)
    <tr>
        <td>{{ $item->equipment_id }}</td>
        <td>{{ $item->property_number }}</td>
        <td>{{ $item->particular }}</td>
        <td>{{ $item->description }}</td>
        <td>{{ $item->office }}</td>
        <td>{{ $item->division }}</td>
        <td>{{ number_format(floatval($item->amount), 2) }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7">No unserviceable items found.</td>
    </tr>
@endforelse
