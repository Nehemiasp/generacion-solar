<?php

namespace App\Providers;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate limiting de la API pública (RF-16): 60 peticiones por minuto por IP.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        // La documentación de la API (/docs/api) es pública: la evalúa el jurado sin cuenta.
        Gate::define('viewApiDocs', fn (?User $user = null) => true);

        // Cifras con coma de miles y punto decimal en todo Filament (docs/DISENO.md),
        // aunque la interfaz esté en español. Las fechas sí quedan en español.
        Table::configureUsing(fn (Table $table) => $table->defaultNumberLocale('en'));
        Schema::configureUsing(fn (Schema $schema) => $schema->defaultNumberLocale('en'));

        // En producción detrás de proxy TLS, los assets deben salir por https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
