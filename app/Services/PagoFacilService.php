<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PagoFacilService
{
    private $tokenService;
    private $tokenSecret;
    private $clientCode;
    private $phoneNumber;
    private $apiUrl;
    private $accessToken;

    public function __construct()
    {
        $this->tokenService = config('services.pagofacil.token_service');
        $this->tokenSecret = config('services.pagofacil.token_secret');
        $this->clientCode = config('services.pagofacil.client_code');
        $this->phoneNumber = config('services.pagofacil.phone_number');
        $this->apiUrl = config('services.pagofacil.api_url');
        
        // Log para debug de configuración
        Log::info('PagoFácil: Configuración cargada', [
            'api_url' => $this->apiUrl,
            'client_code' => $this->clientCode,
            'phone_number' => $this->phoneNumber,
            'token_service_length' => strlen($this->tokenService ?? ''),
            'token_secret_length' => strlen($this->tokenSecret ?? '')
        ]);
    }

    /**
     * Obtener token de acceso
     */
    public function authenticate(): bool
    {
        try {
            Log::info('PagoFácil: Intentando autenticación', [
                'url' => $this->apiUrl . '/login',
                'token_service_set' => !empty($this->tokenService),
                'token_secret_set' => !empty($this->tokenSecret)
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret' => $this->tokenSecret
                ])
                ->post($this->apiUrl . '/login');
            
            $responseData = $response->json();
            
            Log::info('PagoFácil: Respuesta de autenticación', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_data' => $responseData
            ]);

            if ($response->successful() && $responseData && isset($responseData['values']['accessToken'])) {
                $this->accessToken = $responseData['values']['accessToken'];
                Log::info('PagoFácil: Autenticación exitosa', [
                    'access_token_set' => !empty($this->accessToken)
                ]);
                return true;
            }

            Log::error('PagoFácil: Error en autenticación', [
                'response_data' => $responseData,
                'status_code' => $response->status()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('PagoFácil: Excepción en autenticación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Generar QR de pago
     */
    public function generateQR(array $orderData): array
    {
        if (!$this->accessToken && !$this->authenticate()) {
            throw new Exception('No se pudo autenticar con PagoFácil');
        }

        try {
            $payload = [
                'paymentMethod' => 4, // QR
                'clientName' => $orderData['client_name'],
                'documentType' => $orderData['document_type'] ?? 1,
                'documentId' => $orderData['document_id'],
                'phoneNumber' => $this->phoneNumber, // Usar el número configurado en .env
                'email' => $orderData['email'],
                'paymentNumber' => $orderData['payment_number'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'] ?? 2,
                'clientCode' => $orderData['client_code'], // ID del cliente en tu sistema
                'callbackUrl' => $orderData['callback_url'],
                'orderDetail' => $orderData['order_details'] // Array de productos
            ];

            Log::info('PagoFácil: Enviando request generate-qr', [
                'payload' => $payload,
                'url' => $this->apiUrl . '/generate-qr'
            ]);

            $response = Http::timeout(60) // Aumentar timeout a 60 segundos
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl . '/generate-qr', $payload);

            Log::info('PagoFácil: Response recibido', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'response_body' => $response->body(), // Log completo del body
                'response_headers' => $response->headers() // Headers de respuesta
            ]);

            $responseData = $response->json();

            Log::info('PagoFácil: Respuesta generate-qr', [
                'status_code' => $response->status(),
                'response' => $responseData,
                'has_qr_base64' => isset($responseData['values']['qrBase64']),
                'qr_length' => isset($responseData['values']['qrBase64']) ? strlen($responseData['values']['qrBase64']) : 0
            ]);

            if ($response->successful() && isset($responseData['error']) && $responseData['error'] === 0) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'qr_base64' => $responseData['values']['qrBase64'] ?? null, // Corregir ruta del QR Base64
                    'transaction_id' => $responseData['values']['transactionId'] ?? null, // Corregir ruta del transaction ID
                    'access_token' => $this->accessToken
                ];
            }

            throw new Exception('Error en la respuesta de PagoFácil: ' . ($responseData['message'] ?? 'Error desconocido'));

        } catch (Exception $e) {
            Log::error('PagoFácil: Error al generar QR', [
                'error' => $e->getMessage(),
                'order_data' => $orderData,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar estado del pago
     */
    public function verifyPayment(string $transactionId): array
    {
        if (!$this->accessToken && !$this->authenticate()) {
            throw new Exception('No se pudo autenticar con PagoFácil');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl . '/verify-payment', [
                    'transactionId' => $transactionId
                ]);

            $responseData = $response->json();

            Log::info('PagoFácil: Respuesta verify-payment', [
                'transaction_id' => $transactionId,
                'response' => $responseData
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'status' => $responseData['status'] ?? 'unknown'
                ];
            }

            return [
                'success' => false,
                'error' => 'Error en verificación: ' . $response->body()
            ];

        } catch (Exception $e) {
            Log::error('PagoFácil: Error al verificar pago', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Construir datos de orden para PagoFácil
     */
    public function buildOrderData($order, $qrTransaction, string $callbackUrl): array
    {
        // En sandbox, siempre usar 0.20 Bs sin ajustes proporcionales
        $usedAmount = 0.20;
        
        // Simplificar orderDetail para sandbox
        $orderDetails = [
            [
                'serial' => 1,
                'product' => 'Producto Sandbox',
                'quantity' => 1,
                'price' => 0.20,
                'discount' => 0,
                'total' => 0.20
            ]
        ];

        return [
            'client_name' => $qrTransaction->client_name,
            'document_type' => $qrTransaction->document_type,
            'document_id' => $qrTransaction->client_document_id ?: '12345678', // Valor por defecto si está vacío
            'phone_number' => $this->phoneNumber, // Usar el configurado en .env
            'email' => $qrTransaction->client_email,
            'payment_number' => $qrTransaction->payment_number,
            'amount' => $usedAmount,
            'currency' => $qrTransaction->currency,
            'client_code' => $qrTransaction->client_code, // ID del cliente en tu sistema
            'callback_url' => $callbackUrl,
            'order_details' => $orderDetails
        ];
    }
}