<?php

namespace App\Http\Controllers;
use App\Models\Flat;
use Illuminate\Http\Request;

class FlatController extends Controller
{
    public function home()
    {
        $pageTitle = "ফ্লাট এর তালিকা";
        return view('admin.flat.index', compact('pageTitle'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $flats = Flat::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate(10);
        $flats->getCollection()->transform(function ($flat) {
            return $flat;
        });
    
        return response()->json($flats);
    }
    
    public function postData(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $flat = new Flat();
        $flat->name = $request->name;
        $flat->save();
    
        return response()->json(['message' => 'flat created successfully!'], 201);
    }
    
    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $flat = Flat::findOrFail($id);

        // Update the flat with the validated data
        $flat->update($validated);
    
        // Return the updated flat as JSON
        return response()->json($flat);
    }
    

    public function delete($id)
    {
        $flat = Flat::findOrFail($id); // Ensure the flat exists
        $flat->delete();

        return response()->json(['message' => 'flat deleted successfully!']);
    }

}
