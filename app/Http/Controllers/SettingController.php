<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
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
        $settings = Setting::latest()->get();

        if ($settings->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $settings,
            'status' => 'success',
            'message' => 'Setting List'
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
            'display_name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:settings',
            'input_type' => 'required|string',
            'group' => 'required|string|in:site,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $setting = Setting::create([
            'display_name' => $request->display_name,
            'key' => $request->key,
            'input_type' => $request->input_type,
            'group' => $request->group,
            'order' => $request->order ?? 0,
            'details' => $request->details ?? null
        ]);

        return response()->json([
            'data' => $setting,
            'status' => 'success',
            'message' => 'Setting value created successfully!'
        ], 201);
    }

    public function configuration(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'state' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $settings = Setting::latest()->get();


        foreach ($settings as $key => $setting) {
            if (isset($request->state[$setting->key])) {
                $setting->value = $request->state[$setting->key];
                $setting->save();
            }
        }

        return response()->json([
            'data' => $settings,
            'status' => 'success',
            'message' => 'Settings value updated successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  $setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($setting): \Illuminate\Http\JsonResponse
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $setting,
            'status' => 'success',
            'message' => 'Setting Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($setting): \Illuminate\Http\JsonResponse
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $setting,
            'status' => 'success',
            'message' => 'Setting Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $setting): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'input_type' => 'required|string',
            'group' => 'required|string|in:site,admin',
            'key' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $setting->update([
            'display_name' => $request->display_name,
            'key' => $request->key,
            'input_type' => $request->input_type,
            'group' => $request->group,
            'order' => $request->order ?? 0,
            'value' => $request->value ?? null,
            'details' => $request->details ?? null
        ]);

        return response()->json([
            'data' => $setting,
            'status' => 'success',
            'message' => 'Setting details updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($setting): \Illuminate\Http\JsonResponse
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $setting;
        $setting->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Setting detail deleted successfully'
        ], 200);
    }
}
