<?php

namespace App\Http\Controllers;

use App\Models\Qualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class QualificationController extends Controller
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
        $qualifications = Qualification::latest()->get();

        if ($qualifications->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $qualifications,
            'status' => 'success',
            'message' => 'List of Qualifications'
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
            'type' => 'required|string|max:255',
            'min' => 'required|integer',
            'max' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $qualification = Qualification::create([
            'type' => $request->type,
            'label' => Str::slug($request->type),
            'min' => $request->min,
            'max' => $request->max,
        ]);

        return response()->json([
            'data' => $qualification,
            'status' => 'success',
            'message' => 'Qualification Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($qualification): \Illuminate\Http\JsonResponse
    {
        $qualification = Qualification::find($qualification);

        if (! $qualification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $qualification,
            'status' => 'success',
            'message' => 'Qualification Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($qualification): \Illuminate\Http\JsonResponse
    {
        $qualification = Qualification::find($qualification);

        if (! $qualification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $qualification,
            'status' => 'success',
            'message' => 'Qualification Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $qualification): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'min' => 'required|integer',
            'max' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $qualification = Qualification::find($qualification);

        if (! $qualification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $qualification->update([
            'type' => $request->type,
            'label' => Str::slug($request->type),
            'min' => $request->min,
            'max' => $request->max,
        ]);

        return response()->json([
            'data' => $qualification,
            'status' => 'success',
            'message' => 'Qualification Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($qualification): \Illuminate\Http\JsonResponse
    {
        $qualification = Qualification::find($qualification);

        if (! $qualification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $qualification;
        $qualification->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Qualification Deleted Successfully!'
        ], 200);
    }
}
