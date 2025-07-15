<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase.factory', function ($app) {
            try {
                // Check if Firebase SDK is available
                if (!class_exists('\Kreait\Firebase\Factory')) {
                    Log::warning('Firebase SDK not installed. Please run: composer require kreait/firebase-php');
                    return null;
                }

                $factory = (new \Kreait\Firebase\Factory);

                // Configure with service account
                if (config('firebase.project_id')) {
                    $serviceAccount = [
                        'type' => 'service_account',
                        'project_id' => config('firebase.project_id'),
                        'private_key_id' => config('firebase.private_key_id'),
                        'private_key' => str_replace('\\n', "\n", config('firebase.private_key')),
                        'client_email' => config('firebase.client_email'),
                        'client_id' => config('firebase.client_id'),
                        'auth_uri' => config('firebase.auth_uri'),
                        'token_uri' => config('firebase.token_uri'),
                        'auth_provider_x509_cert_url' => config('firebase.auth_provider_x509_cert_url'),
                        'client_x509_cert_url' => config('firebase.client_x509_cert_url'),
                    ];

                    $factory = $factory->withServiceAccount($serviceAccount);
                }

                // Configure database URL if provided
                if (config('firebase.database_url')) {
                    $factory = $factory->withDatabaseUri(config('firebase.database_url'));
                }

                return $factory;
            } catch (\Exception $e) {
                Log::error('Firebase initialization failed: ' . $e->getMessage());
                return null;
            }
        });

        // Register Firebase Auth service
        $this->app->singleton('firebase.auth', function ($app) {
            $factory = $app->make('firebase.factory');
            return $factory ? $factory->createAuth() : null;
        });

        // Register Firebase Database service
        $this->app->singleton('firebase.database', function ($app) {
            $factory = $app->make('firebase.factory');
            return $factory ? $factory->createDatabase() : null;
        });

        // Register Firebase Storage service
        $this->app->singleton('firebase.storage', function ($app) {
            $factory = $app->make('firebase.factory');
            return $factory ? $factory->createStorage() : null;
        });

        // Register Firebase Messaging service
        $this->app->singleton('firebase.messaging', function ($app) {
            $factory = $app->make('firebase.factory');
            return $factory ? $factory->createMessaging() : null;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
} 