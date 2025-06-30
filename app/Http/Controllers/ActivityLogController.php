<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'description'  => 'sometimes|string',
            'subject_type' => 'sometimes|string',
            'date_from'    => 'nullable|date_format:d-m-Y',
            'date_to'      => 'nullable|date_format:d-m-Y|after_or_equal:date_from',
        ]);

        $activities = Activity::with(['causer', 'subject'])
            ->when($request->description, function($query, $description) {
                return $query->where('description', 'like', "%{$description}%");
            })
            ->when($request->subject_type, function($query, $type) {
                return $query->where('subject_type', $type);
            })
            ->when($request->date_from, function($query, $date) {
                $from = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->date_to, function($query, $date) {
                $to = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                return $query->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->paginate(20);

        // Get unique subject types for filter dropdown
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->filter();

        return view('activity-logs.index', compact('activities', 'subjectTypes'));
    }


    public function getActivityLogs(Request $request)
    {
        $request->validate([
            'description'  => 'sometimes|string',
            'subject_type' => 'sometimes|string',
            'date_from'    => 'nullable|date_format:d-m-Y',
            'date_to'      => 'nullable|date_format:d-m-Y|after_or_equal:date_from',
        ]);

        $logs = Activity::with(['causer'])
            ->when($request->description, function($query, $description) {
                return $query->where('description', 'like', '%'.$description.'%');
            })
            ->when($request->subject_type, function($query, $type) {
                return $query->where('subject_type', $type);
            })
            ->when($request->date_from, function($query, $date) {
                $from = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->date_to, function($query, $date) {
                $to = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                return $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($logs)
            ->addIndexColumn()
            ->editColumn('description', function($log) {
                return ucfirst($log->description);
            })
            ->editColumn('causer', function($log) {
                return $log->causer ? $log->causer->name : 'System';
            })
            ->editColumn('subject', function($log) {
                return $log->subject ? class_basename($log->subject).' #'.$log->subject->id : '';
            })
            ->editColumn('created_at', function($log) {
                return $log->created_at->format('d-m-Y h:i A');
            })
            ->addColumn('action', function($log) {
                return '<a href="'.route('admin.activity-logs.show', $log->id).'" class="btn btn-soft-primary btn-sm">
                    <i class="bi bi-eye"></i> View
                </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $activity = Activity::with(['causer', 'subject'])
            ->findOrFail($id);

        return view('activity-logs.show', compact('activity'));
    }
}