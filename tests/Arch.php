<?php

arch()
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump'])
    ->not->toHaveFileSystemPermissions('0777');
