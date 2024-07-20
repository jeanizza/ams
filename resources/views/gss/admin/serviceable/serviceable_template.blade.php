<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Property Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .content .section {
            margin-bottom: 10px;
        }
        .content .section .label {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            font-size: 72px;
            color: #000;
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!--if ICS is selected-->
    @if ($record->property_type === 'ICS')
    <div class="container">
        <!-- Watermark -->
        <div class="watermark">Hey Soul Sister</div>

        <!-- Header -->
        <div class="header">
            <h1>Your Company Name</h1>
        </div>

        <!-- Content -->
        <div class="content">

            <div class="section">
                <div class="label">Entity Name: DENR 10, {{ $record->office }}</div>
            </div>

            <div class="section">
                <div class="label">Property Number:</div>
                <div>{{ $record->property_number }}</div>
            </div>
            <div class="section">
                <div class="label">Date Acquired:</div>
                <div>{{ $record->date_acquired }}</div>
            </div>
            <div class="section">
                <div class="label">Amount:</div>
                <div>{{ $record->amount }}</div>
            </div>
            <div class="section">
                <div class="label">End User:</div>
                <div>{{ $record->end_user }}</div>
            </div>
            <div class="section">
                <div class="label">Position:</div>
                <div>{{ $record->position }}</div>
            </div>
                <div class="section">
                    <div class="label">Authorized By:</div>
                    <div>Michelle S. Millana<br>Administrative Officer IV</div>
                </div>
                <div class="section">
                    <div class="label">Description:</div>
                    <div>{{ $record->description }}<br>{{ $record->brand }}<br>{{ $record->model }}<br>{{ $record->serial_no }}</div>
                </div>
                <div class="section">
                    <div class="label">Lifespan:</div>
                    <div>{{ $record->lifespan }}</div>
                </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Additional Information</p>
            <p>Contact Us: [Your Contact Info]</p>
        </div>
    </div>
    @endif <!--for ICS-->
    
</body>
</html>
