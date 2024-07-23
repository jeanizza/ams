       @forelse ($serviceables as $serviceable)
            <tr>
                <td>{{ $serviceable->id }}</td>
                <td>{{ $serviceable->property_number }}</td>
                <td>{{ $serviceable->description }}</td>
                <td>{{ $serviceable->office }}</td>
                <td>
                    <a href="{{ route('serviceables.update_serviceable', $serviceable->id) }}" class="btn btn-success btn-sm">Update</a>
                    <a href="{{ route('serviceables.transfer_serviceable', $serviceable->id) }}" class="btn btn-warning btn-sm">Transfer</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No serviceable items found.</td>
            </tr>
        @endforelse

