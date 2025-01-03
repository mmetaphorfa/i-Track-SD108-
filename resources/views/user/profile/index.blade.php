@extends('layouts.app')

@section('page', 'My Profile')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endsection

@section('content')
    <div class="row">
        {{-- update profile --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Account Details</h4>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('user.profile.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-12 text-center mb-1">
                                <div class="avatar mb-3">
                                    <img id="profile-photo" class="img-100 rounded-circle border" 
                                        src="{{ Storage::disk('public')->url('users/'.Auth::user()->image) }}" alt="profile-photo">
                                    <input type="file" name="photo" id="photo-input" accept="image/*" class="d-none">
                                </div>
                                <a id="uploadBtn" href="javascript:void(0);" class="btn btn-light d-inline-flex gap-2">
                                    <i class="icon-upload" style="transform: translateY(3px);"></i><span>Upload Photo</span>
                                </a>
                                <small class="d-block text-muted mt-2">Maximum: 5MB</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3">
                                <label class="form-label" for="nric_number">NRIC Number</label>
                                <input type="text" class="form-control" id="nric_number" name="nric_number" value="{{ $user->username }}" disabled>
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
                                <input type="text" class="form-control @error('phone_number') border-danger @enderror" id="phone_number" name="phone_number" placeholder="01XXXXXXXXX" 
                                    value="{{ old('phone_number', $user->phone) }}" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    pattern="^01\d{8,11}$" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <textarea class="form-control @error('address') border-danger @enderror" id="address" name="address" 
                                    rows="3">{{ old('address', $user->address->address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control @error('city') border-danger @enderror" id="city" name="city" value="{{ old('city', $user->address->city ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="postcode">Postcode</label>
                                <input type="text" class="form-control @error('postcode') border-danger @enderror" id="postcode" name="postcode" 
                                    value="{{ old('postcode', $user->address->postcode ?? '') }}" minlength="5" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end update profile --}}

        {{-- change password --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Account Security</h4>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('user.profile.change') }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-light-secondary txt-secondary border-left-secondary mb-4">
                                    <strong class="mb-2">Password Requirements:</strong>
                                    <ol class="m-0">
                                        <li>Must include at least one lowercase letter (a-z).</li>
                                        <li>Must include at least one uppercase letter (A-Z)</li>
                                        <li>Must include at least one number (0-9).</li>
                                        <li>Must be at least 8 characters long.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="current_password" name="current_password" value="{{ old('current_password') }}" required>
                                <div class="form-check checkbox-checked ms-1 mt-1">
                                    <input class="form-check-input pass" id="showPass1" type="checkbox">
                                    <label class="form-check-label" for="showPass1" style="font-size: 12px;">Show password</label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label" for="new_password">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" value="{{ old('new_password') }}" 
                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$" required>
                                <div class="form-check checkbox-checked ms-1 mt-1">
                                    <input class="form-check-input pass" id="showPass2" type="checkbox">
                                    <label class="form-check-label" for="showPass2" style="font-size: 12px;">Show password</label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" required>
                                <div class="form-check checkbox-checked ms-1 mt-1">
                                    <input class="form-check-input pass" id="showPass3" type="checkbox">
                                    <label class="form-check-label" for="showPass3" style="font-size: 12px;">Show password</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end change password --}}
    </div>

    {{-- upload modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Profile Photo</h5>
                </div>
                <div class="modal-body">
                    <div class="overflow-hidden">
                        <img id="preview-photo" src="{{ asset('png') }}" alt="uploaded-photo" class="d-block" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button id="cropBtn" class="btn btn-primary" type="button">Crop</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end upload modal --}}
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: "top",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                },
            });
            
            // Show password
            $('.form-check-input.pass').on('click', function() {
                const input = $(this).parent().prev();

                if ($(this).prop('checked')) {
                    input.prop('type', 'text');
                } else {
                    input.prop('type', 'password');
                }
            });

            // Trigger upload photo
            $('#uploadBtn').on('click', function () {
                $('#photo-input').click();
            });

            // File input change
            $('#photo-input').on('change', function () {
                const file = this.files[0];
                
                // Validate file type (check if it's an image)
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validImageTypes.includes(file.type)) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Please upload a valid photo (JPEG, JPG, PNG, or GIF).',
                    });
                    this.value = '';
                    return;
                }

                // Validate file size (check if it's less than 5MB)
                const maxSizeInMB = 5;
                if (file.size > maxSizeInMB * 1024 * 1024) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'File size must be less than 5MB.',
                    });
                    this.value = '';
                    return;
                }

                // Preview the image
                const reader = new FileReader();
                const previewImg = $('img#preview-photo');
                reader.onload = function (e) {
                    previewImg.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);

                // Trigger upload modal
                $('#uploadModal').modal('show');
            });

            // When the modal is about to be shown
            let cropper;
            $('#uploadModal').on('show.bs.modal', function () {
                const previewImg = $('img#preview-photo');

                // Destroy the previous cropper instance
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize Cropper.js
                setTimeout(() => {
                    cropper = new Cropper(previewImg[0], {
                        aspectRatio: 1,
                        viewMode: 2,
                    });
                }, 200);
            });

            // Crop the image when 'cropBtn' clicked
            $('#cropBtn').on('click', function () {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 250,
                        height: 250,
                    });

                    // Update the profile photo preview
                    const croppedImg = $('img#profile-photo');
                    const croppedInput = $('#photo-input');

                    canvas.toBlob((blob) => {
                        const url = URL.createObjectURL(blob);
                        croppedImg.attr('src', url);

                        // Create a new File object and set it to the input
                        const file = new File([blob], 'cropped-image.jpg', { type: 'image/jpeg' });

                        // Update the input with the cropped image
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        croppedInput[0].files = dataTransfer.files;

                        // Close the modal
                        $('#uploadModal').modal('hide');
                    }, 'image/jpeg');
                }
            });

            // When the modal is hidden
            $('#uploadModal').on('hidden.bs.modal', function () {
                $('img#preview-photo').attr('src', '');

                // Destroy the cropper instance
                if (cropper) {
                    cropper.destroy();
                }
            });
        });
    </script>
@endsection