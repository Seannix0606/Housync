<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseDataService;
use App\Models\User;
use App\Models\Apartment;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class SyncToFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync 
                           {--model= : Sync specific model only (users, apartments, units)}
                           {--force : Force sync even if records already exist}
                           {--test : Test Firebase connection first}
                           {--dry-run : Show what would be synced without actually syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all existing data to Firebase';

    private $firebaseService;

    /**
     * Create a new command instance.
     */
    public function __construct(FirebaseDataService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Firebase Data Sync Tool');
        $this->info('=========================');

        // Test Firebase connection if requested
        if ($this->option('test')) {
            $this->info('Testing Firebase connection...');
            if ($this->firebaseService->testConnection()) {
                $this->info('✅ Firebase connection successful!');
            } else {
                $this->error('❌ Firebase connection failed!');
                return self::FAILURE;
            }
        }

        // Get model filter
        $modelFilter = $this->option('model');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be synced');
        }

        // Confirm before proceeding
        if (!$force && !$dryRun) {
            if (!$this->confirm('This will sync all data to Firebase. Continue?')) {
                $this->info('Sync cancelled.');
                return self::SUCCESS;
            }
        }

        $results = [
            'users' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
            'apartments' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
            'units' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
            'errors' => []
        ];

        // Sync users
        if (!$modelFilter || $modelFilter === 'users') {
            $this->info("\n📱 Syncing users...");
            $results['users'] = $this->syncUsers($dryRun);
        }

        // Sync apartments
        if (!$modelFilter || $modelFilter === 'apartments') {
            $this->info("\n🏢 Syncing apartments...");
            $results['apartments'] = $this->syncApartments($dryRun);
        }

        // Sync units
        if (!$modelFilter || $modelFilter === 'units') {
            $this->info("\n🏠 Syncing units...");
            $results['units'] = $this->syncUnits($dryRun);
        }

        // Display results
        $this->displayResults($results);

        return self::SUCCESS;
    }

    /**
     * Sync users to Firebase
     */
    private function syncUsers($dryRun = false)
    {
        $users = User::all();
        $results = ['success' => 0, 'failed' => 0, 'skipped' => 0];

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line("  Would sync user: {$user->name} ({$user->email}) - {$user->role}");
                $results['success']++;
            } else {
                try {
                    if ($this->firebaseService->saveUser($user)) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        Log::error("Failed to sync user: {$user->email}");
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Error syncing user {$user->email}: " . $e->getMessage());
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');

        return $results;
    }

    /**
     * Sync apartments to Firebase
     */
    private function syncApartments($dryRun = false)
    {
        $apartments = Apartment::with('landlord')->get();
        $results = ['success' => 0, 'failed' => 0, 'skipped' => 0];

        $progressBar = $this->output->createProgressBar($apartments->count());
        $progressBar->start();

        foreach ($apartments as $apartment) {
            if ($dryRun) {
                $landlordName = $apartment->landlord ? $apartment->landlord->name : 'Unknown';
                $this->line("  Would sync apartment: {$apartment->name} (Landlord: {$landlordName})");
                $results['success']++;
            } else {
                try {
                    if ($this->firebaseService->saveApartment($apartment)) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        Log::error("Failed to sync apartment: {$apartment->name}");
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Error syncing apartment {$apartment->name}: " . $e->getMessage());
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');

        return $results;
    }

    /**
     * Sync units to Firebase
     */
    private function syncUnits($dryRun = false)
    {
        $units = Unit::with('apartment')->get();
        $results = ['success' => 0, 'failed' => 0, 'skipped' => 0];

        $progressBar = $this->output->createProgressBar($units->count());
        $progressBar->start();

        foreach ($units as $unit) {
            if ($dryRun) {
                $apartmentName = $unit->apartment ? $unit->apartment->name : 'Unknown';
                $this->line("  Would sync unit: {$unit->unit_number} (Apartment: {$apartmentName})");
                $results['success']++;
            } else {
                try {
                    if ($this->firebaseService->saveUnit($unit)) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        Log::error("Failed to sync unit: {$unit->unit_number}");
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error("Error syncing unit {$unit->unit_number}: " . $e->getMessage());
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');

        return $results;
    }

    /**
     * Display sync results
     */
    private function displayResults($results)
    {
        $this->info("\n📊 Sync Results:");
        $this->info("================");

        $totalSuccess = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach (['users', 'apartments', 'units'] as $model) {
            if ($results[$model]['success'] > 0 || $results[$model]['failed'] > 0 || $results[$model]['skipped'] > 0) {
                $this->info("\n{$model}:");
                $this->info("  ✅ Success: {$results[$model]['success']}");
                if ($results[$model]['failed'] > 0) {
                    $this->error("  ❌ Failed: {$results[$model]['failed']}");
                }
                if ($results[$model]['skipped'] > 0) {
                    $this->warn("  ⏭️  Skipped: {$results[$model]['skipped']}");
                }

                $totalSuccess += $results[$model]['success'];
                $totalFailed += $results[$model]['failed'];
                $totalSkipped += $results[$model]['skipped'];
            }
        }

        $this->info("\n📋 Summary:");
        $this->info("  Total Success: {$totalSuccess}");
        if ($totalFailed > 0) {
            $this->error("  Total Failed: {$totalFailed}");
        }
        if ($totalSkipped > 0) {
            $this->warn("  Total Skipped: {$totalSkipped}");
        }

        if ($totalFailed > 0) {
            $this->error("\n❌ Some records failed to sync. Check the logs for details.");
        } else {
            $this->info("\n✅ All records synced successfully!");
        }
    }
}
