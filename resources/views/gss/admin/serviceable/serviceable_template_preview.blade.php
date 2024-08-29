<!DOCTYPE html>
<html>
<head>
    <title>Serviceable Record PDF</title>
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
        .ics-table thead th, .ics-table thead td {
            text-align: center;
            padding: 5px;
            border: 1px solid;
            vertical-align: middle !important;
        }
        .ics-table-top td {
            border: none !important;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .appendix {
            text-align: right;
            width: 95%;
            font-style: italic;
        }
        .appendix h4 {
            font-size: 20px;
        }
        @page {
            margin:20px !important;
        }
        .ics-table ul li {
            list-style: none;
        }
                .ics-table ul {
            padding: 0 0 0 5px;
        }
        table.ics-table-top, table.ics-table.table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .ics-table tbody .top-details {
            border: 1px solid;
        }
        .ics-table tbody td {
            padding: 10px;
        }
        .top-details td:nth-child(5) li {
            margin-bottom: 15px;
        }
        .bot-details td:nth-child(2) .col h4 {
            font-weight: bold;
            margin: 0;
        }
        .bot-details td:nth-child(2) .col p {
            margin-top: 5px;
        }
        .pdf-container {
            width: 1500px;
            max-width: 100%;
            margin: auto;
        }
    </style>
</head>
<body>
@if ($record->property_type === 'ICS')
        <div class="container table-container pdf-container">
            <div class="row">
                <div class="appendix">
                    <h4>Appendix 59</h4>
                </div>
            </div> 
            <div class="header-title">
                <h4>INVENTORY CUSTODIAN SLIP</h4>
            </div>
            <div class="top-row row">
                <table class="ics-table-top table">
                    <tbody>
                        <tr>
                            <td>
                                <div class="col">
                                    <div class="label"><b>Entity Name:</b> DENR 10, {{ $record->office }}</div>
                                    <div class="label"><b>Fund Cluster:</b> {{ $record->fund }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="col">
                                    <div class="label"><b>ICS No.:</b> {{ $record->property_number }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive">
                <table class="ics-table table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" scope="col">Quantity</th>
                            <th rowspan="2" scope="col">Unit</th>
                            <th colspan="2">Amount</th>
                            <th rowspan="2" scope="col">Description</th>
                            <th rowspan="2" scope="col">Inventory Item No.</th>
                            <th rowspan="2" scope="col">Estimated Useful Life</th>
                        </tr>
                        <tr>
                            <td>Unit Cost</td>
                            <td>Total Cost</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="top-details">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <ul>
                                    <li><b>Category:</b> {{ $record->category }}</li>
                                    <li><b>Particular:</b> {{ $record->particular }}</li>
                                    <li><b>Description:</b> {{ $record->description }}</li>
                                    <li><b>Brand:</b> {{ $record->brand }}</li>
                                    <li><b>Model:</b> {{ $record->model }}</li>
                                    <li><b>Serial No.:</b> {{ $record->serial_no }}</li>
                                </ul>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="bot-details">
                            <td colspan="6">
                                <div class="col">
                                    <p>Received from:</p>
                                    <h4>{{ $record->uploaded_by }}</h4>
                                    <p><i>Signature Over Printed Name</i></p>
                                    <p><i>Position/Office</i></p>
                                    <h4>{{ $record->date_created}}</h4>
                                    <p><i>Date</i></p>
                                </div>
                            </td>
                            <td colspan="4">
                                <div class="col">
                                    <p>Received by:</p>
                                    <h4>MICHELLE MILLAMA</h4>
                                    <p><i>Signature Over Printed Name</i></p>
                                    <h4>GENERAL SERVICES SECTION</h4>
                                    <p><i>Position/Office</i></p>
                                    <h4>{{ $record->date_created}}</h4>
                                    <p><i>Date</i></p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif <!--END ICS-->

    @if ($record->property_type === 'PAR')
        <div class="container table-container pdf-container">
            <div class="row">
                <div class="appendix">
                    <h4>Appendix 71</h4>
                </div>
            </div> 
            <div class="header-title">
                <h4>PROPERTY ACKNOWLEDGMENT RECEIPT</h4>
            </div>
            <div class="top-row row">
                <table class="ics-table-top table">
                    <tbody>
                        <tr>
                            <td>
                                <div class="col">
                                    <div class="label"><b>Entity Name:</b> DENR 10, {{ $record->office }}</div>
                                    <div class="label"><b>Fund Cluster:</b> {{ $record->fund }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="col">
                                    <div class="label"><b>PAR No.:</b> {{ $record->property_number }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive">
                <table class="ics-table table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" scope="col">Quantity</th>
                            <th rowspan="2" scope="col">Unit</th>
                            <th rowspan="2" scope="col">Description</th>
                            <th rowspan="2" scope="col">Property Number</th>
                            <th rowspan="2" scope="col">Date Acquired</th>
                            <th rowspan="2" scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="top-details">
                            <td></td>
                            <td></td>
                            <td>
                                <ul>
                                    <li><b>Category:</b> {{ $record->category }}</li>
                                    <li><b>Particular:</b> {{ $record->particular }}</li>
                                    <li><b>Description:</b> {{ $record->description }}</li>
                                    <li><b>Brand:</b> {{ $record->brand }}</li>
                                    <li><b>Model:</b> {{ $record->model }}</li>
                                    <li><b>Serial No.:</b> {{ $record->serial_no }}</li>
                                </ul>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="bot-details">
                            <td colspan="6">
                                <div class="col">
                                    <p>Received by:</p>
                                    <p>Signature Over Printed Name of End User</p>
                                    <p>Position/Office</p>
                                    <p>Date</p>
                                </div>
                            </td>
                            <td colspan="4">
                                <div class="col">
                                    <p>Issued by:</p>
                                    <h4>MICHELLE MILLAMA</h4>
                                    <p><i>Signatue over Printed Name of Supply and/or Property Custodian</i></p>
                                    <h4>GENERAL SERVICES SECTION</h4>
                                    <p><i>Position/Office</i></p>
                                    <h4>{{ $record->date_created}}</h4>
                                    <p><i>Date</i></p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif <!--END PAR-->
</body>
</html>
