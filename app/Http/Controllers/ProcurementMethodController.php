<?php

namespace App\Http\Controllers;

use App\Models\ProcurementMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProcurementMethodController extends Controller
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
        $procurementMethods = ProcurementMethod::latest()->get();

        if ($procurementMethods->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 404);
        }

        return response()->json([
            'data' => $procurementMethods,
            'status' => 'success',
            'message' => 'List of Service Categories'
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:procurement_methods'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procurementMethod = ProcurementMethod::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
        ]);

        return response()->json([
            'data' => $procurementMethod,
            'status' => 'success',
            'message' => 'Procurement Method has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProcurementMethod  $procurementMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($procurementMethod)
    {
        $procurementMethod = ProcurementMethod::find($procurementMethod);
        if (! $procurementMethod) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $procurementMethod,
            'status' => 'success',
            'message' => 'Procurement Method details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProcurementMethod  $procurementMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($procurementMethod)
    {
        $procurementMethod = ProcurementMethod::find($procurementMethod);
        if (! $procurementMethod) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $procurementMethod,
            'status' => 'success',
            'message' => 'Procurement Method details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcurementMethod  $procurementMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $procurementMethod)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:procurement_methods'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procurementMethod = ProcurementMethod::find($procurementMethod);
        if (! $procurementMethod) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $procurementMethod->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
        ]);

        return response()->json([
            'data' => $procurementMethod,
            'status' => 'success',
            'message' => 'Procurement Method Details Updated Successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcurementMethod  $procurementMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($procurementMethod)
    {
        $procurementMethod = ProcurementMethod::find($procurementMethod);
        if (! $procurementMethod) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $procurementMethod;
        $procurementMethod->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Procurement Method deleted successfully!!'
        ], 200);
    }
}
