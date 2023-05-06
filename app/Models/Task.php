<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Public_tasks as public_task;

class Task extends Model
{
    use HasFactory;
    protected $table = 'task';
    protected $guarded = [''];
    public $timestamps = true;
    protected $casts = [
        'fields' => 'array',
        'data' => 'array',
    ];
    protected $appends = ['isOnline', 'authors'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'is_activated', 'author_created', 'author_updated'
    ];

    public function public_tasks() {
        return $this->hasOne(public_task::class, 'task_id');
    }

    public function scopeGetThisMonth($query, $company_id) {
        $now = Carbon::now();

        $tasks = $query->where('company_id', $company_id)
                       ->whereNull('is_deleted')
                       ->whereBetween('task_date', [Carbon::parse($now->startOfMonth())->format('Y-m-d'), Carbon::parse($now->endOfMonth())->format('Y-m-d')])
                       ->get();

        return $tasks->groupBy('task_date');
    }

    public function scopeGetThisWeek($query, $company_id) {
        $now = Carbon::now();

        return $query->where('company_id', $company_id)->whereNull('is_deleted')->where(function ($q) use ($now) {
            $q->whereBetween('task_date', [Carbon::parse($now->startOfWeek())->format('Y-m-d'), Carbon::parse($now->endOfWeek())->format('Y-m-d')])->
            orWhere(function ($q) use ($now) {
                $q->where('task_date_end', '>=', Carbon::parse($now->startOfWeek())->format('Y-m-d'))->where('task_date', '<', Carbon::parse($now->endOfWeek())->format('Y-m-d'));
            });
        })->get();
    }

    public function scopeGetToday($query, $company_id) {
        $now = Carbon::now();

        return $query->where('company_id', $company_id)->whereNull('is_deleted')->where(function ($q) use ($now) {
            $q->where('task_date', Carbon::now()->format('Y-m-d'))->
            orWhere(function ($q) use ($now) {
                $q->where('task_date_end', '>=', Carbon::parse($now)->format('Y-m-d'))->where('task_date', '<', Carbon::parse($now)->format('Y-m-d'));
            });
        })->get();
    }

    public function scopeGetByDay($query, $company_id, $byDate) {
        $date = Carbon::createFromFormat('d-m-Y', $byDate);

        return $query->where('company_id', $company_id)->whereNull('is_deleted')->where(function ($q) use ($date) {
            $q->where('task_date', Carbon::parse($date)->format('Y-m-d'))->
            orWhere(function ($q) use ($date) {
                $q->where('task_date_end', '>=', Carbon::parse($date)->format('Y-m-d'))->where('task_date', '<', Carbon::parse($date)->format('Y-m-d'));
            });
        })->get();
    }

    public function scopeGetByWeek($query, $company_id, $byDate) {
        $date = Carbon::createFromFormat('d-m-Y', $byDate);

        return $query->where('company_id', $company_id)->whereNull('is_deleted')->where(function ($q) use ($date) {
            $q->whereBetween('task_date', [Carbon::parse($date->startOfWeek())->format('Y-m-d'), Carbon::parse($date->endOfWeek())->format('Y-m-d')])->
            orWhere(function ($q) use ($date) {
                $q->where('task_date_end', '>=', Carbon::parse($date->startOfWeek())->format('Y-m-d'))->where('task_date', '<', Carbon::parse($date->endOfWeek())->format('Y-m-d'));
            });
        })->get();
    }

    public function scopeGetByMonth($query, $company_id, $byDate) {
        $date = Carbon::createFromFormat('d-m-Y', $byDate);

        $tasks = $query->where('company_id', $company_id)
                       ->whereNull('is_deleted')
                       ->whereBetween('task_date', [Carbon::parse($date->startOfMonth())->format('Y-m-d'), Carbon::parse($date->endOfMonth())->format('Y-m-d')])
                       ->get();

        return $tasks->groupBy('task_date');
    }

    public function scopeGetLatestTaskNumber($query, $company_id) {
        return $query->where('company_id', $company_id)->orderBy('task_number', DESC)->first()->task_number ?? 0;
    }

    public function scopeCreateTask($query, $task, $company_id, $data, $user_id) {
        $latestTaskNumber = $query->where('company_id', $company_id)->orderBy('task_number', 'DESC')->first();
        $latestTaskNumber = $latestTaskNumber->task_number ?? 0;

        try {
            return $query->create([
                'title' => ucfirst($task['title']),
                'task_number' => $latestTaskNumber+1,
                'task_date' => $task['startDate'] ? Carbon::createFromFormat('d/m/Y', $task['startDate'])->format('Y-m-d') : null,
                'company_id' => $company_id,
                'task_color' => $task['color'],
                'task_time' => $task['startTime'],
                'task_date_end' => $task['endDate'] ? Carbon::createFromFormat('d/m/Y', $task['endDate'])->format('Y-m-d') : null,
                'task_all_day' => $task['allDay'],
                'task_time_end' => $task['endTime'],
                'fields' => $task['fields'],
                'data' => !empty($data) ? $data : null,
                'author_created' => $user_id,
            ]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function scopeUpdateTask($query, $task, $company_id, $task_id, $data, $user_id) {
        try {
            $updatedTask = $query->where('id', $task_id)->whereNull('is_deleted')->where('company_id', $company_id);
            $updatedTask->update([
                'title' => ucfirst($task['title']),
                'task_date' => $task['startDate'] ? Carbon::createFromFormat('d/m/Y', $task['startDate'])->format('Y-m-d') : null,
                'task_color' => $task['color'],
                'task_time' => $task['startTime'],
                'task_date_end' => $task['endDate'] ? Carbon::createFromFormat('d/m/Y', $task['endDate'])->format('Y-m-d') : null,
                'task_all_day' => $task['allDay'],
                'task_time_end' => $task['endTime'],
                'fields' => $task['fields'],
                'data' => !empty($data) ? $data : null,
                'author_updated' => $user_id,
            ]);

            return $updatedTask->first();

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function scopeSearchApi($query, $q, $searchcriteria, $company_id, $limit = 50) {
        try {
            $tasks = $query->whereNull('is_deleted')->where('company_id', $company_id);

            if (is_numeric($q)) {
                $tasks->where('task_number', $q);
            } else {
                $tasks->where('title', 'like', '%'.$q.'%');
            }

            if ($searchcriteria) {
                if ($searchcriteria['included']) {
                    foreach ($searchcriteria['included'] as $included) {
                        $tasks = $tasks->whereJsonContains('data', $included['id']);
                    }
                }

                if ($searchcriteria['excluded']) {
                    foreach ($searchcriteria['excluded'] as $excluded) {
                        $tasks = $tasks->whereJsonDoesntContain('data', $excluded['id']);
                    }
                }

                if ($searchcriteria['colors']) {
                    $tasks = $tasks->whereIn('task_color', $searchcriteria['colors']);
                }

                if ($searchcriteria['dates']) {
                    $tasks = $tasks->whereBetween('task_date', [Carbon::parse($searchcriteria['dates']['start'])->format('Y-m-d'), Carbon::parse($searchcriteria['dates']['end'])->format('Y-m-d')]);
                }
            }

            $tasks = $tasks->limit($limit)->orderBy('task_date', 'DESC')->get();

            if ($tasks->count()) {
                return $tasks;
            } else {
                return null;
            }

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getIsOnlineAttribute() {
        return $this->hasOne('App\Models\Public_tasks', 'task_id')->first();
    }

    public function getAuthorsAttribute() {
        $authors = [];
        $authors['created_by'] = $this->belongsTo('App\Models\User', 'author_created')->select('name')->first();
        $authors['updated_by'] = $this->belongsTo('App\Models\User', 'author_updated')->select('name')->first();
        
        return $authors;
    }

    public function getDataAttribute($value) {
        if (empty($value)) {
            return null;
        }

        $data = [];
        $ids = $this->fromJson($value, true);
        foreach($ids as $id) {
            $directories = Directory::where('company_id', $this->company_id)->get();
            if ($directories) {
                foreach($directories as $directory) {
                    if ($directory->data) {                        
                        foreach($directory->data as $singleData) {
                            if ($singleData['id'] === $id) {
                                $singleData['directory_title'] = $directory->title;
                                $singleData['directory_id'] = $directory->id;
                                if ($directory->is_deleted === 1) {
                                    $singleData['is_deleted'] = true;
                                }
                                $data[] = $singleData;
                            }
                        }
                    }
                }
            }
        }

        return !empty($data) ? $data : null;
    }
}
