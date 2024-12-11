<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function getMonthYear(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('getMonthYear method called from base controller');
        // Ensure the request contains valid month and year
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        if ($month < 1 || $month > 12 || $year < 2000 || $year > now()->year) {
            throw new \InvalidArgumentException("Invalid month or year");
        }

        return [$month, $year];
    }
}
