@extends('layouts.app')

@section('page', 'Administrator List')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Users Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- statistics --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body total-sells">
                    <div class="d-flex align-items-center gap-3"> 
                        <div class="flex-shrink-0"><i data-feather="users" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-users">0</h2>
                            <p class="w-100 mt-1 mb-0">Total Administrators</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body total-sells-4">
                    <div class="d-flex align-items-center gap-3"> 
                        <div class="flex-shrink-0"><i data-feather="user-check" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-active">0</h2>
                            <p class="w-100 mt-1 mb-0">Active Administrators</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body total-sells-3">
                    <div class="d-flex align-items-center gap-3"> 
                        <div class="flex-shrink-0"><i data-feather="user-x" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-inactive">0</h2>
                            <p class="w-100 mt-1 mb-0">Inactive Administrators</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end statistics --}}
        
        {{-- datatable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Administrator List</h4>
                        <a href="{{ route('admin.management.create') }}" class="btn btn-primary btn-sm">Add New</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="admin-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>NRIC Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
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
    {{-- users datatable --}}
    <script>
        $(document).ready(function () {
            $("#admin-table").DataTable({
                pageLength: 50,
                lengthMenu: [50, 100, 150, 200],
                ajax: {
                    url: "{{ route('ajax.getUserData', 'admin') }}",
                    type: 'get',
                    dataSrc: function (json) {
                        $('#total-users').text(json.total);
                        $('#total-active').text(json.active);
                        $('#total-inactive').text(json.inactive);
                        
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.log(xhr.responseText);
                    }
                },
                columns: [
                    { data: 'num', className: 'text-center', searchable: false },
                    { data: 'nric' },
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'phone' },
                    { 
                        data: 'status', orderable: false, searchable: false, 
                        render: function(data, type, row) {
                            if (data === 'active') {
                                return '<span class="badge rounded-pill badge-success">Active</span>';
                            } else {
                                return '<span class="badge rounded-pill badge-danger">Inactive</span>';
                            }
                        }
                    },
                    { 
                        data: 'id', orderable: false, searchable: false, 
                        render: function(data, type, row) {
                            let url = '{{ route("admin.management.edit", ":id") }}'.replace(':id', data);
                            return '<ul class="action"><li class="mx-auto"><a href="' + url + '"><i class="icon-pencil-alt text-dark"></i></a></li></ul>';
                        }
                    },
                ],
            });
        });
    </script>
@endsection