<!DOCTYPE html>
<html>

<head>
    <title>Employee Management</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTable -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <div class="row">

            <!-- LEFT SIDE: LIST -->
            <div class="col-md-12">
                {{-- <h5>Employees List</h5> --}}

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Employees List</h5>
                        <button class="btn btn-primary px-3" id="addEmployeeBtn">
                            + Add Employee
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- 🔍 FILTERS -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <input type="text" id="search_name" class="form-control"
                                    placeholder="Search by Name">
                            </div>
                            <div class="col-md-3">
                                <select id="department_filter" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="manager_filter" class="form-select">
                                    <option value="">All Managers</option>
                                    @foreach ($managers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" id="date_range" class="form-control"
                                        placeholder="Joining Date - To Date">
                                    <span class="input-group-text">
                                        📅
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- 📊 TABLE -->
                        <table class="table table-hover align-middle employeeTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Employee Code</th>
                                    <th>Department</th>
                                    <th>Manager</th>
                                    <th>Joining Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>      
        </div>
        <!-- Add/Edit Modal -->
        <div class="modal fade" id="employeeModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 id="modalTitle">Add Employee</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <!-- ✅ VALIDATION ERRORS -->
                        <div id="validationErrors" class="alert alert-danger d-none">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1" id="errorList"></ul>
                        </div>
                        <form id="employeeForm">
                            @csrf
                            <input type="hidden" id="id">

                            <input class="form-control mb-2" id="name" name="name" placeholder="Name">
                            <input class="form-control mb-2" id="employee_code" name="employee_code" placeholder="Code">

                            <select class="form-control mb-2" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach ($departments as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>

                            <select class="form-control mb-2" id="manager_id" name="manager_id">
                                <option value="">Select Manager</option>
                                @foreach ($managers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>

                            <input type="date" class="form-control mb-2" id="joining_date" name="joining_date">
                            <input type="text" class="form-control mb-2" id="email" placeholder="Email"
                                name="email">
                            <input type="number" class="form-control mb-2" id="phone" placeholder="Phone"
                                name="phone" max="15">
                            <textarea id="address" name="address" class="form-control" placeholder="Enter address"></textarea>
                            <br>
                            <button class="btn btn-success w-100">Save</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        var dataTableEmployee = $(".employeeTable").DataTable({
            processing: true,
            serverSide: true,
            //remove default search
            searching: false,
            //remove "Show entries"
            lengthChange: false,
            ajax: {
                url: "{{ route('employees.index') }}",
                data: function(d) {
                    d.name = $('#search_name').val();
                    d.department_id = $('#department_filter').val();
                    d.manager_id = $('#manager_filter').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'employee_code',
                    name: 'employee_code'
                },
                {
                    data: 'department',
                    name: 'department'
                },
                {
                    data: 'manager',
                    name: 'manager'
                },
                {
                    data: 'joining_date',
                    name: 'joining_date'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        //filter change
        $('#search_name, #department_filter, #manager_filter, #date_range').on('change keyup', function() {
            dataTableEmployee.draw();
        });
        // Clear validation errors
        function clearErrors() {
            $('#validationErrors').addClass('d-none');
            $('#errorList').html('');
        }

        //Show validation errors at top of modal
        function showErrors(errors) {
            let html = '';
            $.each(errors, function(field, messages) {
                $.each(messages, function(i, msg) {
                    html += `<li>${msg}</li>`;
                });
            });
            $('#errorList').html(html);
            $('#validationErrors').removeClass('d-none');
        }
        $('#addEmployeeBtn').click(function() {
            clearErrors();
            $('#employeeForm')[0].reset();
            $('#id').val('');
            $('#modalTitle').text('Add Employee');
            $('#employeeModal').modal('show');
        });
        $(document).on('click', '.editBtn', function() {
            clearErrors();
            let id = $(this).data('id');

            $.get(`/employees/${id}/edit`, function(res) {

                $('#id').val(res.id);
                $('#name').val(res.name);
                $('#employee_code').val(res.employee_code);
                $('#department_id').val(res.department_id);
                $('#manager_id').val(res.manager_id);
                $('#joining_date').val(res.joining_date);
                $('#email').val(res.email);
                $('#phone').val(res.phone);
                $('#address').val(res.address);

                $('#modalTitle').text('Edit Employee');
                $('#employeeModal').modal('show');
            });

        });
        $('#employeeForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            let id = $('#id').val();
            let url = id ? `/employees/${id}` : `/employees`;
            let type = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: type,
                data: {
                    name: $('#name').val(),
                    employee_code: $('#employee_code').val(),
                    department_id: $('#department_id').val(),
                    manager_id: $('#manager_id').val(),
                    joining_date: $('#joining_date').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $('#employeeModal').modal('hide');
                    dataTableEmployee.draw();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        showErrors(xhr.responseJSON.errors); //show errors on top of modal
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                }
            });
        });
        $(document).on('click', '.deleteBtn', function() {

            let id = $(this).data('id');
            if (confirm('Are you sure to delete?')) {
                $.ajax({
                    url: `/employees/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        dataTableEmployee.draw();
                    }
                });

            }
        });
        $('#employeeModal').on('hidden.bs.modal', function() {
            clearErrors();
            $('#employeeForm')[0].reset();
            $('#id').val('');
        });
        let startDate = '';
        let endDate = '';

        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');

            $(this).val(
                picker.startDate.format('DD-MM-YYYY') + ' - ' +
                picker.endDate.format('DD-MM-YYYY')
            );

            dataTableEmployee.draw();
        });

        $('#date_range').on('cancel.daterangepicker', function() {
            $(this).val('');
            startDate = '';
            endDate = '';
            dataTableEmployee.draw();
        });
    </script>

</body>

</html>
