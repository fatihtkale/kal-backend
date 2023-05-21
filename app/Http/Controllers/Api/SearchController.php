<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Directory;
use App\Models\Fields;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search (Request $request) {
        $tasks = Task::searchApi($request->q, $request->searchcriteria, $request->get_company_id);

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    public function dataSearch (Request $request) {
        $data = $request['data'];
        $now = Carbon::now();
        $start_date = $request['dates'] && $request['dates']['start'] ? $request['dates']['start'] : null;
        $end_date = $request['dates'] && $request['dates']['end'] ? $request['dates']['end'] : null;

        $tasks = Task::whereNull('is_deleted')->where('company_id', $request->get_company_id)->whereJsonContains('data', $data['id']);

        if ($start_date) {
            $tasks->whereBetween('task_date', [Carbon::parse($start_date)->format('Y-m-d'), Carbon::parse($end_date)->format('Y-m-d')]);
        } else {
            $tasks->where('task_date', Carbon::parse($now)->format('Y-m-d'));
        }

        $tasks = $tasks->get();

        return response()->json([
            'tasks' => $tasks->groupBy('task_date')
        ]);
    }
}
