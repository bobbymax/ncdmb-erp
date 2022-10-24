<?php

namespace App\Http\Controllers;

use App\Models\ProcurementProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcurementProcessController extends Controller
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
        $procurementProcess = ProcurementProcess::latest()->get();

        if ($procurementProcess->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $procurementProcess,
            'status' => 'success',
            'message' => 'Procurement Process List'
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
            'stage' => 'required|string',
            'slug' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procurementProcess = ProcurementProcess::create([
            'project_id' => $request->project_id,
            'stage' => $request->stage,
            'slug' => $request->slug
        ]);

        return response()->json([
            'data' => $procurementProcess,
            'status' => 'success',
            'message' => 'Procurement Process have been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $procurementProcess
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($procurementProcess)
    {
        $procurementProcess = ProcurementProcess::find($procurementProcess);

        if (! $procurementProcess) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $procurementProcess,
            'status' => 'success',
            'message' => 'Procurement Process details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $procurementProcess
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($procurementProcess)
    {
        $procurementProcess = ProcurementProcess::find($procurementProcess);

        if (! $procurementProcess) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $procurementProcess,
            'status' => 'success',
            'message' => 'Procurement Process details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcurementProcess  $procurementProcess
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $procurementProcess)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
            'stage' => 'required|string',
            'slug' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procurementProcess = ProcurementProcess::find($procurementProcess);

        if (! $procurementProcess) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $procurementProcess->update([
            'project_id' => $request->project_id,
            'stage' => $request->stage,
            'slug' => $request->slug
        ]);

        return response()->json([
            'data' => $procurementProcess,
            'status' => 'success',
            'message' => 'Procurement Process have been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcurementProcess  $procurementProcess
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($procurementProcess)
    {
        $procurementProcess = ProcurementProcess::find($procurementProcess);

        if (! $procurementProcess) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $procurementProcess;
        $procurementProcess->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Procurement Process Details have been updated successfully!!'
        ], 200);
    }
}
