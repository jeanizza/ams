@foreach($serviceables as $index => $item)
<tr>
    <td>{{ $serviceables->firstItem() + $index }}</td>
    <td>{{ $item->equipment_id }}</td>
    <td>{{ $item->property_number }}</td>
    <td>{{ $item->particular }}</td>
    <td>{!! nl2br(e($item->description)) !!}</td>
    <td>{{ $item->end_user }}</td>
    <td>{{ $item->office }}</td>
    <td>{{ $item->division }}</td>
    <td>{{ $item->section }}</td>
    <td>{{ number_format((float)$item->amount, 2, '.', ',') }}</td>
    <td>
        @if($item->upload_image)
            <img src="{{ asset('storage/' . $item->upload_image) }}" 
                alt="Image" 
                class="img-thumbnail preview-image" 
                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" 
                data-bs-toggle="modal" 
                data-bs-target="#imageModal"
                data-image="{{ asset('storage/' . $item->upload_image) }}">
        @else
            <span class="text-muted">No Image</span>
        @endif
    </td>
    <td>
        <div class="btn-group-vertical">
            <a href="{{ route('requests.request_update', $item->equipment_id) }}" class="btn btn-success btn-sm">Update</a>
            <a href="{{ route('requests.request_transfer', $item->equipment_id) }}" class="btn btn-warning btn-sm">Transfer</a>
            <a href="{{ route('requests.request_unserviceable',$item->equipment_id) }}" class="btn btn-danger btn-sm">Unserviceable</a>
        </div>
    </td>
</tr>
@endforeach
