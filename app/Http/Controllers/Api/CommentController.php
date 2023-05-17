<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index (Request $request) {
        $task = Task::where('id', $request->task_id)->select('company_id')->first();

        if ($task->company_id === $request->get_company_id) {
            $comments = Comment::where('task_id', $request->task_id)->orderBy('id', 'DESC')->get();

            return response()->json([
                'comments' => $comments
            ], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
    }

    public function create(Request $request) {
        $comment = Comment::create([
            'user_id' => $request->get_user_id,
            'task_id' => $request->task_id,
            'comment' => $request->comment,
        ]);

        if ($comment) {
            return response()->json([
                'comment' => $comment,
            ], Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }
}
