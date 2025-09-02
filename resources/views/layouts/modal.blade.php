<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .modal-content {
            border-radius: 8px;
            padding: 20px;
        }
        .modal-header {
            background: linear-gradient(to right, #28a745, #007bff);
            color: white;
            padding: 15px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            margin: 5px;
        }
        .modal-footer {
            text-align: right;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <div class="card">
        <div class="modal-header">
            <h5 class="modal-title">@yield('page-title')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="card-body">
            @yield('content')
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <a href="#" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
        </div>
    </div>
</div>

</body>
</html>
