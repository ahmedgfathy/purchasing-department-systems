<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A vendor registered against a service / product (Access: "Vendor VS Tasks").
 */
class VendorService extends Model
{
    protected $fillable = [
        'vendor_id',
        'activity_id',
        'registration_type',
        'registration_role',
        'brand',
        'country_of_origin',
        'origin',
        'original_manufacturer',
        'reviewer',
        'selected',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'selected' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
