<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function getNowMonth(Request $request) {
        // Get week days and date
        $month = DateHelper::getMonth();
        $tasks = Task::getThisMonth($request->get_company_id);

        return response()->json([
            'month' => $month,
            'tasks' => $tasks,
            'year' => DateHelper::getYear(),
        ]);
    }

    public function getNowWeek(Request $request) {
        // Get week days and date
        $week = DateHelper::getWeek();
        $tasks = Task::getThisWeek($request->get_company_id);

        return response()->json([
            'week' => $week,
            'tasks' => $tasks,
            'week_number' => DateHelper::getWeekNumber(),
            'year' => DateHelper::getYear(),
        ]);
    }

    public function getToday(Request $request) {
        // Get today
        $day = DateHelper::getDay();
        $tasks = Task::getToday($request->get_company_id);

        return response()->json([
            'day' => $day,
            'tasks' => $tasks,
        ]);
    }

    public function getByDay(Request $request) {
        // Get today
        $day = DateHelper::getDay($request->day);
        $tasks = Task::getByDay($request->get_company_id, $request->day);

        return response()->json([
            'day' => $day,
            'tasks' => $tasks,
        ]);
    }

    public function getByWeek(Request $request) {
        // Get week days and date
        $week = DateHelper::getWeek($request->week);
        $tasks = Task::getByWeek($request->get_company_id, $request->week);

        return response()->json([
            'week' => $week,
            'tasks' => $tasks,
            'week_number' => DateHelper::getWeekNumber($request->week),
            'year' => DateHelper::getYear($request->week),
        ]);
    }

    public function getByMonth(Request $request) {
        // Get week days and date
        $month = DateHelper::getMonth($request->month);
        $tasks = Task::getByMonth($request->get_company_id, $request->month);

        return response()->json([
            'month' => $month,
            'tasks' => $tasks,
            'year' => DateHelper::getYear($request->month),
        ]);
    }
}
