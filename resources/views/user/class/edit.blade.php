@extends('layouts.app')

@section('page', 'Edit Classroom')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Classroom Management'];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endsection

@section('content')
    <div class="row">
        {{-- classroom details --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Classroom Details</h4>
                        <a href="{{ route('classes.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('classes.update', $classroom->id) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="class_code">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_code') border-danger @enderror" id="class_code" name="class_code" 
                                    value="{{ old('class_code', $classroom->code) }}" maxlength="5" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="class_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_name') border-danger @enderror" id="class_name" name="class_name" 
                                    value="{{ old('class_name', $classroom->name) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="grade_level">Grade Level <span class="text-danger">*</span></label>
                                <select name="grade_level" id="grade_level" class="form-select" required>
                                    <option value="">Please Select</option>
                                    <option value="1" {{ old('grade_level', $classroom->level) == 1 ? 'selected' : '' }}>Primary 1</option>
                                    <option value="2" {{ old('grade_level', $classroom->level) == 2 ? 'selected' : '' }}>Primary 2</option>
                                    <option value="3" {{ old('grade_level', $classroom->level) == 3 ? 'selected' : '' }}>Primary 3</option>
                                    <option value="4" {{ old('grade_level', $classroom->level) == 4 ? 'selected' : '' }}>Primary 4</option>
                                    <option value="5" {{ old('grade_level', $classroom->level) == 5 ? 'selected' : '' }}>Primary 5</option>
                                    <option value="6" {{ old('grade_level', $classroom->level) == 6 ? 'selected' : '' }}>Primary 6</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="class_limit">Limit (Students in Class) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_limit') border-danger @enderror" id="class_limit" name="class_limit" 
                                    value="{{ old('class_limit', $classroom->max_limit) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                @php
                                    if (count($classroom->teachers) > 0) {
                                        $activeTeacher = $classroom->teachers->first(function ($teacher) {
                                            return $teacher->pivot->status === 'active';
                                        });
                                        $active = $activeTeacher->id ?? null;
                                    } else {
                                        $active = null;
                                    }
                                @endphp
                                <label class="form-label" for="teacher">Assigned Teacher</label>
                                <select name="teacher" id="teacher" class="form-select">
                                    <option value="">Please Select</option>
                                    @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher', $active ?? '') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="created_by">Created By</label>
                                <input type="text" class="form-control" id="created_by" name="created_by" value="{{ $classroom->creator->full_name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="created_at">Created At</label>
                                <input type="text" class="form-control" id="created_at" name="created_at" value="{{ $classroom->created_at->format('d/m/Y, h:i A') }}" disabled>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end subject details --}}

        {{-- upload timetable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Class Timetable</h4>
                        <div>
                            <label for="photo-input" class="btn btn-secondary mb-0">Upload</label>
                            <input type="file" id="photo-input" accept="image/*" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    @if ($timetable)
                        <img src="{{ Storage::disk('public')->url('timetables/'.$timetable->file_name) }}" alt="timetable-{{ $classroom->name }}" class="img-fluid border rounded">
                    @else
                        <div class="text-center">
                            <span class="text-muted">No data available.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- end upload timetable --}}
    </div>

    {{-- upload modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop to Upload Timetable</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger">The image size must maintain a 16:10 aspect ratio to ensure visual consistency.</p>
                    <div class="overflow-hidden">
                        <img id="preview-photo" src="" alt="uploaded-photo" class="d-block" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button id="cropBtn" class="btn btn-primary" type="button">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end upload modal --}}
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        $(document).ready(function () {
            // Timetable upload
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

            // File input change
            $('#photo-input').on('change', function () {
                const file = this.files[0];
                
                // Validate file type (check if it's an image)
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validImageTypes.includes(file.type)) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Please upload a valid photo (JPEG, JPG, or PNG).',
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
                        aspectRatio: 16/10,
                        viewMode: 2,
                    });
                }, 200);
            });

            // Crop the image when 'cropBtn' clicked
            $('#cropBtn').on('click', function () {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 1920,
                        height: 1200,
                    });

                    canvas.toBlob((blob) => {
                        // Create the cropped image file
                        const file = new File([blob], 'cropped-image.jpg', { type: 'image/jpeg' });

                        // Create a dynamic form
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("user.timetable.upload", $classroom->id) }}',
                            enctype: 'multipart/form-data',
                        });

                        // Add CSRF token
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}',
                        }));

                        // Add the cropped image input
                        const imageInput = $('<input>', {
                            type: 'file',
                            name: 'image',
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        imageInput[0].files = dataTransfer.files;

                        form.append(imageInput);

                        // Append the form to the body and submit
                        $('body').append(form);
                        form.submit();

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