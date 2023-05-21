<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Directory;
use App\Models\Fields;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataDirectoryController extends Controller
{
    public function update(Request $request) {
        // Get the directory
        $data = $request->data ? array_values($request->data) : null;

        $directory = Directory::where('id', $request->id)->where('company_id', $request->get_company_id)->first();
        $identification = array_filter($directory->fields, function ($object) {
            return isset($object['primary']) && $object['primary'] === true;
        });

        // Set the identification value
        if ($data) {
            foreach ($data as $key => $d) {
                $data[$key]['identification'] = array_values($identification[0])[1];
            }   
        }

        $directory->data = $data;
        $directory->save();

        if ($directory) {
            return response()->json([
                'message' => 'Field updated successfully',
                'updated' => $directory,
            ], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Bad request',], Response::HTTP_BAD_REQUEST);
        }
    }

    public function single(Request $request) {
        $directory = Directory::where('company_id', $request->get_company_id)->where('id', $request->id)->whereNull('is_deleted')->first();
        $data = collect($directory->data)->firstWhere('id', $request->data_id);

        $previous_week = Task::whereNull('is_deleted')->where('company_id', $request->get_company_id)
                                                      ->whereJsonContains('data', $data['id'])
                                                      ->whereBetween('task_date', [Carbon::now()->addWeek(-1)->startOfWeek()->format('Y-m-d'), Carbon::now()->addWeek(-1)->endOfWeek()->format('Y-m-d')])
                                                      ->orderBy('task_date', 'ASC')->get();

        $next_week = Task::whereNull('is_deleted')->where('company_id', $request->get_company_id)
                             ->whereJsonContains('data', $data['id'])
                             ->whereBetween('task_date', [Carbon::now()->addWeek(1)->startOfWeek()->format('Y-m-d'), Carbon::now()->addWeek(1)->endOfWeek()->format('Y-m-d')])
                             ->orderBy('task_date', 'ASC')->get();

        $current_week = Task::whereNull('is_deleted')->where('company_id', $request->get_company_id)
                         ->whereJsonContains('data', $data['id'])
                         ->whereBetween('task_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                         ->orderBy('task_date', 'ASC')->get();

        return response()->json([
            'directory' => $directory,
            'data' => $data,
            'tasks' => [
                'current_week' => $current_week,
                'previous_week' => $previous_week,
                'next_week' => $next_week
            ],
        ]);
    }
}
