{{ $items->appends(request()->only('search', 'year', 'remarks'))->links('pagination::bootstrap-4') }}
