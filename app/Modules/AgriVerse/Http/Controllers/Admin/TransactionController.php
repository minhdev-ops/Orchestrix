<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController
{
    public function index(Request $request)
    {
        $query = Transaction::with(['order', 'user']);

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        $transactions = $query->latest()->paginate(15);

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['order.product', 'order.buyer', 'user']);

        return Inertia::render('Admin/Transactions/Show', [
            'transaction' => $transaction,
        ]);
    }
}
