<?php

namespace Suth\Merits\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeRegistration extends Model
{
    protected $fillable = ['key', 'is_active', 'available_since'];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
            'is_active' => 'boolean',
            'available_since' => 'datetime',
        ]);

        parent::__construct($attributes);

        $this->table = config('merits.table_names.badge_registrations') ?: parent::getTable();
    }
}
