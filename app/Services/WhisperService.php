<?php

namespace App\Services;

use App\Http\Requests\WhisperRequest;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhisperService
{
    public function __construct(protected Client $client)
    {
        $this->client = new Client(['base_uri' => 'https://api.openai.com/v1/']);
    }

    public function transcribe(WhisperRequest $request)
    {
        info('got hit in the service');
        $audioFile = $request->file('audio');
        $filePath = $audioFile->getPathname();
        $convertedPath = storage_path('app/public/converted.mp3');

        try {
              info('got hit in the try ' . $filePath);
            FFMpeg::create([
                'ffmpeg.binaries'  => '/opt/homebrew/bin/ffmpeg',
                'ffprobe.binaries' => '/opt/homebrew/bin/ffprobe',
            ])
                ->open($filePath)
                ->save(new Mp3(), $convertedPath);

             info('after save and create');
            // File conversion successful
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'File conversion failed: ' . $e->getMessage()], 500);
        }


        $headers = [
            'Authorization' => 'Bearer '. config('services.openai.key'),
//            'Content-Type' => 'multipart/form-data'
        ];

        info('file name: ' . $audioFile->getClientOriginalName() . ' and mime: ' . $audioFile->getClientMimeType());

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($convertedPath, 'r'),
//                'filename' => $audioFile->getClientOriginalName(),
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

        $response = json_decode($response->getBody()->getContents());

        info('resp', (array)$response);

        return response()->json([
            'data' => $response,
        ]);
    }
}
