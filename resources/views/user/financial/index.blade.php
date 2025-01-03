@extends('layouts.app')

@section('page', 'Financial Management')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- statistics --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body total-sells">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0"><i data-feather="dollar-sign" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-payments">0</h2>
                            <p class="w-100 mt-1 mb-0">Total Payments ({{ date('Y') }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body total-sells-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0"><i data-feather="clock" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-pending">0</h2>
                            <p class="w-100 mt-1 mb-0">Pending Payments ({{ date('Y') }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body total-sells-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0"><i data-feather="check-circle" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-success">0</h2>
                            <p class="w-100 mt-1 mb-0">Successful Payments ({{ date('Y') }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body total-sells-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0"><i data-feather="x-circle" class="fs-4 text-light"></i></div>
                        <div class="flex-grow-1">
                            <h2 id="total-failed">0</h2>
                            <p class="w-100 mt-1 mb-0">Failed Payments ({{ date('Y') }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end statistics --}}

        {{-- payment list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Payment List</h4>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="payment-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Full Name</th>
                                    <th>Description</th>
                                    <th>Amount (RM)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end payment list --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Payment table
            $("#payment-table").DataTable({
                pageLength: 50,
                lengthMenu: [50, 100, 150, 200],
                ajax: {
                    url: "{{ route('ajax.getPayments', session('role')) }}",
                    type: 'get',
                    dataSrc: function (json) {
                        $('#total-payments').text(json.total);
                        $('#total-success').text(json.success);
                        $('#total-pending').text(json.pending);
                        $('#total-failed').text(json.failed);

                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.log(xhr.responseText);
                    }
                },
                columns: [
                    { data: 'num', className: 'text-center', searchable: false },
                    { data: 'invoice' },
                    { data: 'name' },
                    { data: 'description' },
                    { data: 'amount' },
                    {
                        data: 'status', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            if (data === 'paid') {
                                return '<span class="badge rounded-pill badge-success">Paid</span>';
                            } else if (data === 'pending') {
                                return '<span class="badge rounded-pill badge-warning">Pending</span>';
                            } else {
                                return '<span class="badge rounded-pill badge-danger">Failed</span>';
                            }
                        }
                    },
                    {
                        data: 'invoice', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            let url = '{{ route("financial.show", ":id") }}';
                            url = url.replace(':role', '{{ session("role") }}').replace(':id', row.invoice.toLowerCase());
                            return '<ul class="action"><li class="mx-auto"><a href="' + url + '" class="btn btn-sm btn-outline-primary px-3 py-1">View</a></li></ul>';
                        }
                    },
                ],
            });
        });
    </script>
@endsection
