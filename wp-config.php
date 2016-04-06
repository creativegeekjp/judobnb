<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'judobnb');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'cgeek');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_DEBUG',true);
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(QR:,3R@co>VWiM<|Fd~y}?R)$rnj`l4)Mm7$*n&+-^TpLc%sd#,X(N.%262N+fK');
define('SECURE_AUTH_KEY',  'hTuv2/RwZ~ZDlCbrG?nCL4.[*n?.;]Kh!9-Q5I^obrENEFH9R?-E##*dlE+AYy32');
define('LOGGED_IN_KEY',    'Yrb/scKba6d E >DduYk0{!G{|Nc9uQ`u?!][bVVb|T4Y;l*lz)gP=PQFaik[&ol');
define('NONCE_KEY',        'B8!B [>=pg)bKuNry62&YXh>VAPpgb-Az7d=Vw5q(!-|ClvZ*Q~PZ2I,o;juZ9Sm');
define('AUTH_SALT',        'J=v)vS?!Dd%A-DBiqG6Y_DV({aE&|LZ(Uq/#)Cf}+LdOi39yJWnLnm0Lsz+nf7v{');
define('SECURE_AUTH_SALT', 'rK%io~g~5E>>DN4+`0zx}D:KDt=@2cc138b])q?g*rik=!b)$9Eoj;u~{Y~pSvog');
define('LOGGED_IN_SALT',   ')On!gQ$[peB7R-P]B2-nioPOouUe^qm$vR*]XC%,Ky1^z_Q1d=`vREis,QrMn92t');
define('NONCE_SALT',       '=ty!zs4ICsLS|8&?>eqUOVO(Rf|wNj04qi)*w7[KU3kHH|lt<|l|aWMH;M>e;LOs');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'jd_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define( 'FS_METHOD', 'ftpext' );
define( 'FTP_BASE', '/workspace/www/' );
define( 'FTP_CONTENT_DIR', '/workspace/www/wp-content/' );
define( 'FTP_PLUGIN_DIR ', '/workspace/www/wp-content/plugins/' );
define( 'FTP_PUBKEY', '/home/root/.ssh/id_rsa.pub' );
define( 'FTP_PRIKEY', '/home/root/.ssh/id_rsa' );
define( 'FTP_USER', 'root' );
define( 'FTP_PASS', 'cgeek' );
define( 'FTP_HOST', '192.168.1.2:23662' );
define( 'FTP_SSL', false );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');