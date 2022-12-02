<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
        $categories = Category::latest()->get();

        if ($categories->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $categories,
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
     * @param  \Illuminate\Http\Request  $request
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

        $category = Category::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'parentId' => $request->parentId,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $category,
            'status' => 'success',
            'message' => 'Category Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($category): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($category);

        if (! $category) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $category,
            'status' => 'success',
            'message' => 'Category Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($category): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($category);

        if (! $category) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $category,
            'status' => 'success',
            'message' => 'Category Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $category): \Illuminate\Http\JsonResponse
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

        $category = Category::find($category);

        if (! $category) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $category->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'parentId' => $request->parentId,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $category,
            'status' => 'success',
            'message' => 'Category Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($category): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($category);

        if (! $category) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }
        $old = $category;
        $category->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Category Deleted Successfully!'
        ], 200);
    }
}
