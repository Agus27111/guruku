<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SchoolInviteCode extends Widget
{
    protected string $view = 'filament.widgets.school-invite-code';

    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 0;
}
