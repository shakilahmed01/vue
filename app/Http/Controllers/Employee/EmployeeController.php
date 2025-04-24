<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee; // Correct namespace for Employee model
use App\Models\EmployeePosition;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function home()
    {
        $pageTitle = "ভাড়াটিয়াদের ড্যাশবোরড";
        return view('admin.employee.index', compact('pageTitle'));
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $employees = Employee::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        })->paginate(10);
        $employees->getCollection()->transform(function ($employee) {
            if ($employee->image) {
                $employee->image_url = url('' . $employee->image);
            } else {
                $employee->image_url = null; // No image
            }
            return $employee;
        });
    
        return response()->json($employees);
    }
    


    public function postData(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);
    
        $imagePath = null;
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName); 
            $imagePath = 'images/' . $imageName; 
        }
    
        $employee = new Employee();
        $employee->name = $request->name;
        $employee->address = $request->address;
        $employee->phone = $request->phone;
        $employee->image = $imagePath; // Save the public path to the database
        $employee->save();
    
        return response()->json(['message' => 'Employee created successfully!', 'image_path' => $imagePath], 201);
    }
    

    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional image validation
        ]);
        
        $employee = Employee::findOrFail($id);

        if ($request->hasFile('image')) {
            // Delete the old image
            if ($employee->image) {
                $oldImagePath = public_path($employee->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $validated['image'] = 'images/' . $imageName; // ✅ use this path for saving
        }
        
    
        // Update the employee with the validated data
        $employee->update($validated);
    
        // Return the updated employee as JSON
        return response()->json($employee);
    }
    

    public function delete($id)
    {
        $employee = Employee::findOrFail($id); // Ensure the employee exists
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully!']);
    }

    //position function begin

    public function addPosition(Request $request)
    {
        $request->validate([
            'em_id' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $position = new EmployeePosition();
        $position->em_id = $request->em_id;
        $position->name = $request->name;
        $position->save();

        return response()->json(['message' => 'Employee Position created successfully!'], 201);
    }
}
