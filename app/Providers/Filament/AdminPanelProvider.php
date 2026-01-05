<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\GuruKuLogin;
use App\Filament\Pages\Auth\GuruKuRegister;
use App\Filament\Pages\Tenancy\RegisterSchool;
use App\Filament\Resources\LearningJournals\Widgets\LatestLearningJournals;
use App\Filament\Resources\StudentDevelopments\Widgets\LatestStudentDevelopments;
use App\Filament\Widgets\SchoolInviteCode;
use App\Models\School;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Assets\Css;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;

use function Symfony\Component\String\s;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName('JurnalGuruku')
            ->id('admin')
            ->path('admin')
            ->authGuard('web')


            //filament tenancy setup
            ->tenant(School::class)
            ->tenantRegistration(RegisterSchool::class)

            //custom login page
            ->login(GuruKuLogin::class)
            ->registration(GuruKuRegister::class)
            ->passwordReset(\App\Filament\Pages\Auth\GuruKuPasswordResetRequest::class)
            ->tenantRegistration(RegisterSchool::class)

            //custom theme file
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('right')
                    ->mobileFormPanelPosition('bottom')
                    ->formPanelWidth('40%')
                    ->formPanelBackgroundColor(Color::Emerald, '600')
                    // ->emptyPanelBackgroundImageOpacity('30%')
                    ->emptyPanelBackgroundImageUrl('https://blogger.googleusercontent.com/img/a/AVvXsEhz4p_70hzOsJDERFxWAilbcNhgaYC4bm40AZfqDfOcHjDOeF3dJBpe1XJFiRwrCswmbch4viYYHGnRimdJ3PTLiT-EFfqIDpQvIPKkRClL5b-g3OS4VEgpCNGX8sTva2QbjoWVqdJxEjIR2-ZeSsPll-oH3aRPYXj4kugO3HedScORixTyiI-JM6HAlgs=s1600'),
            ])
            ->brandLogo(asset('images/brandlogo.png'))
            ->brandLogoHeight('4rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->assets([
                Css::make(
                    'admin-custom',
                    resource_path('css/filament/admin.css')
                ),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                LatestLearningJournals::class,
                LatestStudentDevelopments::class,
                SchoolInviteCode::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
