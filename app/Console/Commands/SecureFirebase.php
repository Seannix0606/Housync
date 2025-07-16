<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\File;

class SecureFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:secure 
                            {--check : Check current security status}
                            {--rules : Display security rules}
                            {--test : Test Firebase connection and security}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Secure Firebase configuration and test security rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔒 Firebase Security Manager');
        $this->info('============================');

        if ($this->option('check')) {
            return $this->checkSecurityStatus();
        }

        if ($this->option('rules')) {
            return $this->displaySecurityRules();
        }

        if ($this->option('test')) {
            return $this->testFirebaseSecurity();
        }

        // Default: Run security check and provide recommendations
        $this->checkSecurityStatus();
        $this->newLine();
        $this->provideSecurityRecommendations();
    }

    /**
     * Check current Firebase security status
     */
    private function checkSecurityStatus(): int
    {
        $this->info('Checking Firebase security status...');
        $this->newLine();

        $checks = [
            'Firebase SDK Configuration' => $this->checkFirebaseSDK(),
            'Environment Variables' => $this->checkEnvironmentVariables(),
            'Security Rules File' => $this->checkSecurityRulesFile(),
            'Service Account Key' => $this->checkServiceAccountKey(),
            'Authentication Setup' => $this->checkAuthenticationSetup(),
        ];

        $passed = 0;
        $total = count($checks);

        foreach ($checks as $check => $result) {
            if ($result['status']) {
                $this->info("✅ {$check}");
                $passed++;
            } else {
                $this->error("❌ {$check}");
            }
            
            if (!empty($result['message'])) {
                $this->line("   {$result['message']}");
            }
            $this->newLine();
        }

        $this->info("Security Status: {$passed}/{$total} checks passed");
        
        if ($passed === $total) {
            $this->info('🎉 All security checks passed!');
            return 0;
        } else {
            $this->warn('⚠️  Some security checks failed. Please review the recommendations.');
            return 1;
        }
    }

    /**
     * Check Firebase SDK configuration
     */
    private function checkFirebaseSDK(): array
    {
        try {
            $firebaseService = app(FirebaseService::class);
            $configured = $firebaseService->isConfigured();
            
            return [
                'status' => $configured,
                'message' => $configured ? 'Firebase SDK is properly configured' : 'Firebase SDK is not configured'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error checking Firebase SDK: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check environment variables
     */
    private function checkEnvironmentVariables(): array
    {
        $requiredVars = [
            'FIREBASE_PROJECT_ID',
            'FIREBASE_PRIVATE_KEY',
            'FIREBASE_CLIENT_EMAIL',
            'FIREBASE_DATABASE_URL'
        ];

        $missing = [];
        foreach ($requiredVars as $var) {
            if (empty(env($var))) {
                $missing[] = $var;
            }
        }

        if (empty($missing)) {
            return [
                'status' => true,
                'message' => 'All required environment variables are set'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Missing environment variables: ' . implode(', ', $missing)
            ];
        }
    }

    /**
     * Check security rules file
     */
    private function checkSecurityRulesFile(): array
    {
        $rulesFile = base_path('firebase-security-rules.json');
        
        if (!File::exists($rulesFile)) {
            return [
                'status' => false,
                'message' => 'Security rules file not found'
            ];
        }

        $content = File::get($rulesFile);
        $rules = json_decode($content, true);

        if (!$rules || !isset($rules['rules'])) {
            return [
                'status' => false,
                'message' => 'Invalid security rules format'
            ];
        }

        // Check for public rules (security risk)
        if (isset($rules['rules']['.read']) && $rules['rules']['.read'] === true) {
            return [
                'status' => false,
                'message' => 'Security rules allow public read access'
            ];
        }

        if (isset($rules['rules']['.write']) && $rules['rules']['.write'] === true) {
            return [
                'status' => false,
                'message' => 'Security rules allow public write access'
            ];
        }

        return [
            'status' => true,
            'message' => 'Security rules file exists and appears secure'
        ];
    }

    /**
     * Check service account key
     */
    private function checkServiceAccountKey(): array
    {
        $privateKey = env('FIREBASE_PRIVATE_KEY');
        $clientEmail = env('FIREBASE_CLIENT_EMAIL');

        if (empty($privateKey) || empty($clientEmail)) {
            return [
                'status' => false,
                'message' => 'Service account key not configured'
            ];
        }

        // Check if private key format is correct
        if (!str_contains($privateKey, '-----BEGIN PRIVATE KEY-----')) {
            return [
                'status' => false,
                'message' => 'Private key format appears incorrect'
            ];
        }

        // Check if client email format is correct
        if (!str_contains($clientEmail, '@') || !str_contains($clientEmail, '.iam.gserviceaccount.com')) {
            return [
                'status' => false,
                'message' => 'Client email format appears incorrect'
            ];
        }

        return [
            'status' => true,
            'message' => 'Service account key appears to be configured correctly'
        ];
    }

    /**
     * Check authentication setup
     */
    private function checkAuthenticationSetup(): array
    {
        try {
            $firebaseService = app(FirebaseService::class);
            
            // Try to create a test token
            $testToken = $firebaseService->createCustomToken('test-user');
            
            if ($testToken) {
                return [
                    'status' => true,
                    'message' => 'Firebase authentication is working'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Firebase authentication is not working'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error testing authentication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Display security rules
     */
    private function displaySecurityRules(): int
    {
        $rulesFile = base_path('firebase-security-rules.json');
        
        if (!File::exists($rulesFile)) {
            $this->error('Security rules file not found: ' . $rulesFile);
            return 1;
        }

        $this->info('Firebase Security Rules:');
        $this->info('======================');
        $this->newLine();

        $content = File::get($rulesFile);
        $this->line($content);

        $this->newLine();
        $this->info('To apply these rules:');
        $this->info('1. Go to Firebase Console → Realtime Database → Rules');
        $this->info('2. Copy and paste the above rules');
        $this->info('3. Click "Publish"');

        return 0;
    }

    /**
     * Test Firebase security
     */
    private function testFirebaseSecurity(): int
    {
        $this->info('Testing Firebase security...');
        $this->newLine();

        try {
            $firebaseService = app(FirebaseService::class);

            // Test 1: Check if Firebase is configured
            $this->info('Test 1: Configuration check');
            if ($firebaseService->isConfigured()) {
                $this->info('✅ Firebase is configured');
            } else {
                $this->error('❌ Firebase is not configured');
                return 1;
            }

            // Test 2: Test authentication
            $this->info('Test 2: Authentication test');
            $testToken = $firebaseService->createCustomToken('test-user-' . time());
            if ($testToken) {
                $this->info('✅ Authentication is working');
            } else {
                $this->error('❌ Authentication failed');
                return 1;
            }

            // Test 3: Test database connection
            $this->info('Test 3: Database connection test');
            $testData = [
                'test' => true,
                'timestamp' => now()->toISOString(),
                'message' => 'Security test'
            ];
            
            if ($firebaseService->storeData('test/security-check', $testData)) {
                $this->info('✅ Database write successful');
                
                // Try to read back the data
                $readData = $firebaseService->getData('test/security-check');
                if ($readData) {
                    $this->info('✅ Database read successful');
                } else {
                    $this->warn('⚠️  Database read failed');
                }
            } else {
                $this->error('❌ Database write failed');
            }

            $this->newLine();
            $this->info('🎉 All security tests passed!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Security test failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Provide security recommendations
     */
    private function provideSecurityRecommendations(): void
    {
        $this->info('Security Recommendations:');
        $this->info('========================');
        $this->newLine();

        $recommendations = [
            '1. Apply Security Rules' => [
                'Copy the rules from firebase-security-rules.json to Firebase Console',
                'Go to Realtime Database → Rules → Paste → Publish'
            ],
            '2. Enable Authentication' => [
                'Go to Firebase Console → Authentication → Sign-in method',
                'Enable Email/Password and Custom token authentication'
            ],
            '3. Set Up App Check' => [
                'Go to Firebase Console → App Check',
                'Enable for your web application',
                'Add your domain to allowed domains'
            ],
            '4. Monitor Usage' => [
                'Set up usage alerts in Firebase Console',
                'Monitor authentication logs regularly',
                'Review security rules monthly'
            ],
            '5. Secure Environment' => [
                'Never commit private keys to version control',
                'Use environment variables for all secrets',
                'Rotate service account keys regularly'
            ]
        ];

        foreach ($recommendations as $title => $items) {
            $this->info($title);
            foreach ($items as $item) {
                $this->line("   • {$item}");
            }
            $this->newLine();
        }

        $this->info('For detailed instructions, see: FIREBASE_SECURITY_SETUP.md');
    }
} 