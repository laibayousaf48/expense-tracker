<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends BaseController
{

    public function loginView()
    {
        return view('login');
    }

    public function registerView()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        Log::info("admin register method called");
        // Validate admin registration data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Log::info("creating admin");
        // Create a new admin
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);
        Log::info("created admin", ['admin' => $admin]);
        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }


    public function login(Request $request)
    {
        try {
            Log::info('Admin login attempt', ['email' => $request->email]);
            
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
           
            Log::info('Attempting authentication with credentials', ['email' => $credentials['email']]);

            // Add the role condition to the credentials
            // $credentials['role'] = 'admin';


            if (Auth::attempt($credentials)) {
                Log::info('Authentication successful');
                $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

                $request->session()->regenerate();
                
                return redirect()->intended(route('home'));
            }

            Log::info('Authentication failed');
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    public function home()
    {
Log::info("admin home method called");
        $users = User::where('role', '!=', 'admin')->count();

        $expenses = Expense::with('category')
            ->orderBy('expense_date', 'desc')
            ->get();

        $categories = Category::withCount('expenses')
            ->get()
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group[0]->name,
                    'count' => $group->sum('expenses_count')
                ];
            });
            Log::info("categories count: " . $categories);
        $view = view('home', [
            'users' => $users,
            'categories' => $categories,
            'expenses' => $expenses
        ]);
        
        Log::info("admin dashboard view returned", ['users' => $users, 'categories' => $categories, 'expenses' => $expenses]);
        return $view;
    }
//users list
public function users()
{
    $users = User::where('role', '!=', 'admin')
    ->with(['expenses', 'budgets'])
    ->paginate(10);
    return view('users', [
        'users' => $users,
    ]);
}
//expenses list
public function expenses()
{
    $expenses = Expense::all();
    return view('expenses', [
        'expenses' => $expenses,
    ]);
}
//budgets list
public function budgets()
{
    $budgets = Budget::with(['category', 'user'])
        ->whereHas('user', function($query) {
            $query->where('role', '!=', 'admin');
        })
        ->get();

    return view('budgets', [
        'budgets' => $budgets,
    ]);
}


public function usersShow($id)
{
    $user = User::find($id);
    return view('usersShow', [
        'user' => $user,
    ]);
}   

public function usersEdit($id)
{
    $user = User::find($id);
    return view('usersEdit', [
        'user' => $user,
    ]);
}   

public function usersDestroy($id)
{
    $user = User::find($id);
    $user->delete();
    return redirect()->route('admin.users')->with('success', 'User deleted successfully');
}


    public function logout(Request $request)
    {
        Log::info("admin logout method called");
        // Auth::guard('web')->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        $user = Auth::user();
        Log::info('auth user',['users' => $users,]);
        // Auth::user()->tokens()->delete();
        return redirect()->route('admin.login')->with('success', 'Logout successful!');
    }




public function getUsers()
{
    // Ensure the user is an admin
    if (Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized access'], 403);
    }

    // Fetch all users with their expenses and budgets
    $users = User::with(['expenses', 'budgets'])->get();

    return response()->json([
        'message' => 'Users with expenses and budgets retrieved successfully',
        'data' => $users,
    ], 200);
}
}


//token for admin:    14|bMUYpwlYSbY7J0oUyHuxngpUexRPVzC8micv2arU86012123
