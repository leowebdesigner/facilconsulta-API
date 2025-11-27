<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'document',
        'crm',
        'specialty',
        'experience_years',
        'bio',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_years' => 'integer',
    ];

    /*
     * Relationships
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /*
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialty(Builder $query, ?string $specialty): Builder
    {
        return $specialty ? $query->where('specialty', $specialty) : $query;
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('schedules', function (Builder $builder) {
            $builder->active();
        });
    }

    public function scopeActiveState(Builder $query, ?bool $active): Builder
    {
        return is_null($active) ? $query : $query->where('is_active', $active);
    }
}
