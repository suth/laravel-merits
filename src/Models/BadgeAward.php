<?php

namespace Suth\Merits\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeAward extends Model
{
    protected $fillable = ['badge_key'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('merits.table_names.badge_awards') ?: parent::getTable();
    }
}
