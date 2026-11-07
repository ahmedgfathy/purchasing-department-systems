<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    public const STATUS_PENDING = 'pending_approval';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_VENDORS_ASSIGNED = 'vendors_assigned';

    protected $fillable = [
        'reference',
        'title',
        'project',
        'vessel',
        'requesting_department',
        'location',
        'cost_center',
        'contact_name',
        'contact_code',
        'material_req_no',
        'requisition_rec_date',
        'preparation_date',
        'service_delivery_period',
        'market_id',
        'order_type_id',
        'proposed_method_id',
        'ref_order_no',
        'estimated_budget',
        'budget_currency',
        'description',
        'status',
        'priority',
        'currency',
        'needed_by',
        'notes',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'vendors_assigned_by',
        'vendors_assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'needed_by' => 'date',
            'requisition_rec_date' => 'date',
            'preparation_date' => 'date',
            'approved_at' => 'datetime',
            'vendors_assigned_at' => 'datetime',
            'estimated_budget' => 'decimal:2',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_VENDORS_ASSIGNED => 'Vendors assigned',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function priorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function currencies(): array
    {
        return ['USD', 'EGP', 'EUR', 'GBP', 'SAR', 'AED'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class)
            ->withPivot(['assigned_by', 'note'])
            ->withTimestamps();
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function vendorsAssignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendors_assigned_by');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function proposedMethod(): BelongsTo
    {
        return $this->belongsTo(ProposedMethod::class);
    }

    public function statusLabel(): string
    {
        return __('app.rfq.statuses.'.$this->status);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'amber',
            self::STATUS_APPROVED => 'blue',
            self::STATUS_REJECTED => 'red',
            self::STATUS_VENDORS_ASSIGNED => 'green',
            default => 'slate',
        };
    }

    public function priorityLabel(): string
    {
        return __('app.rfq.priorities.'.$this->priority);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high' => 'amber',
            'low' => 'slate',
            default => 'blue',
        };
    }
}
