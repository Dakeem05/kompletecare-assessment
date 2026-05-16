<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMonitorUrlRequest;
use App\Http\Resources\MonitorCheckResource;
use App\Http\Resources\MonitorResource;
use App\Services\MonitorService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MonitorController extends Controller
{
    public function __construct(private MonitorService $monitorService) {}

    public function index()
    {
        try {
            $response = $this->monitorService->getMonitors();
            return response()->json([
                'data' => MonitorResource::collection($response)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CreateMonitorUrlRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            $response = $this->monitorService->createMonitor($validatedData);
            
            return response()->json([
                'data' => MonitorResource::make($response)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMonitorHistory(int $monitorId)
    {
        try {
            $response = $this->monitorService->getMonitorHistory($monitorId);
            $resource = MonitorCheckResource::collection($response);
            
            return response()->json([
                'data' => $resource,
                'meta' => [
                    'current_page' => $response->currentPage(),
                    'per_page' => $response->perPage(),
                    'total' => $response->total(),
                ]
            ], 200);
        } 
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
