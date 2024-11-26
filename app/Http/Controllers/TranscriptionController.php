<?php

namespace App\Http\Controllers;

use App\Http\Requests\WhisperRequest;
use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;

class TranscriptionController extends Controller
{
    public function __construct(protected WhisperService $whisperService)
    {

    }


    public function transcribe(WhisperRequest $request): JsonResponse
    {
        return $this->whisperService->transcribe($request);
    }
}
