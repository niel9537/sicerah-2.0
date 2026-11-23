<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait LogsAudit
{
    protected function logAudit(string $aksi, Model $model, ?array $dataLama = null): void
    {
        AuditLog::create([
            'tabel'      => $model->getTable(),
            'data_id'    => $model->getKey(),
            'aksi'       => $aksi, // create | update | delete
            'user_id'    => auth()->id(),
            'data_lama'  => $dataLama,
            'data_baru'  => $aksi !== 'delete' ? $model->getAttributes() : null,
            'created_at' => now(),
        ]);
    }
}