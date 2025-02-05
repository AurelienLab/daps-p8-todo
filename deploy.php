<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/yarn.php';
// Config

set('repository', 'git@github.com:AurelienLab/daps-p8-todo.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('prod')
    ->set('hostname', 'vmedias-prod') // requires config in ~/.ssh/config
    ->set('deploy_path', '/srv/www/daps/p8')
    ->set('branch', 'main')
;

task(
    'build', function () {
    run('cd {{release_path}} && {{bin/yarn}} prod');
}
);

// Hooks

after('deploy:vendors', 'yarn:install');
after('yarn:install', 'build');

after('deploy:failed', 'deploy:unlock');
