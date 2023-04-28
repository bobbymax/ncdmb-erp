<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettlementResource;
use App\Models\GradeLevel;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettlementController extends Controller
{

    public $settlements;

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
        $settlements = Settlement::latest()->get();

        if ($settlements->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'warning',
                'message' => 'No Data Found'
            ], 200);
        }

        return response()->json([
            'data' => SettlementResource::collection($settlements),
            'status' => 'success',
            'message' => 'List of Remunerations'
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
            'grades' => 'required|array',
            'remuneration_id' => 'required|integer',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        foreach ($request->grades as $value) {
            $grade = GradeLevel::find($value['value']);

            if ($grade) {
                $settlement = Settlement::create([
                    'remuneration_id' => $request->remuneration_id,
                    'grade_level_id' => $grade->id,
                    'amount' => $request->amount
                ]);

                $this->settlements[] = $settlement;
            }
        }

        return response()->json([
            'data' => SettlementResource::collection($this->settlements),
            'status' => 'success',
            'message' => 'Settlements created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $settlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($settlement): \Illuminate\Http\JsonResponse
    {
        $settlement = Settlement::find($settlement);

        if (! $settlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new SettlementResource($settlement),
            'status' => 'success',
            'message' => 'Settlement details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $settlement
     * @return JsonResponse
     */
    public function edit($settlement): \Illuminate\Http\JsonResponse
    {
        $settlement = Settlement::find($settlement);

        if (! $settlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new SettlementResource($settlement),
            'status' => 'success',
            'message' => 'Settlement details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $settlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $settlement): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'grade_level_id' => 'required|integer',
            'remuneration_id' => 'required|integer',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $settlement = Settlement::find($settlement);

        if (! $settlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $settlement->update([
            'remuneration_id' => $request->remuneration_id,
            'grade_level_id' => $request->grade_level_id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'data' => new SettlementResource($settlement),
            'status' => 'success',
            'message' => 'Settlement updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $settlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($settlement): \Illuminate\Http\JsonResponse
    {
        $settlement = Settlement::find($settlement);

        if (! $settlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $old = $settlement;
        $settlement->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Settlement deleted successfully!'
        ], 200);
    }
}
