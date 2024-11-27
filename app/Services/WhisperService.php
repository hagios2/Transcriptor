<?php

namespace App\Services;

use App\Http\Requests\WhisperRequest;
use App\Models\Conversation;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhisperService
{
    public function __construct(protected Client $client)
    {
        $this->client = new Client(['base_uri' => 'https://api.openai.com/v1/']);
    }

    public function transcribe(WhisperRequest $request): JsonResponse
    {
        try {
            $user = User::find($request->user_id);
            $conversation = Conversation::query()->where('user_id', $user->id)->first();

            if ($conversation) {
                $conversation = $user->conversation()->create();
            }

            $audioFile = $request->file('audio');
            $fileName = 'audios/' . $audioFile->getClientOriginalName();
            $filePath = $audioFile->getPathname();

            Storage::disk('local')
                ->put("public/$fileName", file_get_contents($filePath), 'public');
            $path = Storage::disk('local')->url($fileName);

            $headers = [
                'Authorization' => 'Bearer '. config('services.openai.key'),
            ];

            $multipart = [
                [
                    'name' => 'file',
                    'contents' => fopen(public_path($path), 'r'),
                    'filename' => $audioFile->getClientOriginalName(),
                ],
                [
                    'name' => 'model',
                    'contents' => 'whisper-1',
                ],
                [
                    'name' => 'response_format',
                    'contents' => 'text',
                ],
            ];

            $response = $this->client->request('POST', 'audio/transcriptions', [
                'multipart' => $multipart,
                'headers' => $headers
            ]);

            $transcription = $response->getBody()->getContents();


            $conversation->trancriptions()->create([
                'audio_path' => $path,
                'trancription' => $transcription
            ]);

            return response()->json([
                'transcription' => $transcription,
                'timestamp' => now()->format('M d, Y H:i a')
            ]);
        } catch (\Exception $exception) {
              Log::error($exception);
              return response()->json([
                'error' => 'Failed to transcribe audio file',
            ], 500);
        }
    }
}
