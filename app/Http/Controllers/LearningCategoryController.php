<?php

namespace App\Http\Controllers;

use App\Models\LearningCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LearningCategoryController extends Controller
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
        $learningCategories = LearningCategory::latest()->get();

        if ($learningCategories->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $learningCategories,
            'status' => 'success',
            'message' => 'Category List'
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $learningCategory = LearningCategory::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => $learningCategory,
            'status' => 'success',
            'message' => 'Learning Category Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $learningCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($learningCategory): \Illuminate\Http\JsonResponse
    {
        $learningCategory = LearningCategory::find($learningCategory);

        if (! $learningCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $learningCategory,
            'status' => 'success',
            'message' => 'Learning Category Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $learningCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($learningCategory): \Illuminate\Http\JsonResponse
    {
        $learningCategory = LearningCategory::find($learningCategory);

        if (! $learningCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $learningCategory,
            'status' => 'success',
            'message' => 'Learning Category Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $learningCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $learningCategory): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $learningCategory = LearningCategory::find($learningCategory);

        if (! $learningCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $learningCategory->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => $learningCategory,
            'status' => 'success',
            'message' => 'Learning Category Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $learningCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($learningCategory): \Illuminate\Http\JsonResponse
    {
        $learningCategory = LearningCategory::find($learningCategory);

        if (! $learningCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $learningCategory;
        $learningCategory->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Learning Category Deleted Successfully!'
        ], 200);
    }
}
