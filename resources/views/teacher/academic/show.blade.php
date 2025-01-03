@extends('layouts.app')

@section('page', 'Academics Records')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Academics'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- add form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>New Academics Record</h4>
                        <a href="{{ route('academics.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('academics.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="subject">Subject</label>
                                <input type="text" class="form-control" value="{{ $classroom->subject->name }}" disabled>
                                <input type="hidden" class="form-control" name="subject_id" value="{{ $classroom->subject->id }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="class">Class (Grade)</label>
                                <input type="text" class="form-control" value="{{ $classroom->classroom->name . ' (' . $classroom->classroom->level . ')' }}" disabled>
                                <input type="hidden" class="form-control" name="class_id" value="{{ $classroom->classroom->id }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="examination">Examination <span class="text-danger">*</span></label>
                                <select class="form-select @error('examination') border-danger @enderror" id="examination" name="examination" required>
                                    <option value="">Please Select</option>
                                    @foreach (config('grades.examinations') as $key => $exam)
                                        <option value="{{ $key }}" {{ old('examination') == $key ? 'selected' : '' }}>{{ $exam }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="year">Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('year') border-danger @enderror" id="year" name="year"
                                    value="{{ old('year') }}" minlength="4" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end add form --}}

        {{-- record list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Record List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="academics-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Examination</th>
                                    <th>Year</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Edit Grades</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ config('grades.examinations.' . $record->examination) }}</td>
                                        <td>{{ $record->year }}</td>
                                        <td>{{ $record->created_at->format('d/m/Y, h:i A') }}</td>
                                        <td>{{ $record->updated_at->format('d/m/Y, h:i A') }}</td>
                                        <td class="text-center">
                                            <ul class="action justify-content-center gap-3">
                                                <li><a href="{{ route('academics.edit', $record->id) }}"><i class="icon-pencil-alt txt-dark"></i></a></li>
                                                @if (App\Models\Grade::where('record_id', $record->id)->count() == 0)
                                                <li><a href="javascript:void(0);" class="btn-delete" data-id="{{ $record->id }}"><i class="icon-trash txt-danger"></i></a></li>
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
        {{-- end record list --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Academics datatable
            $("#academics-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });

            // Delete button
            $('.btn-delete').click(function () {
                const record_id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure you want to delete this record?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a dynamic form
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("academics.destroy", ":id") }}'.replace(':id', record_id),
                        });

                        // Add CSRF token
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}',
                        }));

                        // Add the method input for DELETE
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE',
                        }));

                        // Append the form to the body and submit
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
