<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $type = $request->filter_type;

        $query = Task::query();

        if ($type == 0) {
            $query->where('complete', '=', 0);
        } else if ($type == 1) {
            $query->where('complete', '=', 1);
        } else if ($type == 2) {
            $query->orderByRaw("FIELD(priority, 2, 1, 0)");
        }

        $data = $query->latest()->paginate(6);

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
            'deadline' => 'required|date|after_or_equal:today'
        ]);

        $deadline = Carbon::parse($validateData['deadline']);

        $reminder = $deadline->copy()->subHour(2);

        Task::create([
            'task' => $validateData['task'],
            'category' => $validateData['category'],
            'priority' => $validateData['priority'],
            'deadline' => $deadline,
            'reminder' => $reminder
        ]);

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

    public function reminderTask() {
        $now = now()->setTimezone('Asia/Ho_Chi_Minh');

        $data = Task::where('reminder', '<=', $now) 
                ->where('deadline', '>=', $now) 
                ->where('complete', '=', 0)    
                ->get();
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function checkNotification($id) {
        $data = Task::find($id);

        if ($data->check_notification == 1) {
            return;
        } else {
            $data->check_notification = 1;
            $data->save();
            return response()->json([
                'status' => 'sucesss', 
            ]);
        }
    }
}
