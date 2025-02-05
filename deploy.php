<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/yarn.php';
// Config

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');

set('repository', 'git@github.com:AurelienLab/daps-p8-todo.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('prod')
    ->set('hostname', 'vmedias-prod') // requires config in ~/.ssh/config
    ->set('deploy_path', '/srv/www/daps/p8')
    ->set('branch', 'develop')
;

task(
    'build', function () {
    run('cd {{release_path}} && {{bin/yarn}} build');
}
);

// Hooks

after('deploy:vendors', 'yarn:install');
after('yarn:install', 'build');

before('deploy:publish', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
