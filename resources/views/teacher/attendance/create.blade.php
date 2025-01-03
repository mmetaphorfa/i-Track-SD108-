@extends('layouts.app')

@section('page', 'New Attendance')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Attendance Records'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- add form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>New Attendance Record</h4>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('attendances.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="class">Class (Grade) <span class="text-danger">*</span></label>
                                <select class="form-select @error('class') border-danger @enderror" id="class" name="class" required>
                                    <option value="">Please Select</option>
                                    @foreach ($classrooms as $key => $classroom)
                                        <option value="{{ $classroom->classroom->id }}" {{ old('class') == $classroom->classroom->id ? 'selected' : '' }}>
                                            {{ $classroom->classroom->name }} ({{ $classroom->classroom->level }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') border-danger @enderror" id="date" name="date" value="{{ old('date') }}" 
                                    max="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') border-danger @enderror" id="type" name="type" required>
                                    <option value="">Please Select</option>
                                    @foreach (config('attendances') as $key => $type)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection