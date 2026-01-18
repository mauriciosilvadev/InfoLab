<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Imports\CourseImporter;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CourseImporter::class),
            CreateAction::make(),
        ];
    }
}
