<?php

namespace Suth\Merits\Models;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Enums\TriggerCategory;

class BadgeAward extends Model
{
    protected $fillable = ['badge_key', 'trigger_category'];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
            'trigger_category' => TriggerCategory::class,
        ]);

        parent::__construct($attributes);

        $this->table = config('merits.table_names.badge_awards') ?: parent::getTable();
    }
}
