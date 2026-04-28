<?php

namespace Database\Seeders;

use App\Models\BillingEvent;
use App\Models\Institution;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(BillingService $billingService): void
    {
        // We assume this runs on a fresh database (php artisan migrate:fresh --seed)
        
        // Create 1 Institution
        $institution = Institution::create([
            'name' => 'SMP Pesantren Test',
            'type' => 'SMP',
        ]);

        // Create 4 Student records to test all edge cases
        $students = [
            Student::create([
                'nis'               => '1001',
                'full_name'         => 'Santri A',
                'residency_status'  => 'mukim',
                'special_condition' => 'yatim_piatu', // Expects 100% discount
            ]),
            Student::create([
                'nis'               => '1002',
                'full_name'         => 'Santri B',
                'residency_status'  => 'tidak_mukim',
                'special_condition' => 'kurang_mampu', // Expects 50% discount
            ]),
            Student::create([
                'nis'               => '1003',
                'full_name'         => 'Santri C',
                'residency_status'  => 'mukim',
                'special_condition' => 'yatim', // Expects 25% discount
            ]),
            Student::create([
                'nis'               => '1004',
                'full_name'         => 'Santri D',
                'residency_status'  => 'mukim',
                'special_condition' => 'none', // Expects 0% discount
            ]),
        ];

        // Create 2 BillingEvent records
        $events = [
            BillingEvent::create([
                'institution_id'   => $institution->id,
                'name'             => 'SPP Juli 2026',
                'amount'           => 500000.00,
                'target_residency' => 'all',
            ]),
            BillingEvent::create([
                'institution_id'   => $institution->id,
                'name'             => 'Uang Asrama',
                'amount'           => 300000.00,
                'target_residency' => 'mukim',
            ]),
        ];

        // Loop through both Billing Events and all 4 Students
        foreach ($events as $event) {
            foreach ($students as $student) {
                // Call the generator for each combination
                $billingService->generateBill($event, $student);
            }
        }
    }
}
