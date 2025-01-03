@extends('layouts.app')

@section('page', 'Attendance History')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Attendance Records'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- datatable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Attendance History</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="attendance-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Class Name (Grade)</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end datatable --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Users datatable
            $("#attendance-table").DataTable({
                pageLength: 50,
                lengthMenu: [50, 100, 150, 200],
                ajax: {
                    url: "{{ route('ajax.getAttendances', Auth::user()->id) }}",
                    type: 'get',
                    dataSrc: function (json) {
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.log(xhr.responseText);
                    }
                },
                columns: [
                    { data: 'num', className: 'text-center', searchable: false },
                    { data: 'class' },
                    { data: 'date' },
                    { data: 'type' },
                    { 
                        data: 'id', orderable: false, searchable: false, 
                        render: function(data, type, row) {
                            let url = '{{ route("attendances.edit", ":id") }}'.replace(':id', data);
                            return '<ul class="action justify-content-center gap-3"><li><a href="' + url + '"><i class="icon-pencil-alt text-dark"></i></a></li><li><a href="javascript:void(0);" class="btn-delete" data-id="' + data + '"><i class="icon-trash text-danger"></i></a></li></ul>';
                        }
                    },
                ],
            });

            // Delete attendance
            $(document).on('click', '.btn-delete', function () {
                const record_id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure you want to delete this attendance record?',
                    text: 'This action cannot be undone!',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a dynamic form
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("attendances.destroy", ":id") }}'.replace(':id', record_id),
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