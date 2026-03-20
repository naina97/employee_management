<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::pluck('name', 'id');
        $managers = Employee::pluck('name', 'id');
        // DataTable AJAX
        if ($request->ajax()) {
            $query = Employee::with(['department', 'manager'])->select('employees.*');
            // dd($request->all(),$request->date_range);
            // 🔍 Filters
            if (! empty($request->name)) {
                $query->where('employees.name', 'like', "%{$request->name}%");
            }
            if (! empty($request->department_id)) {
                $query->where('employees.department_id', '=', $request->department_id);
            }
            if (! empty($request->manager_id)) {
                $query->where('employees.manager_id', '=', $request->manager_id);
            }
            if (! empty($request->date_range)) {
                $date_range = explode(' - ', $request->date_range);
                $start_data = date('Y-m-d', strtotime($date_range[0]));
                $end_data = date('Y-m-d', strtotime($date_range[1]));

                $query->whereDate('employees.joining_date', '>=', $start_data);
                $query->whereDate('employees.joining_date', '<=', $end_data);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<span class="text-primary">'.$row->name.'</span>';
                })

                ->addColumn('employee_code', function ($row) {
                    return $row->employee_code;
                })
                ->addColumn('joining_date', function ($row) {
                    return $row->joining_date ? date('d-m-Y', strtotime($row->joining_date)) : '-';
                })
                ->addColumn('department', fn ($row) => $row->department->name ?? '-')
                ->addColumn('manager', fn ($row) => $row->manager->name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-light border me-1 editBtn" data-id="'.$row->id.'">
                            <i class="bi bi-pencil"></i>  Edit
                        </button>

                        <button class="btn btn-sm btn-light border deleteBtn" data-id="'.$row->id.'">
                            <i class="bi bi-trash text-danger"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action', 'department', 'name', 'email', 'phone', 'address'])
                ->make(true);
        }

        return view('employees.index', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
            'manager_id' => 'nullable|exists:employees,id',
            'joining_date' => 'required|date',
            'address' => 'nullable|string|max:500',
        ]);

        $employee = new Employee;
        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->department_id = $request->department_id;
        $employee->manager_id = $request->manager_id;
        $employee->joining_date = $request->joining_date;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Employee created successfully',
        ]);
    }

    public function edit(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        // dd(55588888888888,$request->all());
        $employee = Employee::find($id);
        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->department_id = $request->department_id;
        $employee->manager_id = $request->manager_id;
        $employee->joining_date = $request->joining_date;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        $employee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Employee deleted successfully',
        ]);
    }

    // 🔥 Clean filter method
    private function filter($query, $request)
    {
        return $query
            ->when($request->name, fn ($q) => $q->where('name', 'like', "%{$request->name}%")
            )
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id)
            )
            ->when($request->manager_id, fn ($q) => $q->where('manager_id', $request->manager_id)
            )
            ->when($request->from_date && $request->to_date, fn ($q) => $q->whereBetween('joining_date', [$request->from_date, $request->to_date])
            );
    }
}
