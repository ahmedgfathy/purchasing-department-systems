<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    private function translationKey(): string
    {
        return 'app.permissions.'.str_replace('.', '_', $this->slug);
    }

    public function displayName(): string
    {
        $key = $this->translationKey().'.name';
        $value = __($key);

        return is_string($value) && $value !== $key ? $value : (string) $this->name;
    }

    public function displayDescription(): ?string
    {
        $key = $this->translationKey().'.description';
        $value = __($key);

        if (is_string($value) && $value !== $key) {
            return $value;
        }

        return $this->description ?: null;
    }

    public function displayGroup(): string
    {
        $key = $this->translationKey().'.group';
        $value = __($key);

        if (is_string($value) && $value !== $key) {
            return $value;
        }

        return $this->group ?: (string) __('app.models.Permission');
    }
}
