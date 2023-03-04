<?php

namespace App\Http\Controllers;

use App\Http\Resources\TouringAdvanceResource;
use App\Models\Claim;
use App\Models\TouringAdvance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TouringAdvanceController extends Controller
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
        $touringAdvances = TouringAdvance::latest()->get();

        if ($touringAdvances->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => TouringAdvanceResource::collection($touringAdvances),
            'status' => 'success',
            'message' => 'List of Advances'
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'controller_id' => 'required|integer',
            'department_id' => 'required|integer',
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'total_amount' => 'required',
            'reference_no' => 'required|string|max:7|unique:claims'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix errors'
            ], 500);
        }

        $claim = Claim::create([
            'user_id' => $request->user_id,
            'reference_no' => $request->reference_no,
            'title' => $request->title,
            'total_amount' => $request->total_amount,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'type' => "touring-advance",
        ]);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Claim was not created!!!'
            ], 500);
        }

        $touringAdvance = TouringAdvance::create([
            'department_id' => $request->department_id,
            'user_id' => $request->controller_id,
            'claim_id' => $claim->id,
        ]);

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $touringAdvance
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($touringAdvance): \Illuminate\Http\JsonResponse
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        return response()->json([
            'data' => $touringAdvance,
            'status' => 'success',
            'message' => 'Touring Advance Details!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $touringAdvance
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($touringAdvance): \Illuminate\Http\JsonResponse
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        return response()->json([
            'data' => $touringAdvance,
            'status' => 'success',
            'message' => 'Touring Advance Details!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $touringAdvance
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $touringAdvance): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'total_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix errors'
            ], 500);
        }

        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance || $touringAdvance->status === "raised") {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        $claim = Claim::find($touringAdvance->claim_id);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        $claim->update([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'total_amount' => $request->total_amount,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
        ]);

        $touringAdvance->update([
            'department_id' => $request->department_id,
        ]);

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $touringAdvance
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($touringAdvance): \Illuminate\Http\JsonResponse
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance || $touringAdvance->status === "raised") {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entry'
            ], 422);
        }

        $old = $touringAdvance;
        $touringAdvance->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Touring Advance has been deleted successfully!!'
        ], 200);
    }
}
