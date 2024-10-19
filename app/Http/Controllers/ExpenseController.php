<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Get all expenses
    public function index()
    {
        $expenses = Expense::all();
        return response()->json($expenses);
    }

    // Store a new expense
    public function store(Request $request)
    {
        $expense = Expense::create($request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]));

        return response()->json($expense, 201);
    }

    // Update an expense
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $expense->update($request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]));

        return response()->json($expense);
    }

    // Delete an expense
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}

