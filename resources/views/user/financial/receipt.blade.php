<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $payment['receipt_id'] }}</title>

    {{-- styles --}}
    <style>
        @font-face { 
            font-family: 'Open Sans'; 
            src: url("{{ asset('itrack/fonts/OpenSans-Regular.ttf') }}") format('truetype'); 
            font-weight: 400;
            font-style: normal;
        }
        @font-face { 
            font-family: 'Open Sans'; 
            src: url("{{ asset('itrack/fonts/OpenSans-Bold.ttf') }}") format('truetype'); 
            font-weight: 700;
            font-style: normal;
        }
        @page {
            margin: 5mm;
            size: A4 landscape;
        }

        /*-------------------- GENERAL --------------------*/
        body { 
            font-family: 'Open Sans', sans-serif;
        }
        table {
            width: 100%;
        }
        table tr td {
            line-height: 1.7;
        }

        /*-------------------- ALIGNMENTS --------------------*/
        .text-start {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }

        /*-------------------- COLORS --------------------*/
        .color-1 {
            color: #2D2D2D;
        }
        .color-2 {
            color: #56409f;
        }

        /*-------------------- BACKGROUNDS --------------------*/
        .bg-1 {
            background: #f9f9f9;
        }

        /*-------------------- HEADER --------------------*/
        #header tr td {
            padding: 10px;
            vertical-align: bottom;
        }

        /*-------------------- BODY --------------------*/
        .box {
            border-top: 1px solid #9D9D9D;
            border-bottom: 1px solid #9D9D9D;
            margin-top: 30px;
            padding: 20px;
        }
        #body .title {
            font-size: 18px;
        }
        #body .info {
            padding: 5px 15px 9px;
        }
    </style>
</head>
<body>
    {{-- header --}}
    <table id="header">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('itrack/images/sktpp-logo.png') }}" alt="sktpp" style="width: 100%;"></td>
            <td class="color-1" style="width: 20%;"><b>SK Taman Putra Perdana</b><br>Jalan Putra Perdana 3B, Taman Putra Perdana Selangor, Malaysia</td>
            <td></td>
            <td style="width: 30%;" class="text-end"><b>Receipt Transaction</b><br>{{ $payment['receipt_id'] }}<br>{{ date('F j, Y', strtotime($payment['paid_at'])) }}</td>
        </tr>
    </table>
    {{-- end header --}}

    {{-- body --}}
    <div class="box">
        <table id="body" class="color-1">
            <tr>
                <td><b class="color-2 title">PAYOR INFORMATION</b></td>
            </tr>
            <table class="info bg-1">
                <tr><td><b>Full Name:</b> {{ $payment['full_name'] }}</td></tr>
                <tr><td><b>Email Address:</b> {{ $payment['email'] }}</td></tr>
                <tr><td><b>Phone Number:</b> {{ $payment['phone'] }}</td></tr>
            </table>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td><b class="color-2 title">PAYMENT INFORMATION</b></td>
            </tr>
            <table class="info bg-1">
                <tr><td><b>Invoice ID:</b> {{ $payment['invoice_id'] }}</td></tr>
                <tr><td><b>Description:</b> {{ $payment['description'] }}</td></tr>
                <tr><td><b>Amount:</b> RM{{ $payment['amount'] }}</td></tr>
                <tr><td><b>Method:</b> {{ $payment['payment_method'] }}</td></tr>
                <tr><td><b>Date & Time:</b> {{ date('d/m/Y, h:i A', strtotime($payment['paid_at'])) }}</td></tr>
            </table>
        </table>
    </div>
    {{-- end body --}}

    {{-- footer --}}
    <div class="text-center color-1" style="margin-top: 20px;">
        <small>Please note: This receipt is auto-generated and does not require any manual intervention.</small>
    </div>
    {{-- end footer --}}
</body>
</html>