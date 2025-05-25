<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoaningForm extends Model
{
    /** @use HasFactory<\Database\Factories\LoaningFormFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'applicant_id',
        'start_at',
        'end_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    /**
     * Get the item that this loaning form is for.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the applicant who submitted this loaning form.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * Determine if the loaning form can be modified.
     */
    public function canBeModified(): bool
    {
        return $this->status === 'requested';
    }

    /**
     * Determine if the loaning form can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'requested';
    }
}
