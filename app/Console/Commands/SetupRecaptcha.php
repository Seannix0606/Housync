<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupRecaptcha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:setup-recaptcha 
                            {--site-key= : reCAPTCHA site key}
                            {--secret-key= : reCAPTCHA secret key}
                            {--check : Check current reCAPTCHA configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up reCAPTCHA for Firebase App Check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔑 Firebase App Check reCAPTCHA Setup');
        $this->info('=====================================');

        if ($this->option('check')) {
            return $this->checkRecaptchaConfig();
        }

        $siteKey = $this->option('site-key');
        $secretKey = $this->option('secret-key');

        if (!$siteKey || !$secretKey) {
            $this->setupRecaptchaInteractive();
        } else {
            $this->setupRecaptchaWithKeys($siteKey, $secretKey);
        }
    }

    /**
     * Interactive reCAPTCHA setup
     */
    private function setupRecaptchaInteractive()
    {
        $this->info('Let\'s set up reCAPTCHA for Firebase App Check!');
        $this->newLine();

        // Check if user has already created reCAPTCHA site
        $hasRecaptcha = $this->confirm('Have you already created a reCAPTCHA v3 site?');

        if (!$hasRecaptcha) {
            $this->info('Please follow these steps:');
            $this->info('1. Go to https://www.google.com/recaptcha/admin');
            $this->info('2. Click "Create" or "+" to add a new site');
            $this->info('3. Fill in:');
            $this->info('   - Label: HouseSync App Check');
            $this->info('   - Type: reCAPTCHA v3');
            $this->info('   - Domains: localhost, 127.0.0.1, yourdomain.com');
            $this->info('4. Accept Terms of Service and click Submit');
            $this->newLine();

            if (!$this->confirm('Have you completed these steps?')) {
                $this->warn('Please complete the reCAPTCHA setup first, then run this command again.');
                return 1;
            }
        }

        // Get keys from user
        $siteKey = $this->ask('Enter your reCAPTCHA Site Key (public key)');
        $secretKey = $this->secret('Enter your reCAPTCHA Secret Key (private key)');

        if (!$siteKey || !$secretKey) {
            $this->error('Both Site Key and Secret Key are required.');
            return 1;
        }

        $this->setupRecaptchaWithKeys($siteKey, $secretKey);
    }

    /**
     * Set up reCAPTCHA with provided keys
     */
    private function setupRecaptchaWithKeys(string $siteKey, string $secretKey)
    {
        $this->info('Setting up reCAPTCHA configuration...');

        // Update .env file
        $this->updateEnvFile($siteKey, $secretKey);

        // Create test HTML file
        $this->createTestFile($siteKey);

        $this->info('✅ reCAPTCHA configuration completed!');
        $this->newLine();

        $this->info('Next steps:');
        $this->info('1. Go to Firebase Console → App Check');
        $this->info('2. Click "Register" next to your Housesync Web App');
        $this->info('3. Select reCAPTCHA as attestation provider');
        $this->info('4. Enter your reCAPTCHA Secret Key: ' . substr($secretKey, 0, 10) . '...');
        $this->info('5. Set Token time to live: 1 days');
        $this->info('6. Click "Save"');
        $this->newLine();

        $this->info('Test your setup:');
        $this->info('• Visit: http://localhost:8000/test-recaptcha');
        $this->info('• Run: php artisan firebase:secure --test');
    }

    /**
     * Update .env file with reCAPTCHA keys
     */
    private function updateEnvFile(string $siteKey, string $secretKey)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found');
            return;
        }

        $envContent = File::get($envFile);

        // Update or add reCAPTCHA configuration
        $updates = [
            'FIREBASE_RECAPTCHA_SITE_KEY' => $siteKey,
            'FIREBASE_RECAPTCHA_SECRET_KEY' => $secretKey,
            'FIREBASE_APP_CHECK_DEBUG_TOKEN' => 'true'
        ];

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envFile, $envContent);
        $this->info('✅ Updated .env file with reCAPTCHA configuration');
    }

    /**
     * Create test HTML file
     */
    private function createTestFile(string $siteKey)
    {
        $testContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>reCAPTCHA Test - HouseSync</title>
    <script src="https://www.google.com/recaptcha/api.js?render={$siteKey}"></script>
</head>
<body>
    <h1>reCAPTCHA v3 Test</h1>
    <p>This page tests your reCAPTCHA v3 configuration.</p>
    
    <button onclick="testRecaptcha()">Test reCAPTCHA</button>
    
    <div id="result"></div>
    
    <script>
        function testRecaptcha() {
            grecaptcha.ready(function() {
                grecaptcha.execute('{$siteKey}', {action: 'test'}).then(function(token) {
                    document.getElementById('result').innerHTML = 
                        '<h3>Success!</h3>' +
                        '<p>reCAPTCHA token generated successfully.</p>' +
                        '<p>Token: ' + token.substring(0, 50) + '...</p>';
                }).catch(function(error) {
                    document.getElementById('result').innerHTML = 
                        '<h3>Error!</h3>' +
                        '<p>Failed to generate reCAPTCHA token.</p>' +
                        '<p>Error: ' + error + '</p>';
                });
            });
        }
    </script>
</body>
</html>
HTML;

        File::put(public_path('test-recaptcha.html'), $testContent);
        $this->info('✅ Created test file: public/test-recaptcha.html');
    }

    /**
     * Check current reCAPTCHA configuration
     */
    private function checkRecaptchaConfig(): int
    {
        $this->info('Checking reCAPTCHA configuration...');
        $this->newLine();

        $checks = [
            'Site Key' => env('FIREBASE_RECAPTCHA_SITE_KEY'),
            'Secret Key' => env('FIREBASE_RECAPTCHA_SECRET_KEY'),
            'Debug Token' => env('FIREBASE_APP_CHECK_DEBUG_TOKEN'),
        ];

        $allConfigured = true;

        foreach ($checks as $name => $value) {
            if ($value) {
                $this->info("✅ {$name}: " . ($name === 'Secret Key' ? substr($value, 0, 10) . '...' : $value));
            } else {
                $this->error("❌ {$name}: Not configured");
                $allConfigured = false;
            }
        }

        $this->newLine();

        // Check test file
        $testFile = public_path('test-recaptcha.html');
        if (File::exists($testFile)) {
            $this->info('✅ Test file exists: public/test-recaptcha.html');
        } else {
            $this->warn('⚠️  Test file not found: public/test-recaptcha.html');
        }

        $this->newLine();

        if ($allConfigured) {
            $this->info('🎉 reCAPTCHA configuration is complete!');
            $this->info('Next: Register your app in Firebase Console App Check');
            return 0;
        } else {
            $this->warn('⚠️  reCAPTCHA configuration is incomplete');
            $this->info('Run: php artisan firebase:setup-recaptcha');
            return 1;
        }
    }
} 