<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;
use Suth\Merits\Models\BadgeRegistration;

interface BadgeRegistrationRepository
{
    public function register(Badge $badge): BadgeRegistration;
}
