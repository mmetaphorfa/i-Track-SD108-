@extends('layouts.app')

@section('page', 'Make Payment')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['My Financial'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- make payment --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Make A Payment</h4>
                        <a href="{{ route('parent.financial.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('parent.financial.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3">
                                <label class="form-label" for="invoice_id">Invoice Number</label>
                                <input type="text" class="form-control @error('invoice_id') border-danger @enderror" id="invoice_id" name="invoice_id"  
                                    value="{{ $invoiceId }}" readonly>
                            </div>
                            <div class="col-lg-8 col-md-7 mb-3">
                                <label class="form-label" for="full_name">Full Name</label>
                                <input type="text" class="form-control @error('full_name') border-danger @enderror" id="full_name" name="full_name"  
                                    value="{{ old('full_name', $user->full_name) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email_address">Email Address</label>
                                <input type="text" class="form-control @error('email_address') border-danger @enderror" id="email_address" name="email_address"  
                                    value="{{ old('email_address', $user->email) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone_number">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') border-danger @enderror" id="phone_number" name="phone_number"  
                                    value="{{ old('phone_number', $user->phone) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                                <select class="form-select @error('description') border-danger @enderror" id="description" name="description" required>
                                    <option value="">Please Select</option>
                                    @foreach (config('payments') as $key => $payment)
                                        <option value="{{ $key }}" {{ old('description') == $key ? 'selected' : '' }} data-amount="{{ $payment['amount'] }}">{{ $payment['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="amount">Amount (RM)</label>
                                <input type="text" class="form-control @error('amount') border-danger @enderror" id="amount" name="amount"  
                                    value="{{ old('amount', '0.00') }}" readonly>
                            </div>
                        </div>
                        <div class="text-end mt-1">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end make payment --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#description').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const amount = selectedOption.data('amount');

                // Display the amount
                $('#amount').val(amount ? amount.toFixed(2) : '0.00');
            });
        });
    </script>
@endsection