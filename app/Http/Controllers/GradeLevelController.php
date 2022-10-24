<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeLevelController extends Controller
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
        $gradeLevels = GradeLevel::latest()->get();

        if ($gradeLevels->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $gradeLevels,
            'status' => 'success',
            'message' => 'GradeLevel List'
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
            'key' => 'required|string|unique:grade_levels'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $gradeLevel = GradeLevel::create([
            'name' => $request->name,
            'key' => $request->key,
        ]);

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'GradeLevel Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade Level Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade Level Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $gradeLevel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $gradeLevel->update([
            'name' => $request->name,
            'key' => $request->key,
        ]);

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade Level Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $gradeLevel;
        $gradeLevel->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Grade Level Details'
        ], 200);
    }
}
