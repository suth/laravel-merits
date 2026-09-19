<?php

namespace Suth\Merits\Enums;

enum TriggerType: string
{
    case Manual = 'manual';
    case Retroactive = 'retroactive';
    case Model = 'model';
    case Event = 'event';
}
