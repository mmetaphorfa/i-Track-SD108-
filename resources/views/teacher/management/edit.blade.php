@extends('layouts.app')

@section('page', 'Edit Teacher')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Users Management'];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/tagify.css') }}">
@endsection

@section('content')
    <div class="row">
        {{-- user access control --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>User Access Control</h4>
                        <a href="{{ route('teacher.management.index', session('role')) }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <p id="message" class="text-muted">
                    @if ($user->status == 'active')
                        This account will be able to access and perform any actions within the portal. If revoked, this user will not be able to log in or use the portal.
                    @else
                        This account has been revoked. The user is not allowed to access the portal and cannot log in until access is granted again.
                    @endif
                    </p>
                    <form id="uac-form" action="{{ route('teacher.management.uac', ['id' => $user->id, 'role' => session('role')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="form-check-size">
                            <div class="form-check form-switch form-check-inline">
                                <input type="checkbox" class="form-check-input check-size" id="uac_checkbox" name="status" value="1" role="switch"
                                    {{ $user->status == 'active' ? 'checked' : '' }}>
                                <label class="form-check-label ms-3" for="uac_checkbox" style="transform: translateY(-2px);">
                                    {{ $user->status == 'active' ? 'Revoke Access' : 'Grant Access' }}
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end user access control --}}
        
        {{-- edit form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Administrator Information</h4>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('teacher.management.update', ['id' => $user->id, 'role' => session('role')]) }}" method="post">
                        @csrf
                        @method('put')
                        <span class="badge badge-warning p-2 mb-3">PERSONAL</span>
                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3">
                                <label class="form-label" for="nric_number">NRIC Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nric_number') border-danger @enderror" id="nric_number" name="nric_number" 
                                    placeholder="Without (-) or spaces" value="{{ old('nric_number', $user->username) }}" minlength="12" maxlength="12" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="col-lg-8 col-md-7 mb-3">
                                <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') border-danger @enderror" id="full_name" name="full_name" 
                                    value="{{ old('full_name', $user->full_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email_address">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email_address') border-danger @enderror" id="email_address" name="email_address" 
                                    value="{{ old('email_address', $user->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone_number') border-danger @enderror" id="phone_number" name="phone_number" 
                                    placeholder="01XXXXXXXXX" value="{{ old('phone_number', $user->phone) }}" maxlength="15" pattern="^01\d{8,11}$" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                        <hr>
                        <span class="badge badge-warning p-2 mb-3">ADDRESS</span>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <textarea class="form-control @error('address') border-danger @enderror" id="address" name="address" 
                                    rows="3">{{ old('address', $user->address->address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control @error('city') border-danger @enderror" id="city" name="city" 
                                    value="{{ old('city', $user->address->city ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="postcode">Postcode</label>
                                <input type="text" class="form-control @error('postcode') border-danger @enderror" id="postcode" name="postcode" 
                                    value="{{ old('postcode', $user->address->postcode ?? '') }}" minlength="5" maxlength="5" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="state">State</label>
                                <select class="form-select @error('state') border-danger @enderror" id="state" name="state">
                                    <option value="">Please Select</option>
                                    @foreach (config('states') as $key => $state)
                                        <option value="{{ $key }}" {{ old('state', $user->address->state ?? '') == $key ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <span class="badge badge-warning p-2 mb-3">ACCOUNT</span>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') border-danger @enderror" id="role" name="role" required>
                                    <option value="">Please Select</option>
                                    <option value="admin" {{ old('role', $user->admin_role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="teacher" {{ old('role', $user->admin_role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="is_parent">Is A Parent? <span class="text-danger">*</span></label>
                                <select class="form-select @error('is_parent') border-danger @enderror" id="is_parent" name="is_parent" required>
                                    <option value="">Please Select</option>
                                    <option value="yes" {{ old('is_parent') == 'yes' || $user->user_role == 'both' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ old('is_parent') == 'no' || $user->user_role != 'both' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="created_at">Created At</label>
                                <input type="text" class="form-control" id="created_at" value="{{ $user->created_at->format('d/m/Y, h:i A') }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="updated_at">Updated At</label>
                                <input type="text" class="form-control" id="updated_at" value="{{ $user->updated_at->format('d/m/Y, h:i A') }}" disabled>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end edit form --}}

        {{-- teaching subjects --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Teaching Subjects</h4>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="subjectInputs">
                                <label class="form-label" for="subjects">Subjects <span class="text-danger">*</span></label>
                                <input class="form-control" id="subjects" name="subjects" placeholder="Add new subject"
                                    value="{{ old('subjects', implode(', ', $userSubjects)) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button id="subjectBtn" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end teaching subjects --}}
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/select2/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/select2/tagify.polyfills.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Submit form on toggle changes
            $('#uac_checkbox').on('change', function() {
                $('#uac-form').submit();
            });
            
            // Button to convert to parent account
            $('#convertBtn').on('click', function() {
                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone.",
                    showCancelButton: true,
                    confirmButtonText: "Confirm",
                    denyButtonText: "Cancel",
                    customClass: {
                        confirmButton: 'btn btn-success',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create the form
                        var form = $('<form>', {
                            'action': '{{ route("teacher.management.convert", $user->id) }}',
                            'method': 'post'
                        });

                        // Append CSRF token & method override for PATCH
                        form.append($('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}));
                        form.append($('<input>', {type: 'hidden', name: '_method', value: 'patch'}));

                        // Append the form to the body and submit it
                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Subjects selection
            const input = $('#subjects')[0];
            const whitelist = @json($subjects);
            const tagify = new Tagify(input, {
                whitelist: whitelist,
                enforceWhitelist: true,
                dropdown: {
                    enabled: 1,
                    maxItems: 5,
                    position: 'tag',
                    closeOnSelect: false
                }
            });
            tagify.on('dropdown:focus', function(e) {
                const hoveredItem = e.detail.item;
                hoveredItem.classList.add('focused');
            });
            tagify.on('dropdown:blur', function(e) {
                const hoveredItem = e.detail.item;
                hoveredItem.classList.remove('focused');
            });
            
            // Submit form subjects
            $('#subjectBtn').on('click', function () {
                const input = $('#subjectInputs').find('input');
                const actionUrl = '{{ route("teacher.management.subjects", ["id" => ":id", "role" => ":role"]) }}';
                const finalUrl = actionUrl
                    .replace(':id', '{{ $user->id }}')
                    .replace(':role', '{{ session("role") }}');

                // Ensure there's a value in the input
                if (input.val() && input.val().trim() !== '') {
                    $('<form>', {
                        action: finalUrl,
                        method: 'POST'
                    })
                    .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                    .append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }))
                    .append($('<input>', { type: 'hidden', name: 'subjects', value: input.val() }))
                    .appendTo('body')
                    .submit();
                }
            });
        });
    </script>
@endsection