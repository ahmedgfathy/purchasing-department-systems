<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vendor extends Model
{
    public const TYPE_LOCAL = 'local';

    public const TYPE_FOREIGN = 'foreign';

    public const TYPE_BOTH = 'both';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'code',
        'name',
        'type',
        'contact_name',
        'email',
        'phone',
        'country',
        'category',
        'address',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_LOCAL => 'Local',
            self::TYPE_FOREIGN => 'Foreign',
            self::TYPE_BOTH => 'Local & foreign',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function rfqs(): BelongsToMany
    {
        return $this->belongsToMany(Rfq::class)
            ->withPivot(['assigned_by', 'note'])
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return __('app.vendors.types.'.$this->type);
    }

    public function statusLabel(): string
    {
        return __('app.vendors.statuses.'.$this->status);
    }
}
