<?php

namespace App\Services;

use App\Models\FonnteDevice;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected string $notificationServiceUrl;

    public function __construct()
    {
        // Mengambil URL Notification Service dari config/env (default ke container notification-service:5000)
        $this->notificationServiceUrl = config('services.notification.url') 
            ?: env('NOTIFICATION_SERVICE_URL', 'http://notification-service:5000');
    }

    public function isEnabled(): bool
    {
        return Setting::get('fonnte_enabled', false) && $this->hasAvailableDevices();
    }

    public function hasAvailableDevices(): bool
    {
        return FonnteDevice::available()->exists();
    }

    public function getNextDevice(): ?FonnteDevice
    {
        return FonnteDevice::available()
            ->byPriority()
            ->first();
    }

    /**
     * Send WhatsApp message via Notification Microservice
     */
    public function sendMessage(string $phoneNumber, string $message, ?FonnteDevice $device = null, ?int $kelasId = null): array
    {
        try {
            $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

            // Memanggil Notification Service (Microservice) via REST API
            $response = Http::timeout(15)
                ->post($this->notificationServiceUrl . '/api/send-presence-notification', [
                    'phone' => $phoneNumber,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Periksa jika Fonnte mengembalikan response status: false (misal device disconnect/target invalid)
                if (isset($responseData['data']['status']) && $responseData['data']['status'] === false) {
                    $reason = $responseData['data']['reason'] ?? $responseData['data']['detail'] ?? 'Ditolak oleh Fonnte API';
                    return [
                        'success' => false,
                        'message' => 'Fonnte Error: ' . $reason,
                        'data' => $responseData
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Message sent via Notification Service',
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to reach Notification Service: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Microservice Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send Recap Absensi Notification via Notification Microservice (Tugas Dosen)
     */
    public function sendRecapNotification(string $phoneNumber, string $recapText, string $period = 'Harian'): array
    {
        try {
            $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

            $response = Http::timeout(20)
                ->post($this->notificationServiceUrl . '/api/send-recap-notification', [
                    'phone' => $phoneNumber,
                    'recapData' => $recapText,
                    'period' => $period,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Recap notification sent via Notification Service',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send recap via Notification Service'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Microservice Error: ' . $e->getMessage()
            ];
        }
    }

    public function testDevice(FonnteDevice $device): array
    {
        if (empty($device->api_key)) {
            return [
                'success' => false,
                'message' => 'API Key is missing'
            ];
        }

        try {
            $targetNumber = $this->normalizePhoneNumber($device->phone_number ?? '');

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $device->api_key,
                ])
                ->post('https://api.fonnte.com/validate', [
                    'target' => $targetNumber
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'message' => 'Connection to Fonnte API valid',
                    'data' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Validation failed'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function testAllDevices(): array
    {
        $devices = FonnteDevice::active()->get();
        $results = [];

        foreach ($devices as $device) {
            $results[$device->id] = $this->testDevice($device);
            sleep(1);
        }

        return $results;
    }

    public function sendPresensiNotification($presensi, string $type = 'checkin'): array
    {
        try {
            $presensi->load(['siswa', 'kelas.jurusan']);
            
            $siswa = $presensi->siswa;
            $kelas = $presensi->kelas;
            
            if (!$siswa || empty($siswa->parent_phone)) {
                return [
                    'success' => false,
                    'message' => 'Student or parent phone number missing',
                    'skipped' => true
                ];
            }

            $templateKey = $type === 'checkin' 
                ? 'fonnte_message_template_checkin' 
                : 'fonnte_message_template_checkout';
            
            $template = Setting::get($templateKey, $this->getDefaultTemplate($type));
            $className = $kelas ? $kelas->nama_kelas : '-';

            $variables = [
                'parent_name' => $this->getParentName($siswa->name),
                'student_name' => $siswa->name,
                'nis' => $siswa->nis ?? '-',
                'class_name' => $className,
                'date' => $presensi->tanggal_presensi->format('d F Y'),
            ];

            if ($type === 'checkin') {
                $variables['checkin_time'] = $presensi->waktu_checkin 
                    ? $presensi->waktu_checkin->format('H:i') 
                    : '-';
                $variables['status'] = '✅ MASUK';
            } else {
                $variables['checkin_time'] = $presensi->waktu_checkin 
                    ? $presensi->waktu_checkin->format('H:i') 
                    : '-';
                $variables['checkout_time'] = $presensi->waktu_checkout 
                    ? $presensi->waktu_checkout->format('H:i') 
                    : '-';
                $variables['status'] = '🏠 PULANG';
            }

            $message = $this->replaceVariables($template, $variables);

            return $this->sendMessage($siswa->parent_phone, $message);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }

    protected function getParentName(string $studentName): string
    {
        $firstName = explode(' ', $studentName)[0];
        return "Orang Tua " . $firstName;
    }

    protected function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    protected function getDefaultTemplate(string $type = 'checkin'): string
    {
        if ($type === 'checkin') {
            return "Assalamualaikum Bapak/Ibu\n\n" .
                   "Kami informasikan bahwa:\n\n" .
                   "*Nama:* {student_name}\n" .
                   "*NIS:* {nis}\n" .
                   "*Kelas:* {class_name}\n" .
                   "*Status:* ✅ MASUK\n" .
                   "*Waktu:* {checkin_time}\n" .
                   "*Tanggal:* {date}\n\n" .
                   "Terima kasih.\n\n" .
                   "_Sistem Presensi_";
        }

        return "Assalamualaikum Bapak/Ibu\n\n" .
               "Kami informasikan bahwa:\n\n" .
               "*Nama:* {student_name}\n" .
               "*NIS:* {nis}\n" .
               "*Kelas:* {class_name}\n" .
               "*Status:* 🏠 PULANG\n" .
               "*Check-in:* {checkin_time}\n" .
               "*Check-out:* {checkout_time}\n" .
               "*Tanggal:* {date}\n\n" .
               "Terima kasih.\n\n" .
               "_Sistem Presensi_";
    }

    public function getDeviceStatistics(): array
    {
        return [
            'total_devices' => FonnteDevice::count(),
            'active_devices' => FonnteDevice::active()->count(),
            'available_devices' => FonnteDevice::available()->count(),
            'connected_devices' => FonnteDevice::where('status', 'connected')->count(),
            'total_sent' => FonnteDevice::sum('sent_count'),
        ];
    }
}