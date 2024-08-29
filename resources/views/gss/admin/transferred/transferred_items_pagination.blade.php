<!-- resources/views/gss/admin/transferred/transferred_items_pagination.blade.php -->
{{ $transferredItems->appends(request()->only('search', 'year'))->links('pagination::bootstrap-4') }}
