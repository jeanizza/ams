<!-- resources/views/gss/admin/transferred/transferred_items_table.blade.php -->
@forelse ($transferredItems as $item)
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
        <td colspan="7">No transferred items found.</td>
    </tr>
@endforelse
