<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $query = Request::query()
            ->where('visit_at', '>=', Carbon::now()->startOfDay())
            ->where('visit_at', '<=', Carbon::now()->endOfDay());
        $visits = (clone $query)
            ->groupBy(DB::raw("DATE_FORMAT(visit_at, '%H')"))
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("DATE_FORMAT(visit_at, '%H') as visit_at")
            ])
            ->get();

        $res = array_fill(0, 24, 0);
        foreach ($visits as $visit) {
            $res[intval($visit->visit_at)] = $visit->total;
        }

        return responseSuccess($res);
    }
}