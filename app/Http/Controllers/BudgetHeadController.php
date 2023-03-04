<?php

namespace App\Http\Controllers;

use App\Models\BudgetHead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BudgetHeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $budgetHeads = BudgetHead::with(['subBudgetHeads', 'fund'])->latest()->get();

        if ($budgetHeads->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $budgetHeads,
            'status' => 'success',
            'message' => 'Budget Head List'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:budget_heads',
            'budgetId' => 'required|string|max:255|unique:budget_heads',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix errors'
            ], 500);
        }

        $budgetHead = BudgetHead::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'budgetId' => $request->budgetId
        ]);

        return response()->json([
            'data' => $budgetHead,
            'status' => 'success',
            'message' => 'Budget Head created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param BudgetHead $budgetHead
     * @return JsonResponse
     */
    public function show($budgetHead)
    {
        $budgetHead = BudgetHead::find($budgetHead);

        if (! $budgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        return response()->json([
            'data' => $budgetHead,
            'status' => 'success',
            'message' => 'Budget Head details!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BudgetHead $budgetHead
     * @return JsonResponse
     */
    public function edit($budgetHead)
    {
        $budgetHead = BudgetHead::find($budgetHead);

        if (! $budgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        return response()->json([
            'data' => $budgetHead,
            'status' => 'success',
            'message' => 'Budget Head details!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param BudgetHead $budgetHead
     * @return JsonResponse
     */
    public function update(Request $request, $budgetHead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'budgetId' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix errors'
            ], 500);
        }

        $budgetHead = BudgetHead::find($budgetHead);

        if (! $budgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        $budgetHead->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'budgetId' => $request->budgetId
        ]);

        return response()->json([
            'data' => $budgetHead,
            'status' => 'success',
            'message' => 'Budget Head created successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BudgetHead $budgetHead
     * @return JsonResponse
     */
    public function destroy($budgetHead)
    {
        $budgetHead = BudgetHead::find($budgetHead);

        if (! $budgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        $old = $budgetHead;
        $budgetHead->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Budget Head deleted successfully!!'
        ], 200);
    }
}
