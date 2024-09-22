<?php

arch()
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump']);


arch()
    ->expect(globArch('App\*\Models'))
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toBeClasses();

arch('app')
    ->expect('App')
    ->not->toHaveFileSystemPermissions('0777');
