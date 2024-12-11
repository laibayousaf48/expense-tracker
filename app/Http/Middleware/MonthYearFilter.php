<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MonthYearFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            Log::info('MonthYearFilter middleware');
            
            // Default to current month and year if not provided
            $month = $request->query('month', now()->month);
            $year = $request->query('year', now()->year);

            // Validate month and year
            if (!is_numeric($month) || !is_numeric($year) || $month < 1 || $month > 12 || $year < 2000) {
                return response()->json(['error' => 'Invalid month or year parameters'], 400);
            }

            // Set request parameters
            $request->merge(['month' => (int) $month, 'year' => (int) $year]);
            Log::info('MonthYearFilter middleware', ['month' => $month, 'year' => $year]);
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('MonthYearFilter error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request'], 500);
        }
    }
}
