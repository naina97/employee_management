<!DOCTYPE html>
<html>

<head>
    <title>Employee Management</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTable -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <!-- RIGHT SIDE: FORM -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Add / Edit Employee</h5>
                    </div>
                    
                    <div class="card-body">
                        <form id="employeeForm">
                            @csrf
                            <input type="hidden" id="id" name="id">
                            <div class="mb-2">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Full Name">
                            </div>
                            <div class="mb-2">
                                <input type="text" name="employee_code" id="employee_code" class="form-control"
                                    placeholder="Employee Code">
                            </div>
                            <div class="mb-2">
                                <select name="department_id" id="department_id" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <select name="manager_id" id="manager_id" class="form-control">
                                    <option value="">Select Manager</option>
                                    @foreach ($managers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <input type="date" name="joining_date" id="joining_date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Email">
                            </div>
                            <div class="mb-2">
                                <input type="text" name="phone" id="phone" class="form-control"
                                    placeholder="Phone">
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                Save Employee
                            </button>
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

    <script>
        let table = $('#employeeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/employees",
                data: function(d) {
                    d.name = $('#search_name').val();
                    d.department_id = $('#department_filter').val();
                    d.manager_id = $('#manager_filter').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'employee_code'
                },
                {
                    data: 'department'
                },
                {
                    data: 'manager'
                },
                {
                    data: 'joining_date'
                },
                {
                    data: 'action',
                    orderable: false
                }
            ]
        });

        // 🔍 Filter
        $('#filter_btn').click(function() {
            table.draw();
        });

        // ➕ Add / Update
        $('#employeeForm').submit(function(e) {
            e.preventDefault();

            let id = $('#id').val();
            let url = id ? `/employees/${id}` : `/employees`;
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(res) {
                    table.ajax.reload();
                    $('#employeeForm')[0].reset();
                    $('#id').val('');
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.errors);
                }
            });
        });

        // ✏️ Edit
        $(document).on('click', '.edit', function() {
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
            });
        });

        // ❌ Delete
        $(document).on('click', '.delete', function() {
            let id = $(this).data('id');

            if (confirm('Delete this employee?')) {
                $.ajax({
                    url: `/employees/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        table.ajax.reload();
                    }
                });
            }
        });
    </script>

</body>

</html>
