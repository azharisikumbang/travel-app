<?php ob_start();

require_once __DIR__ . '/../src/App.php';
require_once __DIR__ . '/../src/Manager.php';
require_once __DIR__ . '/../src/libraries/Database.php';
require_once __DIR__ . '/../src/libraries/Router.php';
require_once __DIR__ . '/../src/libraries/Session.php';
require_once __DIR__ . '/../src/libraries/Response.php';
require_once __DIR__ . '/../src/libraries/Request.php';

$appConfiguration = require_once __DIR__ . '/../src/config/app.php';
$databaseConfiguration = require_once __DIR__ . '/../src/config/database.php';

$manager = new Manager();
$manager->setDatabaseManager(new Database($databaseConfiguration));
$manager->setSessionManager(new Session());
$manager->setRouterManager(new Router());
$manager->setRequestManager(new Request($_REQUEST));
$manager->setResponseManager(new Response());

$app = new App($manager);
$app->addConfigFor('app', $appConfiguration);
$app->setEnvironment('development');
$app->setTimeZone('Asia/Jakarta');
$app->loadFunction('functions', fn ($file) => require_once $file);
$app->buildRoute($_GET['path'] ?? 'homepage');
$app->run();