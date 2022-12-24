<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClaimResource;
use App\Http\Resources\ExpenditureResource;
use App\Models\Claim;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Construct Class
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'claim_id' => 'required|integer',
            'expenses' => 'required|array',
            'status' => 'required|string|max:255|in:registered,unregistered,draft'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $claim = Claim::find($request->claim_id);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        foreach ($request->expenses as $value) {
            Expense::create([
                'claim_id' => $claim->id,
                'remuneration_id' => $value['remuneration_id'],
                'remuneration_child_id' => $value['remuneration_child_id'],
                'description' => $value['description'],
                'amount' => $value['amount'],
                'from' => Carbon::parse($value['from']),
                'to' => Carbon::parse($value['to']),
            ]);
        }

        $total = $claim->expenses->sum('amount');

        if ($claim->type === "touring-advance") {
            $claim->retired = true;
        } else {
            $claim->total_amount = $total;
        }

        $claim->spent_amount = $total;
        $claim->status = $request->status;
        $claim->save();

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Expenses details created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($expense): \Illuminate\Http\JsonResponse
    {
        $expense = Expense::find($expense);

        if (! $expense) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expense),
            'status' => 'success',
            'message' => 'Expense details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($expense): \Illuminate\Http\JsonResponse
    {
        $expense = Expense::find($expense);

        if (! $expense) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expense),
            'status' => 'success',
            'message' => 'Expense details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $expense): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'remuneration_id' => 'required|integer',
            'from' => 'required|date',
            'to' => 'required|date',
            'description' => 'required|min:3',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $expense = Expense::find($expense);

        if (! $expense) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $expense->update([
            'remuneration_id' => $request->remuneration_id,
            'remuneration_child_id' => $request->remuneration_child_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'from' => Carbon::parse($request->from),
            'to' => Carbon::parse($request->to),
        ]);

        return response()->json([
            'data' => new ExpenditureResource($expense),
            'status' => 'success',
            'message' => 'Expense details updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($expense): \Illuminate\Http\JsonResponse
    {
        $expense = Expense::find($expense);

        if (! $expense) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 204);
        }

        $claim = Claim::find($expense->claim_id);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 204);
        }

        $old = $expense;
        $expense->delete();

        $claim->total_amount = $claim->expenses->sum('amount');
        $claim->save();


        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Expense details deleted successfully!'
        ], 200);
    }
}
