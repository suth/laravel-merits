<?php

namespace Suth\Merits\Enums;

enum TriggerCategory: string
{
    case Manual = 'manual';
    case Retroactive = 'retroactive';
    case Model = 'model';
    case Event = 'event';
}
