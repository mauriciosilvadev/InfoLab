<?php

namespace App\Filament\Imports;

use App\Models\Course;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import; // <--- Importante
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CourseImporter extends Importer
{
    protected static ?string $model = Course::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nome do Curso')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->examples(['Engenharia de Alimentos (exemplo)', 'Medicina Veterinária (exemplo)'])
                ->exampleHeader('Nome do Curso (header)'),
        ];
    }

    public function resolveRecord(): ?Course
    {
        try {
            $name = trim($this->data['name'] ?? '');

            if (empty($name)) {
                throw new RowImportFailedException('O campo "Nome do Curso" está vazio.');
            }

            $name = Str::of($name)
                ->squish()
                ->title()
                ->toString();

            if (mb_strlen($name) > 255) {
                throw new RowImportFailedException("O nome '{$name}' excede 255 caracteres.");
            }

            $existingCourse = Course::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
                ->first();

            if ($existingCourse) {
                return $existingCourse;
            }

            return new Course([
                'name' => $name,
            ]);
        } catch (RowImportFailedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $errorMsg = 'Erro ao processar o curso';
            if (! empty($this->data['name'])) {
                $errorMsg .= " '{$this->data['name']}'";
            }
            $errorMsg .= ': ' . $e->getMessage();

            throw new RowImportFailedException($errorMsg);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = "Importação concluída! {$import->successful_rows} curso(s) processado(s)";

        $notification = Notification::make()
            ->title('Importação de Cursos');

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " e {$failedRowsCount} falhou/falharam.";
            $notification->danger();
        } else {
            $body .= ' com sucesso!';
            $notification->success();
        }

        $notification->body($body);
        $notification->sendToDatabase(auth()->user());

        return $body;
    }

    /**
     * Hook before validate.
     */
    public function beforeValidate(): void
    {
        if (app()->environment('local')) {
            logger()->debug('Importando curso', [
                'data' => $this->data,
                'original_data' => $this->originalData ?? null,
            ]);
        }
    }

    public function getValidationAttributes(): array
    {
        return [
            'name' => 'Nome do Curso',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'O campo "Nome do Curso" é obrigatório.',
            'name.string' => 'O campo "Nome do Curso" deve ser um texto válido.',
            'name.max' => 'O campo "Nome do Curso" não pode ter mais de 255 caracteres.',
        ];
    }
}
