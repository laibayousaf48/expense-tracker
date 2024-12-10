<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IncomeController extends BaseController
{
    // Fetch all incomes with sources
    public function index(Request $request)
    {
        try {

            $userId = Auth::id();
            [$month, $year] = $this->getMonthYear($request);
            $incomes = Income::with('incomeSource')
                ->whereHas('incomeSource', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Check if income records are found
            if ($incomes->isEmpty()) {
                return response()->json([
                    'message' => 'No income records found for the selected month.',
                    'data' => [],
                ], 404);
            }
    
            // Return success response with the list of income records
            return response()->json([
                'message' => 'Incomes retrieved successfully',
                'data' => $incomes,
            ], 200);
    
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while retrieving incomes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

        // Store a new income entry
 

        public function store(Request $request)
        {
            Log::info('IncomeController@store', ['request' => $request->all()]);
            try {
                // Define validation rules
                $rules = [
                    'source' => 'required|string', // Name of the income source
                    'amount' => 'required|numeric',
                    'description' => 'nullable|string',
                    'date' => 'required|date',
                ];
        
                // Create validator instance
                $validator = Validator::make($request->all(), $rules);
        
                // Check if validation fails
                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
        
                // Check if the income source already exists or create a new one
                $incomeSource = IncomeSource::firstOrCreate(
                    // ['name' => $request->source, 'user_id' => Auth::id()], 
                    // Check for existing source by name and user
                    [
                        'name' => $request->source,  // Create new if not found
                        'user_id' => Auth::id()
                    ]
                );
                // Create a new income entry
                $income = Income::create([
                    'income_source_id' => $incomeSource->id,
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'date' => $request->date,
                   
                ]);
        
                // Return success response with the created income data
                return response()->json([
                    'message' => 'Income added successfully',
                    'data' => $income,
                ], 201);
        
            } catch (\Exception $e) {
                // Handle unexpected errors
                return response()->json([
                    'message' => 'An error occurred while adding income',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
        
    
        //summary of expenses and incomes
        public function summary(Request $request)
        {
            try {
                $userId = Auth::id(); // Get the authenticated user's ID
        
                // Retrieve the month and year from the request (handled by middleware or fallback to current)
                [$month, $year] = $this->getMonthYear($request);
        
                // Calculate total income for the specific month and year
                $totalIncome = IncomeSource::where('user_id', $userId)
                    ->with('incomes')
                    ->get()
                    ->flatMap(function($source) use ($month, $year) {
                        return $source->incomes()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->get();
                    })
                    ->sum('amount');
        
                // Calculate total expenses for the specific month and year
                $totalExpenses = Expense::where('user_id', $userId)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('amount');
        
                // Calculate the balance
                $balance = $totalIncome - $totalExpenses;
        
                // Return summary data in JSON response
                return response()->json([
                    'message' => 'Summary retrieved successfully',
                    'data' => [
                        'total_income' => $totalIncome,
                        'total_expenses' => $totalExpenses,
                        'balance' => $balance,
                        'month' => $month,
                        'year' => $year,
                    ],
                ], 200);
        
            } catch (\Exception $e) {
                // Handle unexpected errors
                return response()->json([
                    'message' => 'An error occurred while retrieving summary',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
        
        // Delete an income entry
        public function destroy($id)
        {
            $income = Income::findOrFail($id);
            $income->delete();
            return response()->json(['message' => 'Income deleted successfully'], 200);
        }
}
