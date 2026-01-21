<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Settings\SystemSettings;
use Illuminate\Console\Command;

class UpdateAcademicSemester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update-semester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza automaticamente o semestre letivo baseado na data atual';

    /**
     * Execute the console command.
     */
    public function handle(SystemSettings $settings): int
    {
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        if ($currentMonth >= 3 && $currentMonth <= 8) {
            $period = '1';
            $academicYear = $currentYear;
        } else {
            $period = '2';
            if ($currentMonth <= 2) {
                $academicYear = $currentYear - 1;
            } else {
                $academicYear = $currentYear;
            }
        }

        $newYearString = (string) $academicYear;

        if ($settings->current_semester_year === $newYearString &&
            $settings->current_semester_period === $period) {
            $this->info("Semestre letivo já está atualizado ({$newYearString}/{$period}). Nenhuma alteração necessária.");

            return Command::SUCCESS;
        }

        $oldYear = $settings->current_semester_year;
        $oldPeriod = $settings->current_semester_period;

        $settings->current_semester_year = $newYearString;
        $settings->current_semester_period = $period;
        $settings->save();

        activity()
            ->useLog(Activity::LOG_TYPE_SYSTEM)
            ->withProperties([
                'old' => ['year' => $oldYear, 'period' => $oldPeriod],
                'attributes' => ['year' => $newYearString, 'period' => $period],
                'source' => 'Console Command (Scheduler)',
            ])
            ->log("O sistema atualizou automaticamente o semestre letivo para {$newYearString}/{$period}");

        $this->info("Semestre letivo atualizado para: {$settings->current_semester_year}/{$settings->current_semester_period}º Semestre");

        return Command::SUCCESS;
    }
}
