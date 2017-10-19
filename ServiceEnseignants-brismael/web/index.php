<?php
ini_set('memory_limit', '-1');

// loader le framework
$loader = require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/fpdf/fpdf181/fpdf.php';
define('FPDF_FONTPATH',__DIR__ .'/../vendor/fpdf/fpdf181/font');

// instantier $app comme objet de Silex framework
$app = new Silex\Application();

// init les params dir de bases
$baseDir          = dirname(dirname(__FILE__));
$aBaseDir         = explode(DIRECTORY_SEPARATOR, $baseDir);
$rootFolder       = end($aBaseDir);
$app['paths']     = ['rootFolder' => $rootFolder, 'baseDir' => $baseDir, 'webDir' => dirname(__FILE__), 
    'vendorDir' => $baseDir . '/vendor', 'appDir' => $baseDir . '/app', 'srcDir' => $baseDir . '/src'];

$app['rootRoute'] = '/'.$rootFolder;

// Register the Security Service Provider
$app->register(new Silex\Provider\SessionServiceProvider());

// Register UrlGeneratorServiceProvider to call route by binded name
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Twig configuration
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => $app['paths']['srcDir']."/Views"
));

$app->register(new Silex\Provider\HttpFragmentServiceProvider());

use Symfony\Component\HttpFoundation\Request;

Request::enableHttpMethodParameterOverride();


// security config for security provider
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'admin' => array(
        'pattern' => '^/.*$',
        'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
        'logout' => array('logout_path' => 'logout', 'invalidate_session' => true),

        //DB USERS USE
        'users' => $app->share(function() use ($app) {
            // Specific class App\User\UserProvider is described below
            //return new App\User\UserProvider($app['db']);
            return new Entity\UserProvider($app['db']);
        }),
    ),
);


$app['security.access_rules'] = array(
    //array('^/admin', 'ROLE_ADMIN'),
    //array('^/Modifier', 'ROLE_ADMIN'),
    array('^.*$', 'ROLE_USER'),
);

$app['security.role_hierarchy'] = array(
    'ROLE_ADMIN' => array('ROLE_USER'),
);

/*
$app['security.encoders'] = array(
    'Models\User' => array(
        'algorithm' => 'bcrypt',
    )
);
*/

//security service provider
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => $app['security.firewalls'],
    'security.access_rules' => $app['security.access_rules'],
    'security.role_hierarchy' => $app['security.role_hierarchy'],

));


// charger le fichier de configuration
$ini_config = parse_ini_file($app['paths']['appDir'].'/config.ini', TRUE);
// récupérer des info de connexion BD
$config = $ini_config['developpement'];

//catch all errors and convert them to exceptions
Symfony\Component\Debug\ExceptionHandler::register($app['debug']);


// array info data base
$app['db.options'] = array(
    'driver'    => $config['db.driver'],
    'dbname'    => $config['db.dbname'],
    'host'      => $config['db.host'],
    'port'      => $config['db.port'],
    'user'      => $config['db.user'],
    'password'  => $config['db.password'],
    'charset'   => $config['db.charset'],
);

// instancier Doctrine DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options'],
));

// Récupérer tous les routes avant de lancer l'application
require_once $app['paths']['srcDir'].'/Routes.php';

$app['debug'] = true;

$app['annee'] = 2015;
$app['titreImpression'] = "";

//$app->boot();
$app->run(); 

?>	