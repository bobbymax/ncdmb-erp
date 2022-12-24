<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
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
        $claims = auth()->user()->claims;

        if ($claims->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'You do not have any claims registered!'
            ], 200);
        }

        return response()->json([
            'data' => ClaimResource::collection($claims),
            'status' => 'success',
            'message' => 'List of Claims'
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
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:staff-claim,touring-advance',
            'reference_no' => 'required|string|unique:claims',
            'start' => 'required|date',
            'end' => 'required|date',
            'total_amount' => 'required',
            'status' => 'required|string|in:pending,raised,registered,unregistered'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $claim = Claim::create([
            'title' => $request->title,
            'reference_no' => $request->reference_no,
            'type' => $request->type,
            'user_id' => $request->user_id,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'total_amount' => $request->type === 'touring-advance' ? $request->total_amount : 0,
            'status' => $request->status
        ]);

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Claim has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($claim): \Illuminate\Http\JsonResponse
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Claim details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($claim): \Illuminate\Http\JsonResponse
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Claim details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $claim): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date',
            'total_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $claim->update([
            'title' => $request->title,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'total_amount' => $request->total_amount ?? 0,
            'status' => $request->status ?? 'pending'
        ]);

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Claim has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($claim): \Illuminate\Http\JsonResponse
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $claim;
        $claim->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Claim details deleted successfully'
        ], 200);
    }
}
