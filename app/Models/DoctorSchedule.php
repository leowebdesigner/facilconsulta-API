<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'doctor_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'slot_duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
     * Relationships
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
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

    public function scopeForWeekday(Builder $query, ?int $weekday): Builder
    {
        return is_null($weekday) ? $query : $query->where('weekday', $weekday);
    }

    public function scopeWithinTimeRange(Builder $query, ?string $start, ?string $end): Builder
    {
        if (! $start && ! $end) {
            return $query;
        }

        return $query->when($start, fn (Builder $builder) => $builder->where('start_time', '>=', $start))
            ->when($end, fn (Builder $builder) => $builder->where('end_time', '<=', $end));
    }
}
