<?php

namespace Deployer;

require 'recipe/laravel.php';

// -----------------------------------------------------------------------------
// CONFIG
// -----------------------------------------------------------------------------

set('application', 'workflowlogic');
set('repository', 'https://github.com/ExallRoofing/WorkFlowLogic.git');
set('git_tty', true);
set('keep_releases', 5);

// Shared files/dirs
add('shared_files', ['.env']);
add('shared_dirs', ['storage']);

// Writable
add('writable_dirs', ['storage']);

// -----------------------------------------------------------------------------
// HOST
// -----------------------------------------------------------------------------

host('production')
    ->setHostname('ec2-52-30-37-26.eu-west-1.compute.amazonaws.com')
    ->set('remote_user', 'ubuntu')
    ->set('identity_file', '~/.ssh/exall-ssh.pem')
    ->set('branch', 'main')
    ->set('deploy_path', '/var/www/workflowlogic.co.uk');

// -----------------------------------------------------------------------------
// FRONTEND BUILD (server-side Vite)
// -----------------------------------------------------------------------------

task('build:frontend', function () {
    run('cd {{ release_path }} && npm install');
    run('cd {{ release_path }} && npm run build');
})->desc('Build Vite assets on server');

after('deploy:vendors', 'build:frontend');

// -----------------------------------------------------------------------------
// STATAMIC & LARAVEL OPTIMISATIONS
// -----------------------------------------------------------------------------

task('statamic:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan statamic:stache:clear');
    run('{{bin/php}} {{release_path}}/artisan statamic:stache:warm');
    run('{{bin/php}} {{release_path}}/artisan optimize:clear');
});

// -----------------------------------------------------------------------------
// PERMISSIONS
// -----------------------------------------------------------------------------

task('fix:permissions', function () {
    run('sudo chown -R ubuntu:www-data {{deploy_path}}');
    run('sudo chmod -R 775 {{deploy_path}}');
});

after('deploy:symlink', 'fix:permissions');

// -----------------------------------------------------------------------------
// LARAVEL OPTIMISE & CACHE
// -----------------------------------------------------------------------------

after('deploy:symlink', 'artisan:config:cache');
after('deploy:symlink', 'artisan:route:cache');
after('deploy:symlink', 'statamic:clear');

// -----------------------------------------------------------------------------
// FAILURE HANDLING
// -----------------------------------------------------------------------------

after('deploy:failed', 'deploy:unlock');
