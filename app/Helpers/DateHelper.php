<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function getMonth(String $byDate = null)
    {
        // @TODO remove add hour because timezone and summer hour
        if ($byDate) {
            $timestamp = Carbon::createFromFormat('d-m-Y', $byDate)->addHour()->timestamp;
            $date = Carbon::parse($timestamp)->startOfMonth();
        } else
        {
            $timestamp = Carbon::now()->addHour()->timestamp;
            $date = Carbon::parse($timestamp)->startOfMonth();
        }

        $endDate = Carbon::parse($date)->startOfMonth()->endOfMonth();
        $month = [];

        for ($date = Carbon::parse($date); $date->lte($endDate); $date->addDay()) {
            // Store the day as a timestamp
            $month[] = $date->timestamp;
            // Do something with the timestamp...
        }

        return $month;
    }

    public static function getWeek(String $byDate = null)
    {
        // @TODO remove add hour because timezone and summer hour
        if ($byDate) {
            $timestamp = Carbon::createFromFormat('d-m-Y', $byDate)->addHour()->timestamp;
            $date = Carbon::parse($timestamp);
        } else
        {
            $timestamp = Carbon::now()->addHour()->timestamp;
            $date = Carbon::parse($timestamp);
        }
        $week = [];

        for ($i=0; $i <7 ; $i++) {
            $week[] = $date->startOfWeek()->addDay($i)->timestamp;
        }

        return $week;
    }

    public static function getDay(String $byDate = null)
    {
        // @TODO remove add hour because timezone and summer hour
        return $byDate ? Carbon::createFromFormat('d-m-Y', $byDate)->addHour()->timestamp : Carbon::now()->addHour()->timestamp;
    }

    public static function getWeekNumber(String $byDate = null) {
        if (isset($byDate)) {
            $weekNumber = Carbon::createFromFormat('d-m-Y', $byDate)->weekOfYear;
        } else {
            $weekNumber = Carbon::now()->weekOfYear;
        }
        return $weekNumber;
    }

    public static function getYear(String $byDate = null) {
        if (isset($byDate)) {
            $year = Carbon::createFromFormat('d-m-Y', $byDate)->year;
        } else {
            $year = Carbon::now()->year;
        }
        return $year;
    }
}
