<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Record;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{

    protected $role;

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $users = User::latest()->get();

        if ($users->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 204);
        }

        return response()->json([
            'data' => UserResource::collection($users),
            'status' => 'success',
            'message' => 'User List'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'staffId' => 'required|string|max:255|unique:records',
            'mobile' => 'required|string|max:255|unique:records',
            'designation' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'company_id' => 'required|integer',
            'gradeLevel' => 'required|integer',
            'dob' => 'required|date',
            'date_joined' => 'required|date',
            'department_id' => 'required|integer',
            'email' => 'required|string|email|max:255',
            'type' => 'required|string|in:permanent,contract,secondment'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make('Password1'),
        ]);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Staff not created!!'
            ], 500);
        }

        $record = Record::create([
            'user_id' => $user->id,
            'staffId' => $request->staffId,
            'department_id' => $request->department_id,
            'gradeLevel' => $request->gradeLevel,
            'company_id' => $request->company_id,
            'mobile' => $request->mobile,
            'designation' => $request->designation,
            'location' => $request->location,
            'dob' => Carbon::parse($request->dob),
            'date_joined' => Carbon::parse($request->date_joined),
            'type' => $request->type,
        ]);

        if (! $record) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Staff record not created!!'
            ], 500);
        }

        $this->role = Role::where('label', 'staff')->first();

        if (! $this->role) {
            $this->role = Role::create([
                'name' => "Staff",
                'label' => "staff",
                'max_slots' => 4000,
                'start_date' => Carbon::now(),
                'isSuper' => false,
                'cannot_expire' => true
            ]);
        }

        $user->roles()->save($this->role);

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'Staff Record Created Successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show($user)
    {
        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function edit($user)
    {
        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, $user)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $user->update([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'surname' => $request->surname,
            'email' => $request->email,
            'type' => $request->type
        ]);

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'User details updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy($user)
    {
        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $user;
        $user->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }
}
