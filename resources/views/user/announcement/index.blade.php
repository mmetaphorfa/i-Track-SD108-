@extends('layouts.app')

@section('page', 'Announcement List')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Announcements'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- announcement list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Announcement List</h4>
                        <a id="addBtn" href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">Add New</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="news-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Start At</th>
                                    <th>End At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($news as $key => $new)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $new->title }}</td>
                                    <td>{{ date('d/m/Y, h:i A', strtotime($new->start_at)) }}</td>
                                    <td>{{ date('d/m/Y, h:i A', strtotime($new->end_at)) }}</td>
                                    <td>
                                    @if ($new->status == 'draft')
                                        <span class="badge badge-warning">Draft</span>
                                    @else
                                        <span class="badge badge-success">Published</span>
                                    @endif
                                    </td>
                                    <td>
                                        <ul class="action justify-content-center gap-3">
                                            <li><a href="{{ route('announcements.edit', $new->id) }}"><i class="icon-pencil-alt text-dark"></i></a></li>
                                            <li><a href="javascript:void(0);" data-id="{{ $new->id }}" class="btn-delete"><i class="icon-trash text-danger"></i></a></li>
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
        {{-- end announcement list --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Announcement datatable
            $("#news-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });

            // Delete button
            $('.btn-delete').click(function () {
                const news_id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure you want to delete this announcement?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a dynamic form
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("announcements.destroy", ":id") }}'.replace(':id', news_id),
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