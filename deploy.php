<?php

require_once 'recipe/common.php';

server('production', 'laravel.co.kr', '1013')
    ->user('deploy')
    ->path('/var/www/laravel.co.kr');

set('shared_dirs',['app/storage']);
set('shared_files',[]);
set('repository', 'https://github.com/laravel-kor/laravel.co.kr.git');
set('writeable_dirs', ['app/storage']);

task('chown', function() {
    run('sudo chown -R root:www-data current');
});

task('database:migrate', function() {
    run("php current/artisan migrate");
});

task('laravel:create_storage_dirs', function() {
    run("mkdir current/app/storage/sessions");
    run("mkdir current/app/storage/views");
    run("mkdir current/app/storage/meta");
    run("mkdir current/app/storage/logs");
    run("mkdir current/app/storage/cache");
});

task('deploy', [
    'deploy:start',
    'deploy:prepare',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writeable_dirs',
    'laravel:create_storage_dirs',
    'deploy:vendors',
    'deploy:symlink',
    'database:migrate',
    'cleanup',
    'deploy:end'
])->desc('Deploy your project');
