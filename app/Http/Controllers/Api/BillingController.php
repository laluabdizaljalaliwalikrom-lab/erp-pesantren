<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillingEvent;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BillingController extends Controller
{
    /**
     * Inject the BillingService via constructor promotion.
     */
    public function __construct(
        private readonly BillingService $billingService
    ) {}

    /**
     * Fetch Bill records with their relations, supporting optional status filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Bill::with(['student', 'billingEvent']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, ['unpaid', 'partial', 'paid'], true)) {
                $query->where('status', $status);
            }
        }

        $bills = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Bills retrieved successfully.',
            'data'    => $bills,
        ], 200);
    }

    /**
     * Generate bills in bulk for all active students.
     */
    public function generateMassBilling(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'billing_event_id' => ['required', 'uuid', 'exists:billing_events,id'],
        ]);

        try {
            $generatedBills = DB::transaction(function () use ($validated) {
                $event = BillingEvent::findOrFail($validated['billing_event_id']);

                // Fetch "active" students (checking their student_academic status)
                //$students = Student::whereHas('academicRecords', function ($query) {
                    //$query->where('status', 'active');
                //})->get();
                // dd("Jumlah Santri Ditemukan: " . $students->count(), $event->toArray());
                $students = Student::all();


                $bills = [];
                foreach ($students as $student) {
                    $bill = $this->billingService->generateBill($event, $student);
                    if ($bill !== null) {
                        $bills[] = $bill;
                    }
                }

                return $bills;
            });

            return response()->json([
                'success' => true,
                'message' => sprintf('Successfully generated %d bills.', count($generatedBills)),
                'data'    => $generatedBills,
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate mass billing: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}