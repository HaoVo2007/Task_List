<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Task::latest()->paginate(5);

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'task' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string',
        ]);

        Task::create($validateData);

        return response()->json([
            'message' => 'You have successfully added',
            'status' => 'success',
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);

        return response()->json([
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);

        $validateData = $request->validate([
            'task' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string',
        ]);

        $task->update($validateData);

        return response()->json([
            'message' => 'You have successfully updated',
            'status' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);

        $task->delete();

        return response()->json([
            'message' => 'You have successfully deleted',
            'status' => 'success',
        ]);
    }

    public function taskCompleted($id) {
        $task = Task::find($id);

        $task->complete = !$task->complete;
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated successfully',
            'completed' => $task->completed
        ]);
    }
}
