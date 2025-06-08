<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacturaPedido extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $subpedido;
    public $distribuidor;
    public $pdfContent;

    public function __construct($pedido, $subpedido, $distribuidor, $pdfContent)
    {
        $this->pedido = $pedido;
        $this->subpedido = $subpedido;
        $this->distribuidor = $distribuidor;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject("Factura de tu pedido #{$this->pedido->id}")
            ->markdown('emails.factura')
            ->attachData(
                $this->pdfContent,
                "factura_pedido_{$this->pedido->id}_sub_{$this->subpedido->id}.pdf",
                ['mime' => 'application/pdf']
            );
    }
}

