@extends('layouts.app')

@section('page', 'Create Announcement')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Announcements'];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endsection

@section('content')
    <div class="row">
        {{-- announcement form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Create New Announcement</h4>
                        <a href="{{ route('announcements.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('announcements.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-8 mb-4">
                                <img src="{{ asset('itrack/images/thumbnail.svg') }}" alt="" class="img-fluid border rounded" id="thumbnail-preview">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="thumbnail">Thumbnail (Optional)</label>
                                <input type="file" class="form-control @error('thumbnail') border-danger @enderror" id="thumbnail" name="thumbnail">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') border-danger @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="start_at">Start At <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_at') border-danger @enderror" id="start_at" name="start_at" value="{{ old('start_at') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="end_at">End At <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_at') border-danger @enderror" id="end_at" name="end_at" value="{{ old('end_at') }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description" rows="10" required>{{ old('description') }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check-size">
                                    <div class="form-check form-switch form-check-inline">
                                        <input type="checkbox" class="form-check-input check-size" id="status" name="status" value="1" role="switch"
                                            {{ old('status') == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label ms-3" for="status" style="transform: translateY(-2px);">Set as Draft</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- announcement form --}}
    </div>

    {{-- upload modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Thumbnail</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger">The image size must maintain a 16:10 aspect ratio to ensure visual consistency.</p>
                    <div class="overflow-hidden">
                        <img id="preview-photo" src="" alt="uploaded-photo" class="d-block" style="max-width: 100%;">
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
        $(document).ready(function () {
            // Datetime format
            $('input[type="datetime"]').on('change', function () {
                const rawValue = $(this).val();
                if (rawValue) {
                    const [date] = rawValue.split('T');
                    const [year, month, day] = date.split('-');

                    // Format as dd/mm/yyyy
                    const formattedDate = `${day}/${month}/${year}`;
                    $('#formatted-date').text(formattedDate);
                }
            });

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
            $('input[name="thumbnail"]').on('change', function () {
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

                    // Update the profile photo preview
                    const croppedImg = $('#thumbnail-preview');
                    const croppedInput = $('input[name="thumbnail"]');

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

            // // When the modal is hidden
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