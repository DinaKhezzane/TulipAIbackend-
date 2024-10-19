<?php

namespace App\Http\Controllers;

use App\Models\Profit;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    public function index()
    {
        return Profit::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'revenue' => 'required|numeric',
            'cogs' => 'required|numeric',
            'operating_expenses' => 'required|numeric',
        ]);

        $grossProfit = $validatedData['revenue'] - $validatedData['cogs'];
        $netProfit = $grossProfit - $validatedData['operating_expenses'];

        $profit = Profit::create(array_merge($validatedData, [
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
        ]));

        return response()->json($profit, 201);
    }
}
