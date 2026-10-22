<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FaceRecognitionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.face_recognition.url')
            ?: env('FACE_RECOGNITION_SERVICE_URL', 'http://face-recognition-service:8001');
    }

    public function register(string $nis, string $imagePath): array
    {
        try {
            $response = Http::timeout(60)
                ->attach('file', file_get_contents($imagePath), 'face.jpg')
                ->post($this->baseUrl . '/register', ['nis' => $nis]);

            return [
                'success' => $response->successful() && ($response->json('success') ?? false),
                'message' => $response->json('message') ?? ($response->successful() ? 'ok' : $response->body()),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Layanan wajah tidak tersedia: ' . $e->getMessage()];
        }
    }

    public function recognize(string $imagePath): array
    {
        try {
            $response = Http::timeout(60)
                ->attach('file', file_get_contents($imagePath), 'face.jpg')
                ->post($this->baseUrl . '/recognize');

            $data = $response->json() ?? [];

            return [
                'success' => $response->successful() && ($data['success'] ?? false),
                'nis' => $data['nis'] ?? null,
                'confidence' => $data['confidence'] ?? null,
                'distance' => $data['distance'] ?? null,
                'message' => $data['message'] ?? ($response->successful() ? 'ok' : $response->body()),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Layanan wajah tidak tersedia: ' . $e->getMessage()];
        }
    }
}