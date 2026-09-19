<?php

namespace Suth\Merits\Models;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Enums\TriggerType;

class BadgeAward extends Model
{
    protected $fillable = ['badge_key', 'trigger_type'];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
            'trigger_type' => TriggerType::class,
        ]);

        parent::__construct($attributes);

        $this->table = config('merits.table_names.badge_awards') ?: parent::getTable();
    }
}
