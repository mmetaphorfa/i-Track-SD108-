@extends('layouts.app')

@section('page', 'My Financial')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- payment list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Payment List</h4>
                        <a href="{{ route('parent.financial.create') }}" class="btn btn-primary btn-sm">Make Payment</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="payment-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Download Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $key => $payment)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $payment->invoice_id }}</td>
                                    <td>{{ $payment->description }}</td>
                                    <td>RM {{ $payment->amount }}</td>
                                    <td>{{ date('d/m/Y, h:i A', strtotime($payment->paid_at)) }}</td>
                                    <td class="text-center">
                                        <ul class="action">
                                        @if ($payment->status == 'paid')
                                            <li class="mx-auto"><a href="{{ route('user.download.receipt', $payment->id) }}" class="btn btn-sm" style="background: linear-gradient(to right, #6a11cb, #2575fc); color: white; padding: 5px 12px; font-size: 14px;">e-Receipt</a>
                                            </li>
                                        @else
                                            <li class="mx-auto">
                                                <a href="{{ route('parent.financial.pay', $payment->id) }}" class="btn btn-light" style="padding: 2px 10px;">
                                                    Pay
                                                </a>
                                            </li>
                                        @endif
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end payment list --}}
    </div>
@endsection

@section('script')
    <script>
        // Payment datatable
        $(document).ready(function () {
            $("#payment-table").DataTable({
                pageLength: 25,
                lengthMenu: [25, 50, 100, 200],
            });
        });
    </script>
@endsection
