<?php

namespace App\Services;

use App\Models\PpiSpi;
use App\Models\PpiSpiStatus;
use Carbon\Carbon;

class PpiSpiService
{
    public function getPpiSpiCounts()
    {
        $today = Carbon::today();

        return [
            'ppi' => [
                'created' => [
                    'today' => PpiSpi::where('action_format', 'Ppi')
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpi::where('action_format', 'Ppi')->count(),
                ],
                'completed' => [
                    'today' => PpiSpiStatus::where('status_for', 'Ppi')
                        ->where('code', 'ppi_all_steps_complete')
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpiStatus::where('status_for', 'Ppi')
                        ->where('code', 'ppi_all_steps_complete')
                        ->count(),
                ],
                'pending' => [
                    'today' => PpiSpi::where('action_format', 'Ppi')
                        ->whereNotIn('id', function ($query) {
                            $query->select('ppi_spi_id')
                                ->from('ppi_spi_statuses')
                                ->where('code', 'ppi_all_steps_complete');
                        })
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpi::where('action_format', 'Ppi')
                        ->whereNotIn('id', function ($query) {
                            $query->select('ppi_spi_id')
                                ->from('ppi_spi_statuses')
                                ->where('code', 'ppi_all_steps_complete');
                        })
                        ->count(),
                ],
            ],
            'spi' => [
                'created' => [
                    'today' => PpiSpi::where('action_format', 'Spi')
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpi::where('action_format', 'Spi')->count(),
                ],
                'completed' => [
                    'today' => PpiSpiStatus::where('status_for', 'Spi')
                        ->where('code', 'spi_all_steps_complete')
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpiStatus::where('status_for', 'Spi')
                        ->where('code', 'spi_all_steps_complete')
                        ->count(),
                ],
                'pending' => [
                    'today' => PpiSpi::where('action_format', 'Spi')
                        ->whereNotIn('id', function ($query) {
                            $query->select('ppi_spi_id')
                                ->from('ppi_spi_statuses')
                                ->where('code', 'spi_all_steps_complete');
                        })
                        ->whereDate('created_at', $today)
                        ->count(),
                    'overall' => PpiSpi::where('action_format', 'Spi')
                        ->whereNotIn('id', function ($query) {
                            $query->select('ppi_spi_id')
                                ->from('ppi_spi_statuses')
                                ->where('code', 'spi_all_steps_complete');
                        })
                        ->count(),
                ],
            ],
        ];
    }
}
