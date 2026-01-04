<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LearningJournals\Widgets\LatestLearningJournals as WidgetsLatestLearningJournals;
use App\Filament\Resources\StudentDevelopments\Widgets\LatestStudentDevelopments as WidgetsLatestStudentDevelopments;
use App\Filament\Widgets\LatestLearningJournals;
use App\Filament\Widgets\LatestStudentDevelopments;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            WidgetsLatestLearningJournals::class,
            WidgetsLatestStudentDevelopments::class,
        ];
    }
}
