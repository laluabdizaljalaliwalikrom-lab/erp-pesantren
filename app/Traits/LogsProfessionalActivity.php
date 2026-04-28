<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsProfessionalActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName(strtolower(class_basename($this)));
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $causer = $activity->causer?->name ?? 'Sistem';
        
        $action = match ($eventName) {
            'created'  => 'membuat',
            'updated'  => 'memperbarui',
            'deleted'  => 'menghapus',
            'restored' => 'memulihkan',
            default    => $eventName,
        };

        $modelName = strtolower(class_basename($this));
        
        // Custom branding for models
        $modelLabel = match ($modelName) {
            'student' => 'Santri',
            'payment' => 'Pembayaran',
            'bill'    => 'Tagihan',
            'user'    => 'Pengguna',
            default   => $modelName,
        };

        $subjectName = $this->getAttribute('name') ?? 
                      $this->getAttribute('full_name') ?? 
                      $this->getAttribute('nis') ??
                      $this->getAttribute('transaction_id') ?? 
                      '#' . $this->getKey();

        $activity->description = "{$action} {$modelLabel} '{$subjectName}'";
    }
}
