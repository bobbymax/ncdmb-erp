<?php

namespace App\Http\Controllers;

use App\Http\Resources\RefundResource;
use App\Models\Expenditure;
use App\Models\Refund;
use App\Models\SubBudgetHead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $refunds = Refund::latest()->get();

        if ($refunds->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'warning',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => RefundResource::collection($refunds),
            'status' => 'success',
            'message' => 'List of Logistics Refunds'
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'expenditure_id' => 'required|integer',
            'beneficiary' => 'required|string|max:255',
            'amount' => 'required',
            'description' => 'required|min:3',
            'budget_year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $refund = Refund::create($request->all());

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Request has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($refund): \Illuminate\Http\JsonResponse
    {
        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($refund): \Illuminate\Http\JsonResponse
    {
        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $refund): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'beneficiary' => 'required|string|max:255',
            'amount' => 'required',
            'description' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $refund->update([
            'department_id' => $request->department_id,
            'beneficiary' => $request->beneficiary,
            'amount' => $request->amount,
            'description' => $request->description
        ]);

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Request has been updated successfully!'
        ], 200);
    }

    public function fulfillRefundRequest(Request $request, $refund): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'sub_budget_head_id' => 'required|integer',
            'status' => 'required|string|max:255|in:approved,denied',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        if ($request->status === "approved") {
            $newSub = SubBudgetHead::find($request->sub_budget_head_id);
            $controller = User::find($request->user_id);

            if (! $newSub || ! $controller) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Something went terribly wrong!!'
                ], 500);
            }

            $debit = $newSub->fund;
            $credit = $refund->expenditure->fund();

            $debit->booked_expenditure += $refund->amount;
            $debit->actual_expenditure += $refund->amount;
            $debit->booked_balance -= $refund->amount;
            $debit->actual_balance -= $refund->amount;
            $debit->save();

            $credit->booked_expenditure -= $refund->amount;
            $credit->actual_expenditure -= $refund->amount;
            $credit->booked_balance += $refund->amount;
            $credit->actual_balance += $refund->amount;
            $credit->save();

            if ($refund->expenditure->amount == $refund->expenditure->refunds->sum('amount')) {
                $refund->expenditure->update([
                    'status' => 'refunded'
                ]);
            }

            Expenditure::create([
                'user_id' => $controller->id,
                'sub_budget_head_id' => $newSub->id,
                'beneficiary' => $refund->expenditure->subBudgetHead->name,
                'department_id' => $controller->department_id,
                'amount' => $refund->amount,
                'approved_amount' => $refund->amount,
                'description' => 'REFUND FOR ' . $refund->description,
                'type' => $refund->expenditure->type,
                'payment_type' => $refund->expenditure->payment_type,
                'status' => 'paid',
                'approval_status' => 'accounts',
                'level' => 4,
                'closed' => true
            ]);
        }

        $refund->update([
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'status' => $request->status,
            'remark' => $request->remark,
            'closed' => $request->status === "approved"
        ]);

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Request has been fulfilled successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($refund): \Illuminate\Http\JsonResponse
    {
        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $old = $refund;
        $refund->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Refund Request has been deleted successfully!'
        ], 200);
    }
}
