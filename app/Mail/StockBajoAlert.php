<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class StockBajoAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $producto;
    public $tries = 3;
    public $timeout = 60;
    public $maxExceptions = 2;

    /**
     * Create a new message instance.
     *
     * @param Producto $producto
     * @return void
     */
    public function __construct(Producto $producto)
    {
        // Validar que el producto no sea null
        if (!$producto) {
            throw new \InvalidArgumentException('El producto no puede ser null');
        }

        // Validar datos mínimos requeridos
        if (!$producto->nombre) {
            throw new \InvalidArgumentException('El producto debe tener un nombre');
        }

        $this->producto = $producto;
        
        // Configurar la cola si es necesario
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = '⚠️ Alerta: Stock bajo - ' . ($this->producto->nombre ?? 'Producto');
        
        return new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'noreply@tuempresa.com'),
            replyTo: config('mail.reply_to.address'),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.stock_bajo',
            with: [
                'producto' => $this->producto,
                'stockActual' => $this->producto->stock ?? 0,
                'stockMinimo' => $this->producto->stock_minimo ?? 0,
                'nombreProducto' => $this->producto->nombre ?? 'Sin nombre',
                'codigoProducto' => $this->producto->codigo ?? 'Sin código',
                'fechaAlerta' => now()->format('d/m/Y H:i:s'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Falló el envío de alerta de stock bajo', [
            'producto_id' => $this->producto->id ?? 'unknown',
            'producto_nombre' => $this->producto->nombre ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Método legacy para compatibilidad (se puede remover si no se usa)
     * 
     * @return $this
     * @deprecated Use envelope() and content() methods instead
     */
    public function build()
    {
        try {
            $subject = '⚠️ Alerta: Stock bajo - ' . ($this->producto->nombre ?? 'Producto');
            
            return $this->subject($subject)
                        ->view('emails.stock_bajo')
                        ->with([
                            'producto' => $this->producto,
                            'stockActual' => $this->producto->stock ?? 0,
                            'stockMinimo' => $this->producto->stock_minimo ?? 0,
                            'nombreProducto' => $this->producto->nombre ?? 'Sin nombre',
                            'codigoProducto' => $this->producto->codigo ?? 'Sin código',
                            'fechaAlerta' => now()->format('d/m/Y H:i:s'),
                        ]);
        } catch (\Exception $e) {
            Log::error('Error al construir email de stock bajo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Determine if the job should be retried.
     *
     * @param \Throwable $exception
     * @return bool
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}