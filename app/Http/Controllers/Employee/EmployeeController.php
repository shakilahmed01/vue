<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee; // Correct namespace for Employee model
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the search term from the request
        $search = $request->input('search');

        // Query the employees table, applying a search filter if provided
        $employees = Employee::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        })->paginate(10);

        return response()->json($employees);
    }


    public function postData(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->address = $request->address;
        $employee->phone = $request->phone;
        $employee->save();

        return response()->json(['message' => 'Employee created successfully!'], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
        ]);

        $employee = Employee::findOrFail($id); // Ensure the employee exists
        $employee->update($request->only(['name', 'address', 'phone']));

        return response()->json(['message' => 'Employee updated successfully!']);
    }

    public function delete($id)
    {
        $employee = Employee::findOrFail($id); // Ensure the employee exists
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully!']);
    }
}
