<?php

use Illuminate\Support\Facades\Schedule;

// Atualiza o semestre letivo diariamente
Schedule::command('system:update-semester')->everyTenSeconds();
