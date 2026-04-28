<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Student;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use BackedEnum;
use UnitEnum;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class StudentSearch extends Page
{
    use HasPageShield;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass-circle';
    
    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Keuangan';
    
    protected static ?string $navigationLabel = 'POS Pembayaran';
    
    protected static ?string $title = 'Terminal Pencarian Santri';

    protected string $view = 'filament.pages.student-search';

    public string $searchQuery = '';

    #[Computed]
    public function students()
    {
        if (blank($this->searchQuery)) {
            return collect();
        }

        return Student::with('academicRecords.schoolClass')
            ->where('full_name', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('nis', 'like', '%' . $this->searchQuery . '%')
            ->limit(12)
            ->get();
    }

    public function openFinance(string $studentId): void
    {
        $this->redirect(StudentFinance::getUrl(['student_id' => $studentId]));
    }
}
