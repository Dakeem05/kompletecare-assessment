<?php

namespace App\Http\Controllers;

use App\Services\MonitorService;
use Exception;

class MonitorController extends Controller
{
    public function __construct(private MonitorService $monitorService) {}

    public function index()
    {
        try {
            $response = $this->monitorService->getMonitors();
            return response()->json([
                'data' => $response
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // publi
}
