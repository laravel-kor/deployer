<?php

require_once 'recipe/common.php';

server('production', 'laravel.co.kr', '1013')
    ->user('deploy')
    ->path('/var/www/laravel.co.kr');

set('shared_dirs',['app/storage']);
set('shared_files',[]);
set('repository', 'https://github.com/laravel-kor/laravel.co.kr.git');
set('writeable_dirs', ['app/storage']);

task('database:migrate', function() {
    run("php current/artisan migrate");
});

/**
 * Make writeable dirs
 */
task('deploy:writeable_dirs', function () {
    $user = config()->getUser();
    $wwwUser = config()->getWwwUser();
    $releasePath = env()->getReleasePath();

    cd($releasePath);

    // User specified writeable dirs
    $dirs = (array)get('writeable_dirs', []);

    foreach ($dirs as $dir) {
        run("chmod 0777 $dir");
        run("chmod g+w $dir");
    }
})->desc('Make writeable dirs');

task('laravel:create_storage_dirs', function() {
    $dirs = ['sessions','views','meta','logs','cache'];

    foreach ($dirs as $dir) {
        $path = 'current/app/storage/' . $dir;
        run("if [ ! -d \"$path\" ]; then mkdir -p $path; fi", true);
    }
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
