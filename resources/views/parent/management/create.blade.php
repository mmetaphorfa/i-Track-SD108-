@extends('layouts.app')

@section('page', 'Create Parent')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Users Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- add form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Create New Parent</h4>
                        <a href="{{ route('parent.management.index', session('role')) }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="alert alert-light-danger light txt-danger border-left-danger mb-4" role="alert">
                        <p class="mb-2"><strong>Important Notice!</strong></p>
                        <ul>
                            <li>- Fields marked with an asterisk (*) are mandatory.</li>
                            <li>- A temporary password will be sent to the user's email upon successful creation.</li>
                        </ul>
                    </div>
                    <form action="{{ route('parent.management.store', session('role')) }}" method="post" class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3">
                                <label class="form-label" for="nric_number">NRIC Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nric_number') border-danger @enderror" id="nric_number" name="nric_number" placeholder="Without (-) or spaces" 
                                    value="{{ old('nric_number') }}" minlength="12" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="col-lg-8 col-md-7 mb-3">
                                <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') border-danger @enderror" id="full_name" name="full_name" 
                                    value="{{ old('full_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email_address">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email_address') border-danger @enderror" id="email_address" name="email_address" 
                                    value="{{ old('email_address') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone_number') border-danger @enderror" id="phone_number" name="phone_number" placeholder="01XXXXXXXXX" 
                                    value="{{ old('phone_number') }}" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    pattern="^01\d{8,11}$" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <textarea class="form-control @error('address') border-danger @enderror" id="address" name="address" 
                                    rows="3">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control @error('city') border-danger @enderror" id="city" name="city" value="{{ old('city') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="postcode">Postcode</label>
                                <input type="text" class="form-control @error('postcode') border-danger @enderror" id="postcode" name="postcode" 
                                    value="{{ old('postcode') }}" minlength="5" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="state">State</label>
                                <select class="form-select @error('state') border-danger @enderror" id="state" name="state">
                                    <option value="">Please Select</option>
                                    @foreach (config('states') as $key => $state)
                                        <option value="{{ $key }}" {{ old('state') == $key ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end add form --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection