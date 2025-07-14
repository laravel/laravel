<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Get all departments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $departments = Department::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json($departments, 200);
    }
}
