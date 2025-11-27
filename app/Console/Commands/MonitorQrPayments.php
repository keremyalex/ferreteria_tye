<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrTransaction;
use App\Services\PagoFacilService;
use Illuminate\Support\Facades\Log;

class MonitorQrPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'qr:monitor {--payment-number= : Monitor specific payment number}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor QR payments in real time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Monitor de Pagos QR iniciado...');
        $this->info('📊 Presiona Ctrl+C para detener');
        $this->newLine();
        
        $paymentNumber = $this->option('payment-number');
        
        if ($paymentNumber) {
            $this->monitorSpecificPayment($paymentNumber);
        } else {
            $this->monitorAllPendingPayments();
        }
    }
    
    private function monitorSpecificPayment($paymentNumber)
    {
        $this->info("🔍 Monitoreando pago específico: {$paymentNumber}");
        $this->newLine();
        
        $pagoFacilService = new PagoFacilService();
        
        while (true) {
            $this->line("📅 " . now()->format('H:i:s') . " - Verificando...");
            
            $result = $pagoFacilService->verifyPayment($paymentNumber);
            
            if ($result['success']) {
                $status = $result['status'];
                $rawStatus = $result['raw_status'] ?? 'N/A';
                $amount = $result['data']['values']['amount'] ?? 'N/A';
                
                $this->line("   💰 Estado: {$status} (Raw: {$rawStatus})");
                $this->line("   💵 Monto: {$amount} Bs");
                
                if ($status === 'paid') {
                    $this->info("✅ ¡PAGO COMPLETADO!");
                    break;
                }
            } else {
                $this->error("   ❌ Error: " . ($result['error'] ?? 'Error desconocido'));
            }
            
            sleep(5);
        }
    }
    
    private function monitorAllPendingPayments()
    {
        $this->info("🔄 Monitoreando todos los pagos pendientes...");
        $this->newLine();
        
        $pagoFacilService = new PagoFacilService();
        
        while (true) {
            $pendingTransactions = QrTransaction::where('status', 'pending')
                ->where('expires_at', '>', now())
                ->get();
                
            $this->line("📅 " . now()->format('H:i:s') . " - Pagos pendientes: " . $pendingTransactions->count());
            
            if ($pendingTransactions->isEmpty()) {
                $this->comment("   📭 No hay pagos pendientes");
            }
            
            foreach ($pendingTransactions as $transaction) {
                $result = $pagoFacilService->verifyPayment($transaction->payment_number);
                
                if ($result['success']) {
                    $status = $result['status'];
                    $rawStatus = $result['raw_status'] ?? 'N/A';
                    
                    $this->line("   🔹 {$transaction->payment_number}: {$status} (Raw: {$rawStatus})");
                    
                    if ($status === 'paid') {
                        $this->info("   ✅ ¡Pago completado para {$transaction->payment_number}!");
                        
                        // Actualizar estado en base de datos
                        $transaction->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'verify_response' => $result['data']
                        ]);
                        
                        if ($transaction->order) {
                            $transaction->order->update(['estado' => 'completado']);
                        }
                    }
                } else {
                    $this->line("   🔸 {$transaction->payment_number}: Error - " . ($result['error'] ?? 'N/A'));
                }
            }
            
            $this->newLine();
            sleep(10);
        }
    }
}