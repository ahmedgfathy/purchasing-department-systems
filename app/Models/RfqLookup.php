<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class RfqLookup extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = ['code', 'name', 'status', 'created_by'];

    public static function statuses(): array
    {
        return [self::STATUS_ACTIVE => 'Active', self::STATUS_INACTIVE => 'Inactive'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return __('app.administration.statuses.'.$this->status);
    }
}
