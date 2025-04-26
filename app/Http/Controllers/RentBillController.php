<?php

namespace App\Http\Controllers;

use App\Models\RentBill;
use Illuminate\Http\Request;

class RentBillController extends Controller
{
    public function home()
    {
        $pageTitle = "ফ্লাট এর ভাড়া তালিকা";
        return view('admin.rentBill.index', compact('pageTitle'));
    }
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rentBills = RentBill::with('users')->when($search, function ($query, $search) {
            $query->where('users.name', 'like', "%{$search}%");
        })->paginate(10);
        $rentBills->getCollection()->transform(function ($rentBill) {
            return $rentBill;
        });

        return response()->json($rentBills);
    }


    public function postData(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:employees,id',
            'month' => 'required|string|max:500',
            'flat_rent' => 'required|integer',
            'electric_bill' => 'required|integer',
            'gas_bill' => 'nullable|integer',
            'water_bill' => 'required|integer',
            'garbase_bill' => 'nullable|integer',
            'past_due' => 'nullable|integer',
            'payment' => 'nullable|integer',
            'note' => 'nullable|string|max:500',
        ]);

        $validated['grand_total'] =
            ($validated['flat_rent'] ?? 0) +
            ($validated['electric_bill'] ?? 0) +
            ($validated['gas_bill'] ?? 0) +
            ($validated['water_bill'] ?? 0);
        $validated['due'] = $validated['grand_total'] - ($validated['payment'] ?? 0);
        $validated['due'] = $validated['due'] + ($validated['past_due'] ?? 0);

        $rentBill = RentBill::create($validated);

        return response()->json(['message' => 'Rent bill created successfully!', 'data' => $rentBill], 201);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:employees,id',
            'month' => 'required|string|max:500',
            'flat_rent' => 'required|integer',
            'electric_bill' => 'required|integer',
            'gas_bill' => 'nullable|integer',
            'water_bill' => 'required|integer',
            'garbase_bill' => 'nullable|integer',
            'past_due' => 'nullable|integer',
            'payment' => 'nullable|integer',
            'note' => 'nullable|string|max:500',


        ]);

        $validated['grand_total'] =
            ($validated['flat_rent'] ?? 0) +
            ($validated['electric_bill'] ?? 0) +
            ($validated['gas_bill'] ?? 0) +
            ($validated['water_bill'] ?? 0);
        $validated['due'] = $validated['grand_total'] - ($validated['payment'] ?? 0);
        $validated['due'] = $validated['due'] + ($validated['past_due'] ?? 0);

        $rentBill = RentBill::findOrFail($id);
        $rentBill->update($validated);

        return response()->json(['message' => 'Rent bill updated successfully!', 'data' => $rentBill]);
    }


    public function delete($id)
    {
        $rentBill = RentBill::findOrFail($id); // Ensure the rentBill exists
        $rentBill->delete();

        return response()->json(['message' => 'rentBill deleted successfully!']);
    }
}
