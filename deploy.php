<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'workflowlogic');
set('repository', 'https://github.com/ExallRoofing/WorkFlowLogic.git');

// Shared dirs/files
add('shared_dirs', [
    'storage',
    'bootstrap/cache'
]);
add('shared_files', [
    '.env'
]);

// Writable dirs
add('writable_dirs', [
    'storage',
    'bootstrap/cache',
]);

// Hosts
host('production')
    ->setHostname('ec2-52-30-37-26.eu-west-1.compute.amazonaws.com')
    ->set('remote_user', 'ubuntu')
    ->set('identity_file', '~/.ssh/exall-ssh.pem')
    ->set('branch', 'main')
    ->set('deploy_path', '/var/www/workflowlogic.co.uk');

// Tasks
desc('Build assets locally and upload');
task('build:assets', function () {
    runLocally('npm install');
    runLocally('npm run build');

    upload('public/build/', '{{release_path}}/public/build/');
});

before('deploy:symlink', 'artisan:config:cache');
after('deploy:failed', 'deploy:unlock');

// Optional: clear statamic caches after deploy
task('statamic:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan statamic:stache:clear');
});

// Hooks
after('deploy:failed', 'deploy:unlock');
