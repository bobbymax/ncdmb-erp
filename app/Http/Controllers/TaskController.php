<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Milestone;
use App\Models\Responsibility;
use App\Models\Target;
use App\Models\Task;
use App\Models\Timeline;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $taskable;

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
        $tasks = Task::latest()->get();

        if ($tasks->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $tasks,
            'status' => 'success',
            'message' => 'List of Tasks'
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
            'description' => 'required|min:4',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high,very-high',
            'taskable_id' => 'required|integer',
            'taskable_type' => 'required|string|in:project,responsibility,department,staff',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $this->taskable = $this->getHolder($request->taskable_type, $request->taskable_id);

        $task = new Task;
        $task->user_id = auth()->user()->id;
        $task->due_date = Carbon::parse($request->due_date);
        $task->description = $request->description;
        $task->priority = $request->priority;
        $this->taskable->tasks()->save($task);


        $timeline = new Timeline;
        $task->timeline()->save($timeline);

        return response()->json([
            'data' => $task,
            'status' => 'success',
            'message' => 'Task created successfully!!'
        ], 201);
    }

    public function getHolder($type, $id)
    {
        return match ($type) {
            "responsibility" => Responsibility::find($id),
            "department" => Department::find($id),
            "target" => Target::find($id),
            "milestone" => Milestone::find($id),
            default => User::find($id)
        };
    }

    /**
     * Display the specified resource.
     *
     * @param  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($task): \Illuminate\Http\JsonResponse
    {
        $task = Task::find($task);

        if (! $task) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $task,
            'status' => 'success',
            'message' => 'Task details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($task): \Illuminate\Http\JsonResponse
    {
        $task = Task::find($task);

        if (! $task) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $task,
            'status' => 'success',
            'message' => 'Task details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $task): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|min:4',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high,very-high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $task = Task::find($task);

        if (! $task) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $task->due_date = Carbon::parse($request->due_date);
        $task->description = $request->description;
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            'data' => $task,
            'status' => 'success',
            'message' => 'Task updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($task): \Illuminate\Http\JsonResponse
    {
        $task = Task::find($task);

        if (! $task) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $task;
        $task->timeline()->delete();
        $task->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Task deleted successfully!!'
        ], 200);
    }
}
