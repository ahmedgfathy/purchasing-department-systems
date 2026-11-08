<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    public const TYPE_LOCAL = 'local';

    public const TYPE_FOREIGN = 'foreign';

    public const TYPE_BOTH = 'both';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        // Legacy RFQ facing columns (kept in sync with the Access fields below).
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

        // Microsoft Access "Vendors" table.
        'supplier_id',
        'name_ar',
        'name_en',
        'brochure_no',
        'brochure_type',
        'agent_name',
        'phone1',
        'phone2',
        'phone3',
        'fax1',
        'fax2',
        'mobile1',
        'mobile2',
        'mobile3',
        'email1',
        'email2',
        'email3',
        'website',
        'agent_phone1',
        'agent_phone2',
        'agent_phone3',
        'agent_fax1',
        'agent_fax2',
        'agent_fax3',
        'agent_mobile1',
        'agent_mobile2',
        'agent_mobile3',
        'agent_email1',
        'agent_email2',
        'agent_email3',
        'agent_address',
        'committee_date',
        'committee_no',
        'registration_status',
        'registration_status_legacy',
        'certified_class',
        'data_entry_user',
        'free_zone',
        'cd_vendor',
        'commercial_register',
        'commercial_register_expiry',
        'tax_card',
        'tax_card_expiry',
        'vat_number',
        'vat_expiry',
        'owner_name',
        'capital_amount',
        'capital_currency',
        'agency_documents',
        'local_registered',
        'foreign_registered',
        'stopped',
        'struck_off',
        'ahmed_flag',
        'special_notes',
        'extra_notes',
        'committee_text',
        'sab_no',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'brochure_no' => 'integer',
            'capital_amount' => 'integer',
            'local_registered' => 'boolean',
            'foreign_registered' => 'boolean',
            'stopped' => 'boolean',
            'struck_off' => 'boolean',
            'ahmed_flag' => 'boolean',
            'committee_date' => 'date',
            'commercial_register_expiry' => 'date',
            'tax_card_expiry' => 'date',
            'vat_expiry' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Vendor $vendor): void {
            $vendor->syncDerivedColumns();
        });
    }

    /**
     * Keep the legacy RFQ columns (name, type, status, email, phone) derived
     * from the Microsoft Access fields, exactly like the Access forms did.
     */
    public function syncDerivedColumns(): void
    {
        // name = Arabic name, falling back to the English one.
        $name = trim((string) ($this->name_ar ?: $this->name_en ?: $this->name));
        if ($name !== '') {
            $this->name = $name;
        }

        // code = Access Supplier ID when it exists.
        if ($this->supplier_id && ! $this->code) {
            $this->code = (string) $this->supplier_id;
        }

        $this->type = $this->deriveType();
        $this->status = ($this->stopped || $this->struck_off) ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;

        // email / phone mirror the primary Access fields when they are filled.
        $email = self::cleanHyperlink($this->email1)
            ?: self::cleanHyperlink($this->email2)
            ?: self::cleanHyperlink($this->email3);

        if ($email !== null || $this->email1 || $this->email2 || $this->email3) {
            $this->email = mb_substr($email ?? '', 0, 255) ?: null;
        }

        $phone = $this->firstFilled($this->phone1, $this->mobile1, $this->phone2, $this->phone3, $this->mobile2, $this->mobile3);

        if ($phone !== null) {
            $this->phone = mb_substr($phone, 0, 40);
        }
    }

    /**
     * Access derives the vendor kind from the registration flags, the brochure
     * type (محلى / خارجى) and finally the country.
     */
    public function deriveType(): string
    {
        $local = (bool) $this->local_registered;
        $foreign = (bool) $this->foreign_registered;

        if ($local && $foreign) {
            return self::TYPE_BOTH;
        }

        if ($local) {
            return self::TYPE_LOCAL;
        }

        if ($foreign) {
            return self::TYPE_FOREIGN;
        }

        $brochure = trim((string) $this->brochure_type);

        if ($brochure !== '') {
            return str_contains($brochure, 'محلى') ? self::TYPE_LOCAL : self::TYPE_FOREIGN;
        }

        $country = trim((string) $this->country);

        if ($country === '') {
            return $this->type ?: self::TYPE_LOCAL;
        }

        return self::isEgypt($country) ? self::TYPE_LOCAL : self::TYPE_FOREIGN;
    }

    /**
     * Strip the Access hyperlink decoration ("value#http://value#").
     */
    public static function cleanHyperlink(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/#.*$/s', '', $value) ?? $value;
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private static function isEgypt(string $country): bool
    {
        if (preg_match('/مصر|egypt/iu', $country)) {
            return true;
        }

        return (bool) preg_match('/القاهرة|الجيزة|الإسكندرية|اسكندرية|بورسعيد|حلوان|المنصورة|طنطا|أسيوط|السويس|الإسماعيلية/u', $country);
    }

    private function firstFilled(?string ...$values): ?string
    {
        foreach ($values as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

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

    /**
     * Registered services / products (Access: Vendor VS Tasks).
     *
     * @return HasMany<VendorService, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(VendorService::class);
    }

    /**
     * Search across every vendor column ("search by anything").
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $textColumns = [
                'name', 'code', 'contact_name', 'email', 'phone', 'country', 'category',
                'address', 'notes', 'supplier_id', 'name_ar', 'name_en', 'brochure_type',
                'agent_name', 'phone1', 'phone2', 'phone3', 'fax1', 'fax2',
                'mobile1', 'mobile2', 'mobile3', 'email1', 'email2', 'email3', 'website',
                'agent_phone1', 'agent_phone2', 'agent_phone3',
                'agent_fax1', 'agent_fax2', 'agent_fax3',
                'agent_mobile1', 'agent_mobile2', 'agent_mobile3',
                'agent_email1', 'agent_email2', 'agent_email3', 'agent_address',
                'committee_no', 'registration_status', 'registration_status_legacy',
                'certified_class', 'data_entry_user', 'free_zone', 'cd_vendor',
                'commercial_register', 'tax_card', 'vat_number', 'owner_name',
                'capital_currency', 'agency_documents', 'special_notes', 'extra_notes',
                'committee_text', 'sab_no',
            ];

            foreach ($textColumns as $column) {
                $query->orWhereRaw('CAST('.$column.' AS TEXT) ILIKE ?', [$like]);
            }

            // "Search by anything" also matches the related services/products.
            $query->orWhereHas('services.activity', function (Builder $activity) use ($like) {
                $activity->where(function (Builder $activity) use ($like) {
                    $activity->where('code', 'ilike', $like)
                        ->orWhere('activity_code', 'ilike', $like)
                        ->orWhere('name_ar', 'ilike', $like)
                        ->orWhere('name_en', 'ilike', $like)
                        ->orWhere('product_name', 'ilike', $like)
                        ->orWhere('main_name_ar', 'ilike', $like);
                });
            });
        });
    }

    public function typeLabel(): string
    {
        return __('app.vendors.types.'.$this->type);
    }

    public function statusLabel(): string
    {
        return __('app.vendors.statuses.'.$this->status);
    }

    /**
     * Arabic name falling back to the English one (Access shows both).
     */
    public function displayName(): string
    {
        return $this->name_ar ?: $this->name_en ?: $this->name;
    }

    public function englishName(): string
    {
        return $this->name_en ?: $this->name;
    }
}
