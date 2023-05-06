<?php

namespace App\Http\Controllers\Api;

use App\Helpers\TaskHelper;
use App\Http\Controllers\Controller;
use App\Models\Public_tasks;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function create(Request $request) {
        $data = TaskHelper::getDataIDs($request->task['data']);
        $task = Task::createTask($request->task, $request->get_company_id, $data, $request->get_user_id);

        if ($task) {
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task,
            ],Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => 'Error',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request) {
        $data = TaskHelper::getDataIDs($request->task['data']);
        $task = Task::updateTask($request->task, $request->get_company_id, $request->id, $data, $request->get_user_id);

        if ($task) {
            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task,
            ],Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Error',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Request $request) {
        $task = Task::where('id', $request->id)->where('company_id', $request->get_company_id)->update([
            'is_deleted' => 1
        ]);

        if ($task) {
            return response()->json([
                'message' => 'Task deleted successfully',
                'task' => $task,
            ],Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Error',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function setPublic(Request $request) {
        $validateOwner = Task::where('company_id', $request->get_company_id)->where('id', $request->id)->count();

        $token = Str::random(30);
        $token .= $request->id;
        $token .= Str::random(30);

        if ($validateOwner) {
            $public_task = Public_tasks::firstOrCreate(
                [ 'task_id' => $request->id ],
                [ 'token' => $token, 'task_id' => $request->id ]
            );

            if ($public_task) {
                return response()->json([
                    'message' => 'Published task successfully',
                    'token' => $public_task->token,
                ],Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Error',
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return response()->json([
                'message' => 'Bad request',
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    public function removePublic(Request $request) {
        $validateOwner = Task::where('company_id', $request->get_company_id)->where('id', $request->id)->count();

        if ($validateOwner) {
            $removed_public_task = Public_tasks::where('task_id', $request->id)->delete();

            if ($removed_public_task) {
                return response()->json([
                    'message' => 'Removed published task successfully',
                    'deleted' => $removed_public_task,
                ],Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Error',
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function publicTask(Request $request) {
        $task = Task::whereHas('public_tasks', function ($query) use ($request) {
            $query->where('token', $request->token);
        })->first();

        if ($task) {
            return response()->json([
                'task' => $task
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
