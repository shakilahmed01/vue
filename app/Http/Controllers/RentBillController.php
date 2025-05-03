<?php

namespace App\Http\Controllers;

use App\Models\RentBill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ($validated['garbase_bill'] ?? 0) +
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
            'garbase_bill' => 'nullable|integer', // Fixed typo
            'past_due' => 'nullable|integer',
            'payment' => 'nullable|integer',
            'note' => 'nullable|string|max:500',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Ensure defaults
            $validated['gas_bill'] = $validated['gas_bill'] ?? 0;
            $validated['garbase_bill'] = $validated['garbase_bill'] ?? 0;
            $validated['past_due'] = $validated['past_due'] ?? 0;
            $validated['payment'] = $validated['payment'] ?? 0;
    
            // Calculate totals
            $validated['grand_total'] =
                $validated['flat_rent'] +
                $validated['electric_bill'] +
                $validated['gas_bill'] +
                $validated['garbase_bill'] +
                $validated['water_bill'];
    
            // If due_payment is present, add it to validated['payment']
            $duePaymentAmount = is_numeric($request->due_payment) ? (float) $request->due_payment : 0;
            if ($duePaymentAmount > 0) {
                $validated['payment'] += $duePaymentAmount;
            
                Payment::create([
                    'user_id' => $validated['user_id'],
                    'due_payment' => $duePaymentAmount,
                ]);
            }
    
            $validated['due'] = $validated['grand_total'] - $validated['payment'] + $validated['past_due'];
    
            // Update rent bill
            $rentBill = RentBill::findOrFail($id);
            $rentBill->update($validated);
    
            DB::commit();
    
            return response()->json(['message' => 'Rent bill updated successfully!', 'data' => $rentBill]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update rent bill.', 'details' => $e->getMessage()], 500);
        }
    }


    public function delete($id)
    {
        $rentBill = RentBill::findOrFail($id); // Ensure the rentBill exists
        $rentBill->delete();

        return response()->json(['message' => 'rentBill deleted successfully!']);
    }
}
