@extends('layouts.app')

@section('page', 'Create Student')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Users Management'];
    @endphp
@endsection

@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        {{-- add form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Create New Student</h4>
                        <a href="{{ route('student.management.index', session('role')) }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('student.management.store', session('role')) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label" for="parent">Parent Name <span class="text-danger">*</span></label>
                                <select class="form-select select-2 @error('parent') border-danger @enderror" id="parent" name="parent" required>
                                    <option value="">Please Select</option>
                                    @foreach ($parents as $key => $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent') == $parent->id ? 'selected' : '' }}>{{ $parent->username . ' - ' . $parent->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-5 mb-3">
                                <label class="form-label" for="mykid_number">MyKID Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mykid_number') border-danger @enderror" id="mykid_number" name="mykid_number" placeholder="Without (-) or spaces" 
                                    value="{{ old('mykid_number') }}" minlength="12" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
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
                                <label class="form-label" for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') border-danger @enderror" id="date_of_birth" name="date_of_birth" placeholder="01XXXXXXXXX" 
                                    value="{{ old('date_of_birth') }}" required>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') border-danger @enderror" id="gender" name="gender" required>
                                    <option value="">Please Select</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label" for="race">Race <span class="text-danger">*</span></label>
                                <select class="form-select @error('race') border-danger @enderror" id="race" name="race" required>
                                    <option value="">Please Select</option>
                                    @foreach (config('student_data.race') as $key => $race)
                                        <option value="{{ $key }}" {{ old('race') == $key ? 'selected' : '' }}>{{ $race }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="religion">Religion <span class="text-danger">*</span></label>
                                <select class="form-select @error('religion') border-danger @enderror" id="religion" name="religion" required>
                                    <option value="">Please Select</option>
                                    @foreach (config('student_data.religion') as $key => $religion)
                                        <option value="{{ $key }}" {{ old('religion') == $key ? 'selected' : '' }}>{{ $religion }}</option>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select-2').select2({
                placeholder: 'Please select',
            });
        });
    </script>
@endsection