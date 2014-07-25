<?php

require_once 'recipe/common.php';

server('production', 'laravel.co.kr', '1013')
    ->user('deploy')
    ->path('/var/www/laravel.co.kr');

set('repository', 'https://github.com/laravel-kor/laravel.co.kr.git');


// Symfony shared dirs
set('shared_dirs', []);

// Symfony shared files
set('shared_files', []);

// Symfony writeable dirs
set('writeable_dirs', ['app/storage']);


task('chown', function() {
    run('sudo chown -R root:www-data current');
});

task('deploy', [
    'deploy:start',
    'deploy:prepare',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writeable_dirs',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup',
    'deploy:end'
])->desc('Deploy your project');
