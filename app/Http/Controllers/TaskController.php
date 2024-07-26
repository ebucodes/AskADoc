<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::where('user_id', auth()->user()->id)->get();
        return response()->json(['success' => true, 'data' => $tasks]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $task = auth()->user()->tasks()->create($data);

            return response()->json(['success' => true, 'message' => 'Task Created Successfully', 'data' => $task]);
        } catch (Exception $error) {

            return response()->json(['success' => false, 'message' => 'Failed to Create Task', "error" => $error->getMessage()]);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show($task): JsonResponse
    {
        try {
            if (!$task = Task::find($task)) {
                throw new Exception('Can not find task.');
            }

            return response()->json(['success' => true, 'data' => $task]);
        } catch (Exception $error) {

            return response()->json(['success' => false, 'message' => 'Failed to Find Task', "error" => $error->getMessage()]);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $task): JsonResponse
    {
        try {
            if (!$task = Task::find($task)) {
                throw new Exception('Can not find task.');
            }
            $data = $request->validated();

            $task->title = $data['title'];
            $task->description = $data['description'];
            $task->save();

            return response()->json(['success' => true, 'message' => 'Task Updated Successfully', 'data' => $task]);
        } catch (Exception $error) {

            return response()->json(['success' => false, 'message' => 'Failed to Updated Task', "error" => $error->getMessage()]);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($task): JsonResponse
    {
        try {
            if (!$task = Task::find($task)) {
                throw new Exception('Can not find task.');
            }

            $task->delete();

            return response()->json(['success' => true, 'message' => 'Task Deleted Successfully']);

        } catch (Exception $error) {

            return response()->json(['success' => false, 'message' => 'Failed to Delete Task', "error" => $error->getMessage()]);

        }

    }
}
