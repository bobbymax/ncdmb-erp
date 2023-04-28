<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DepartmentController extends Controller
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
        $departments = Department::latest()->get();

        if ($departments->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $departments,
            'status' => 'success',
            'message' => 'Department List'
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
            'code' => 'required|string|max:7|unique:departments',
            'payment_code' => 'required|string|unique:departments',
            'type' => 'required|string|in:directorate,division,department,unit,committee',
            'parentId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $department = Department::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
            'payment_code' => $request->payment_code,
            'type' => $request->type,
            'parentId' => $request->parentId
        ]);

        return response()->json([
            'data' => $department,
            'status' => 'success',
            'message' => 'Department created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($department): \Illuminate\Http\JsonResponse
    {
        $department = Department::find($department);

        if (! $department) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid department ID'
            ], 422);
        }

        return response()->json([
            'data' => $department,
            'status' => 'success',
            'message' => 'Department details!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($department): \Illuminate\Http\JsonResponse
    {
        $department = Department::find($department);

        if (! $department) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid department ID'
            ], 422);
        }

        return response()->json([
            'data' => $department,
            'status' => 'success',
            'message' => 'Department details!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $department): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'payment_code' => 'required|string',
            'type' => 'required|string|in:directorate,division,department,unit',
            'parentId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 200);
        }

        $department = Department::find($department);

        if (! $department) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid department ID'
            ], 422);
        }

        $department->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
            'payment_code' => $request->payment_code,
            'type' => $request->type,
            'parentId' => $request->parentId
        ]);

        return response()->json([
            'data' => $department,
            'status' => 'success',
            'message' => 'Department updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($department): \Illuminate\Http\JsonResponse
    {
        $department = Department::find($department);

        if (! $department) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid department ID'
            ], 422);
        }

        $old = $department;
        $department->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Department deleted successfully!'
        ], 200);
    }
}
