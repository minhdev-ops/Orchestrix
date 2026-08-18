<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $mes = [
            'title.required'             => 'Title is required',
            'title.max'                  => 'Title must not exceed 255 characters',
            'amount.required'            => 'Amount is required',
            'amount.numeric'             => 'Amount must be a number',
            'amount.min'                 => 'Amount must be greater than 0',
            'expense_category_id.required' => 'Category is required',
            'expense_category_id.exists' => 'Category does not exist',
            'expense_date.date'          => 'Expense date is not valid',
            'payment_method.in'          => 'Payment method is not valid',
            'note.max'                   => 'Note must not exceed 2000 characters',
            'receipt_image.image'        => 'Receipt image must be an image file',
            'receipt_image.mimes'        => 'Receipt image must be jpeg, png, jpg or webp',
            'receipt_image.max'          => 'Receipt image must not exceed 5MB',
        ];

        $v = Validator::make($request->all(), [
            'title'                => 'required|string|max:255',
            'amount'               => 'required|numeric|min:0.01',
            'expense_category_id'  => 'required|integer|exists:expense_categories,id',
            'expense_date'         => 'nullable|date',
            'payment_method'       => 'nullable|string|in:cash,card,transfer,other',
            'note'                 => 'nullable|string|max:2000',
            'receipt_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], $mes);

        if ($v->fails()) {
            return response(['error' => $v->errors()], 400);
        }

        $category = ExpenseCategory::where('id', $request->expense_category_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$category) {
            return response(['error' => ['expense_category_id' => ['Category does not belong to this user']]], 400);
        }

        $receiptImage = null;
        if ($request->hasFile('receipt_image')) {
            $receiptImage = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense = Expense::create([
            'user_id'             => $request->user()->id,
            'expense_category_id' => $category->id,
            'title'               => $request->title,
            'amount'              => $request->amount,
            'expense_date'        => $request->expense_date ?? now()->toDateString(),
            'payment_method'      => $request->payment_method,
            'receipt_image'       => $receiptImage,
            'note'                => $request->note,
            'currency'            => 'VND',
        ]);

        return response()->json([
            'mes' => 'Expense created successfully.',
            'data' => $this->serialize($expense),
        ], 201);
    }

    public function categories(Request $request)
    {
        $categories = ExpenseCategory::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $mes = [
            'name.required' => 'Category name is required',
            'name.max'      => 'Category name must not exceed 100 characters',
            'name.unique'   => 'Category name already exists',
        ];

        $v = Validator::make($request->all(), [
            'name'  => 'required|string|max:100',
            'icon'  => 'nullable|string|max:20',
            'color' => 'nullable|string|max:20',
        ], $mes);

        if ($v->fails()) {
            return response(['error' => $v->errors()], 400);
        }

        $exists = ExpenseCategory::where('user_id', $request->user()->id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response(['error' => ['name' => ['Category name already exists']]], 400);
        }

        $category = ExpenseCategory::create([
            'user_id' => $request->user()->id,
            'name'    => $request->name,
            'icon'    => $request->icon,
            'color'   => $request->color,
        ]);

        return response()->json([
            'mes' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    private function serialize(Expense $expense)
    {
        return [
            'id'                => $expense->id,
            'uuid'              => $expense->uuid,
            'title'             => $expense->title,
            'amount'            => $expense->amount,
            'expense_date'      => $expense->expense_date?->toDateString(),
            'payment_method'    => $expense->payment_method,
            'receipt_image_url' => $expense->receipt_image_url,
            'note'              => $expense->note,
            'currency'          => $expense->currency,
            'category'          => $expense->category ? [
                'id'    => $expense->category->id,
                'name'  => $expense->category->name,
                'icon'  => $expense->category->icon,
                'color' => $expense->category->color,
            ] : null,
            'created_at' => $expense->created_at?->toIso8601String(),
        ];
    }
}