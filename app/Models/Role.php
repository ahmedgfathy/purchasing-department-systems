<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public const SUPER_ADMIN = 'super-admin';

    public const ADMIN = 'admin';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function displayName(): string
    {
        $key = "app.seeded_roles.{$this->slug}.name";
        $value = __($key);

        return is_string($value) && $value !== $key ? $value : (string) $this->name;
    }

    public function displayDescription(): ?string
    {
        $key = "app.seeded_roles.{$this->slug}.description";
        $value = __($key);

        if (is_string($value) && $value !== $key) {
            return $value;
        }

        return $this->description ?: null;
    }
}
