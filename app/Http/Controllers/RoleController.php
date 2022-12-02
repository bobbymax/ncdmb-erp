<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
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
        $roles = Role::latest()->get();

        if ($roles->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 404);
        }

        return response()->json([
            'data' => $roles,
            'status' => 'success',
            'message' => 'Roles list'
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
            'slots' => 'required|integer',
            'type' => 'required|string|max:255|in:roles,groups'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)!:'
            ], 500);
        }

        $role = Role::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'slots' => $request->slots,
            'start' => $request->start != null ? Carbon::parse($request->start) : null,
            'expire' => $request->expire != null ? Carbon::parse($request->expire) : null,
            'isSuper' => $request->isSuper,
            'no_expiration' => $request->no_expiration,
            'type' => $request->type
        ]);

        return response()->json([
            'data' => $role,
            'status' => 'success',
            'message' => 'Role has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($role)
    {
        $role = Role::find($role);

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        return response()->json([
            'data' => $role,
            'status' => 'success',
            'message' => 'Role Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($role)
    {
        $role = Role::find($role);

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        return response()->json([
            'data' => $role,
            'status' => 'success',
            'message' => 'Role Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slots' => 'required|integer',
            'type' => 'required|string|max:255|in:roles,groups'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)!:'
            ], 500);
        }

        $role = Role::find($role);

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        $role->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'slots' => $request->slots,
            'start' => Carbon::parse($request->start),
            'expire' => $request->expire != null ? Carbon::parse($request->expire) : null,
            'isSuper' => $request->isSuper,
            'no_expiration' => $request->no_expiration,
            'type' => $request->type
        ]);

        return response()->json([
            'data' => $role,
            'status' => 'success',
            'message' => 'Role updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($role)
    {
        $role = Role::find($role);

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        $old = $role;
        $role->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Role deleted successfully!'
        ], 200);
    }
}
