<?php
// wp-config-ddev.php not needed

/**
 * Fix HTTPS behind load balancers.
 *
 */
if (
    isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
    && 'https' === strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'])
) {
    $_SERVER[ 'HTTPS' ] = 'on';
}

/**
 * Composer autoload.
 */
if (file_exists( $autoload = __DIR__ . '/vendor/autoload.php' ) )
    require_once realpath($autoload);

$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();

// Load .env.local if present and override
$envLocal = dirname(__DIR__, 1) . '/.env.local';
if (file_exists($envLocal)) {
    $dotenvLocal = Dotenv\Dotenv::createMutable(dirname(__DIR__, 1), '.env.local');
    $dotenvLocal->safeLoad();
}

/**
 * Configuration constants.
 *
 * Use environment variables to set WordPress constants.
 * Mandatory settings:
 * - DB_NAME
 * - DB_USER
 * - DB_PASSWORD
 *
 * @var array $env All the variables stored in environment variables
 */

foreach ($_ENV as $name => $value) {
    switch ($name) {
        case 'DB_TABLE_PREFIX':
            $GLOBALS['table_prefix'] = preg_replace('#[^\w]#', '', $value);
            break;
        default:
            defined($name) || define($name, $value);
            break;
    }
}
/**
 * Set WordPress Database Table prefix if not already set.
 *
 * @global string $table_prefix
 */
if (! isset($table_prefix) || empty($table_prefix)) {
    $table_prefix = 'ntdst_';
}


/**
 * Set unique authentication keys if not already set via environment variables.
 */
defined('AUTH_KEY')         or define('AUTH_KEY',         '46`9n!=V=c8^ht[iAd].:%j084V_P*UsONOK>b:S4q9|5}:B<qWM=&IDwnwCa1ME');
defined('SECURE_AUTH_KEY')  or define('SECURE_AUTH_KEY',  'e]9w1hXo!9jg3Z7s*)w/N_#312*Ch4Z0^2jVo]fIz(p1kH;/%Ub^wTC&OZWx0_7Q');
defined('LOGGED_IN_KEY')    or define('LOGGED_IN_KEY',    'Q7$.Kx,}uK-o->A?9B&UbvT$]Ft3mag+Ncyw0#{ekM i&fY{BShi+`t4c3{z/6*&');
defined('NONCE_KEY')        or define('NONCE_KEY',        'w?-XT1+qCbn# Tt<Gd3d_Q5zQ2L9,9^{pq(<H%NY-lE5nRO~P<p`_Zzmk5=yXqS[');
defined('AUTH_SALT')        or define('AUTH_SALT',        '$2&dXy:M$Xa/ D$YgHEYIwE,fG-!Fr1V^@jVz>9a>n45VHq^e1$N?^~}`E06(3xI');
defined('SECURE_AUTH_SALT') or define('SECURE_AUTH_SALT', 'Co~Rb,+BoS(irZ[,[tQ=a%YcO|Q{?%DT*co|1X8DxOD(rnDL,D%5OyFTjQ^yEBTl');
defined('LOGGED_IN_SALT')   or define('LOGGED_IN_SALT',   'uF&ES@peaJ1j6 ho`u&gbUYvAZ%dA0g0<*qg/MT!tq+Vz(ZMI0Rju0*]v2X-huk:');
defined('NONCE_SALT')       or define('NONCE_SALT',       'T,HIH+`<fk^ s$)vZpvQH89@AO}0;kqy&@^qPI$gaiiR8ngiko9J7*E5ibh?z-!z');


/**
 * Set debugging.
 */
$environment = defined('WP_ENVIRONMENT_TYPE') ? WP_ENVIRONMENT_TYPE : 'development';

switch ($environment) {

    case 'local':
    case 'development':
        defined('WP_DEBUG')         or define('WP_DEBUG',         true);
        defined('WP_DEBUG_DISPLAY') or define('WP_DEBUG_DISPLAY', false);
        defined('WP_DEBUG_LOG')     or define('WP_DEBUG_LOG',     true);
        defined('SAVEQUERIES')      or define('SAVEQUERIES',      true);
        defined('SCRIPT_DEBUG')     or define('SCRIPT_DEBUG',     true);
        break;

    case 'staging':
        defined('WP_DEBUG')         or define('WP_DEBUG',         true);
        defined('WP_DEBUG_DISPLAY') or define('WP_DEBUG_DISPLAY', false);
        defined('WP_DEBUG_LOG')     or define('WP_DEBUG_LOG',     true);
        defined('SCRIPT_DEBUG')     or define('SCRIPT_DEBUG',     true);
        break;

    case 'production':
    default:
        defined('WP_DEBUG')         or define('WP_DEBUG',         false);
        defined('WP_DEBUG_DISPLAY') or define('WP_DEBUG_DISPLAY', false);
        defined('WP_DEBUG_LOG')     or define('WP_DEBUG_LOG',     false);
        defined('SCRIPT_DEBUG')     or define('SCRIPT_DEBUG',     false);
        break;
}

/**
 * Set WordPress paths and urls if not set via environment variables.
 */
if (! defined('WP_HOME')) {
    $server = filter_input_array(INPUT_SERVER, array(
        'HTTPS'       => FILTER_SANITIZE_STRING,
        'SERVER_PORT' => FILTER_SANITIZE_NUMBER_INT,
        'SERVER_NAME' => FILTER_SANITIZE_URL,
    ));
    $secure = in_array((string) $server['HTTPS'], array('on', '1'), true);
    $scheme = $secure ? 'https://' : 'http://';
    $name   = $server['SERVER_NAME'] ? : 'localhost';
    define('WP_HOME', $scheme.$name);
}
defined('ABSPATH')        or define('ABSPATH',        realpath(__DIR__.'/wp'));
defined('WP_CONTENT_DIR') or define('WP_CONTENT_DIR', realpath(__DIR__.'/content'));
defined('WP_SITEURL')     or define('WP_SITEURL',     rtrim(WP_HOME, '/').'/wp');
defined('WP_CONTENT_URL') or define('WP_CONTENT_URL', rtrim(WP_HOME, '/').'/content');


/**
 * Clean up.
 */
unset($env, $environment, $server, $secure, $scheme, $name, $autoload);


/**
 * Sets up WordPress vars and included files.
 */
require_once( ABSPATH.DIRECTORY_SEPARATOR . 'wp-settings.php' );
