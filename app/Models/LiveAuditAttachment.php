<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveAuditAttachment extends Model
{
    protected $table = 'live_audit_attachments';

    protected $fillable = [
        'live_audit_id',
        'name',
        'file_path',
        'sort_order',
    ];

    public function liveAudit(): BelongsTo
    {
        return $this->belongsTo(LiveAudit::class);
    }
}
