<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'complaint_code',
    'category_id',
    'title',
    'description',
    'address',
    'district',
    'village',
    'latitude',
    'longitude',
    'reporter_name',
    'reporter_phone',
    'reporter_email',
    'photo_path',
    'status',
])]
#[Hidden(['reporter_name', 'reporter_phone', 'reporter_email'])]
class Complaint extends Model
{
    /** @use HasFactory<ComplaintFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ComplaintStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<Assignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * @return HasOne<Assignment, $this>
     */
    public function activeAssignment(): HasOne
    {
        return $this->hasOne(Assignment::class)->whereNull('completed_at')->latestOfMany();
    }

    /**
     * @return HasMany<ComplaintStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ComplaintStatusHistory::class);
    }
}
