<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Retorna a tradução e a classe CSS para um determinado status de contrato.
     *
     * @param string|null $status O status vindo do banco de dados.
     * @return array Um array associativo com 'text' e 'class'.
     */
    public static function formatStatus(?string $status): array
    {
        switch (strtolower($status ?? '')) {
            case 'processed':
                return [
                    'text' => 'Processado',
                    'class' => 'bg-success-subtle text-success-emphasis'
                ];
            case 'error':
                return [
                    'text' => 'Erro',
                    'class' => 'bg-danger-subtle text-danger-emphasis'
                ];
            case 'pending':
                return [
                    'text' => 'Pendente',
                    'class' => 'bg-warning-subtle text-warning-emphasis'
                ];
            default:
                return [
                    'text' => ucfirst($status ?? 'Desconhecido'),
                    'class' => 'bg-secondary-subtle text-secondary-emphasis'
                ];
        }
    }
}
