<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catalog of services / products a vendor can be registered against.
 *
 * Mirrors the legacy Access "Tasks sub" (source = task) and "الأنشطة"
 * (source = product) tables, plus custom entries created from the web UI.
 */
class Activity extends Model
{
    public const SOURCE_TASK = 'task';

    public const SOURCE_PRODUCT = 'product';

    public const SOURCE_CUSTOM = 'custom';

    protected $fillable = [
        'source',
        'code',
        'name_ar',
        'name_en',
        'main_code',
        'main_name_ar',
        'main_name_en',
        'activity_code',
        'product_name',
        'product_code',
        'product_status',
    ];

    /**
     * @return array<string, string>
     */
    public static function sources(): array
    {
        return [
            self::SOURCE_TASK => __('app.vendors.activities.sources.task'),
            self::SOURCE_PRODUCT => __('app.vendors.activities.sources.product'),
            self::SOURCE_CUSTOM => __('app.vendors.activities.sources.custom'),
        ];
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(VendorService::class, 'activity_id');
    }

    public function sourceLabel(): string
    {
        return __('app.vendors.activities.sources.'.$this->source);
    }

    /**
     * Display label: Arabic name falls back to English, prefixed by the code.
     *
     * NOTE: deliberately not named label() — AuditLogger probes
     * $model->getAttribute('label'), which would invoke this method as a
     * relationship and throw a LogicException.
     */
    public function displayName(): string
    {
        $name = $this->name_ar ?: $this->name_en ?: $this->product_name;

        return trim($this->code.' — '.$name, ' —');
    }
}
