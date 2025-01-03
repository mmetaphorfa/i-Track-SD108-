@extends('layouts.app')

@section('page', 'Financial Management')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- payment details --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Payment Information</h4>
                        <a href="{{ route('financial.index') }}" class="btn btn-danger btn-sm">Back</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="2"><b class="text-info">INVOICE DETAILS</b></td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">INVOICE ID</td>
                                    <td>{{ $payment->invoice_id }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">DESCRIPTION</td>
                                    <td>{{ $payment->description }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">AMOUNT</td>
                                    <td>RM {{ $payment->amount }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">CREATED AT</td>
                                    <td>{{ $payment->created_at->format('h:i A, j F Y') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b class="text-info">PAYMENT DETAILS</b></td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">BILL CODE</td>
                                    <td>{{ $payment->bill_code }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">TRANSACTION ID</td>
                                    <td>{{ $payment->receipt_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">TRANSACTION CHARGE</td>
                                    <td>{{ !empty($payment->trans_charge) ? 'RM ' . $payment->trans_charge : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">PAYMENT CHANNEL</td>
                                    <td>{{ $payment->payment_method ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">STATUS</td>
                                    <td class="text-capitalize">{{ $payment->status }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">PAID AT</td>
                                    <td>{{ !empty($payment->paid_at) ? date('h:i A, j F Y', strtotime($payment->paid_at)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">REFERENCE</td>
                                    <td>{{ $payment->reference ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b class="text-info">PARENT DETAILS</b></td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">ID NUMBER</td>
                                    <td>
                                        <a href="{{ route('parent.management.edit', ['role' => session('role'), 'id' => $payment->user_id]) }}" target="_blank">
                                            {{ $payment->user->username }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">NAME</td>
                                    <td>{{ $payment->status == 'paid' ? $payment->full_name : $payment->user->full_name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">EMAIL ADDRESS</td>
                                    <td>{{ $payment->status == 'paid' ? $payment->email : $payment->user->email }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">PHONE NUMBER</td>
                                    <td>{{ $payment->status == 'paid' ? $payment->phone : $payment->user->phone }}</td>
                                </tr>
                            </tbody>
                        </table>
                        @if ($payment->status == 'paid')
                        <div class="text-start mt-3">
                            <a href="{{ route('user.download.receipt', $payment->id) }}" class="btn btn-primary">Download Receipt</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- end payment details --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection