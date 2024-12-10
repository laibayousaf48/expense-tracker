<?php

namespace App\Http\Controllers;

use App\Models\IncomeSource;
use Illuminate\Http\Request;

class IncomeSourceController extends Controller
{
    public function index()
    {
        return response()->json(IncomeSource::all(), 200);
    }

    // Store a new income source
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:income_sources']);

        $incomeSource = IncomeSource::create($request->all());
        return response()->json(['message' => 'Income source added successfully', 'data' => $incomeSource], 201);
    }

    // Delete an income source
    public function destroy($id)
    {
        $incomeSource = IncomeSource::findOrFail($id);
        $incomeSource->delete();
        return response()->json(['message' => 'Income source deleted successfully'], 200);
    }
}
