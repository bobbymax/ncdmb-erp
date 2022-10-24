<?php

namespace App\Http\Controllers;

use App\Models\Retirement;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RetirementController extends Controller
{
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Retirement $retirement
     * @return JsonResponse
     */
    public function show(Retirement $retirement)
    {
        $retirement = Retirement::find($retirement);

        if (! $retirement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $retirement,
            'status' => 'success',
            'message' => 'Retirement details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Retirement $retirement
     * @return JsonResponse
     */
    public function edit($retirement)
    {
        $retirement = Retirement::find($retirement);

        if (! $retirement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $retirement,
            'status' => 'success',
            'message' => 'Retirement details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Retirement $retirement
     * @return JsonResponse
     */
    public function update(Request $request, $retirement)
    {
        $validator = Validator::make($request->all(), [
            'cash_advance_id' => 'required|integer',
            'description' => 'required',
            'starts' => 'required|date',
            'ends' => 'required|date',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $retirement = Retirement::find($retirement);

        if (! $retirement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $retirement->update([
            'cash_advance_id' => $request->cash_advance_id,
            'starts' => Carbon::parse($request->starts),
            'ends' => Carbon::parse($request->ends),
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'data' => $retirement,
            'status' => 'success',
            'message' => 'Retirement details have been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Retirement $retirement
     * @return JsonResponse
     */
    public function destroy($retirement)
    {
        $retirement = Retirement::find($retirement);

        if (! $retirement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $retirement;
        $retirement->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Retirement details have been deleted successfully!!'
        ], 200);
    }
}
