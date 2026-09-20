<?php

namespace Suth\Merits\Enums;

enum TriggerCategory: string
{
    case Manual = 'manual';
    case Retroactive = 'retroactive';
    case EloquentEvent = 'eloquent_event';
    case CustomEvent = 'custom_event';
}
