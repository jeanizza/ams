       @forelse ($serviceables as $serviceable)
            <tr>
                <td>{{ $serviceable->id }}</td>
                <td>{{ $serviceable->property_number }}</td>
                <td>{{ $serviceable->particular }}</td>
                <td>{{ $serviceable->description }}</td>
                <td>{{ $serviceable->office }}</td>
                <td>{{ $serviceable->end_user }}</td>
                <td>{{ $serviceable->division }}</td>
                <td>
                    @if($serviceable->upload_image)
                        <img src="{{ asset('storage/' . $serviceable->upload_image) }}" alt="Image" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        No image
                    @endif
                </td>
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

