<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Employee;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        try {
            // Use Employee model to get all employees
            $employees = Employee::orderBy('hire_date', 'desc')->get();
            
            return view('employe', compact('employees'));
        } catch (\Exception $e) {
            return view('employe')->with('error', 'Failed to load employees: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255|unique:employees,email',
            'position' => 'required|string|max:255',
            'department' => 'required|string|in:IT,HR,Finance,Marketing,Operations',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date|before_or_equal:today',
        ], [
            'name.regex' => 'Name should only contain letters and spaces.',
            'email.unique' => 'This email address is already registered.',
            'salary.min' => 'Salary must be a positive number.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create new employee using the model
            Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'position' => $request->position,
                'department' => $request->department,
                'salary' => $request->salary,
                'hire_date' => $request->hire_date,
                'status' => 'active'
            ]);
            
            return redirect()->route('employee.index')
                ->with('success', 'Employee has been added successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            return view('employee.edit', compact('employee'));
            
        } catch (\Exception $e) {
            return redirect()->route('employee.index')
                ->with('error', 'Failed to load employee: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        // Validation rules (similar to store but with unique email exception)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255|unique:employees,email,' . $id,
            'position' => 'required|string|max:255',
            'department' => 'required|string|in:IT,HR,Finance,Marketing,Operations',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date|before_or_equal:today',
        ], [
            'name.regex' => 'Name should only contain letters and spaces.',
            'email.unique' => 'This email address is already registered.',
            'salary.min' => 'Salary must be a positive number.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employee = Employee::findOrFail($id);
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'position' => $request->position,
                'department' => $request->department,
                'salary' => $request->salary,
                'hire_date' => $request->hire_date,
            ]);
            
            return redirect()->route('employee.index')
                ->with('success', 'Employee has been updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            
            return redirect()->route('employee.index')
                ->with('success', 'Employee has been deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('employee.index')
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Get employees statistics
     */
    public function getStatistics()
    {
        try {
            $statistics = Employee::getStatistics();
            
            return response()->json($statistics);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get statistics: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export employees data
     */
    public function export()
    {
        try {
            $employees = Employee::orderBy('hire_date', 'desc')->get();
            
            $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($employees) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, ['ID', 'Name', 'Email', 'Position', 'Department', 'Salary', 'Hire Date', 'Years of Service', 'Status']);
                
                // CSV data
                foreach ($employees as $employee) {
                    fputcsv($file, [
                        $employee->id,
                        $employee->name,
                        $employee->email,
                        $employee->position,
                        $employee->department,
                        $employee->salary,
                        $employee->hire_date->format('Y-m-d'),
                        $employee->years_of_service,
                        $employee->status
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->route('employee.index')
                ->with('error', 'Failed to export employees: ' . $e->getMessage());
        }
    }

    /**
     * Search employees
     */
    public function search(Request $request)
    {
        try {
            $query = Employee::query();
            
            // Search by name
            if ($request->has('name') && $request->name) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            
            // Filter by department
            if ($request->has('department') && $request->department) {
                $query->where('department', $request->department);
            }
            
            // Filter by position
            if ($request->has('position') && $request->position) {
                $query->where('position', 'like', '%' . $request->position . '%');
            }
            
            // Salary range filter
            if ($request->has('min_salary') && $request->min_salary) {
                $query->where('salary', '>=', $request->min_salary);
            }
            
            if ($request->has('max_salary') && $request->max_salary) {
                $query->where('salary', '<=', $request->max_salary);
            }
            
            // Hire date range filter
            if ($request->has('hire_date_from') && $request->hire_date_from) {
                $query->where('hire_date', '>=', $request->hire_date_from);
            }
            
            if ($request->has('hire_date_to') && $request->hire_date_to) {
                $query->where('hire_date', '<=', $request->hire_date_to);
            }
            
            $employees = $query->orderBy('hire_date', 'desc')->get();
            
            if ($request->ajax()) {
                return response()->json($employees);
            }
            
            return view('employe', compact('employees'));
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
            }
            
            return redirect()->route('employee.index')
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }
}
