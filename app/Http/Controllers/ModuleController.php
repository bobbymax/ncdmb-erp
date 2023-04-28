<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModuleController extends Controller
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
        $modules = Module::latest()->get();

        if ($modules->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => ModuleResource::collection($modules),
            'status' => 'success',
            'message' => 'Modules List'
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
            'code' => 'required|string|max:255',
            'url' => 'required|string',
            'type' => 'required|string|in:application,module,page',
            'parentId' => 'required',
            'roles' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
            'path' => $request->path,
            'icon' => $request->icon,
            'parentId' => $request->parentId,
            'url' => $request->url,
            'type' => $request->type,
        ]);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong!!'
            ], 500);
        }

        if ($request->roles) {
            foreach($request->roles as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $module->roles()->save($role);
                }
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($module): \Illuminate\Http\JsonResponse
    {
        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($module): \Illuminate\Http\JsonResponse
    {
        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $module): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'url' => 'required|string',
            'type' => 'required|string|in:application,module,page',
            'parentId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $module->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'path' => $request->path,
            'icon' => $request->icon,
            'code' => $request->code,
            'parentId' => $request->parentId,
            'type' => $request->type,
            'url' => $request->url
        ]);

        foreach ($request->roles as $val) {
            $role = Role::find($val);

            if ($role) {
                $module->roles()->save($role);
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($module)
    {
        $module = Module::find($module);
        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $module;
        $module->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Module deleted successfully!'
        ], 200);
    }
}
