<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BidController extends Controller
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
    public function index()
    {
        $bids = Bid::latest()->get();

        if ($bids->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $bids,
            'status' => 'success',
            'message' => 'Bid List'
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'amount' => 'required|integer',
            'proposal' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $status = $request->proposal !== "" ? 'registered' : 'draft';

        $bid = Bid::create([
            'project_id' => $request->project_id,
            'company_id' => $request->company_id,
            'amount' => $request->amount,
            'proposal' => $request->proposal ?? null,
            'status' => $status,
        ]);

        return response()->json([
            'data' => $bid,
            'status' => 'success',
            'message' => 'Bid Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bid  $bid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($bid)
    {
        $bid = Bid::find($bid);
        if (! $bid) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $bid,
            'status' => 'success',
            'message' => 'Bid Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bid  $bid
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($bid)
    {
        $bid = Bid::find($bid);
        if (! $bid) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $bid,
            'status' => 'success',
            'message' => 'Bid Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $bid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $bid)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:registered,draft,invitation,tenders,evaluation,closed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $bid = Bid::find($bid);

        if (! $bid) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $bid->update([
            'invitation' => $request->invitation,
            'technical_document' => $request->technical_document,
            'financial_document' => $request->financial_document,
            'status' => $request->status,
        ]);

        return response()->json([
            'data' => $bid,
            'status' => 'success',
            'message' => 'Bid Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $bid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($bid)
    {
        $bid = Bid::find($bid);

        if (! $bid) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $bid;
        $bid->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Bid record deleted Successfully!'
        ], 200);
    }
}
