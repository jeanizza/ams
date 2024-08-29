<!-- resources/views/gss/admin/unserviceable/unserviceable_items_pagination.blade.php -->
{{ $unserviceableItems->appends(request()->only('search', 'year'))->links('pagination::bootstrap-4') }}