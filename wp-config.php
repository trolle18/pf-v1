<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sofietrolle_dkwordpress' );

/** Database username */
define( 'DB_USER', 'sofietrolle_dkwordpress' );

/** Database password */
define( 'DB_PASSWORD', '5Sizd)PoDc!fMUG3ZEnK' );

/** Database hostname */
define( 'DB_HOST', 'sofietrolle.dk.mysql.service.one.com' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'qMhoVccpWQTgQyMbs4_NQ5hi265r0DZFZoRIN1W9UeA=' );
define( 'SECURE_AUTH_KEY',  'FT8pKoaIIyCzIcjCiOipkkacYRK7YvP5iEOuKDfXyv0=' );
define( 'LOGGED_IN_KEY',    'BF8buwGlxMV9XRpmrPsJQj0Ay4JWYAqgHLaHmVUo304=' );
define( 'NONCE_KEY',        '4KN8BSyxgwYYNYPAu1JdUarZ4n6xJM9SPPrU703cnG0=' );
define( 'AUTH_SALT',        'K7h61v8TEQJVHgfmPA1oAU8Gl5ySvayRMWtN-rSOj4k=' );
define( 'SECURE_AUTH_SALT', 'pGiJw1FTJOcBCYJlxoTMugDs_ldEYDCGZFO16nmlMH8=' );
define( 'LOGGED_IN_SALT',   '_NET4bchBPkJUMWDrFb-bkwMRoXoZ-LihUYqSsWTxU0=' );
define( 'NONCE_SALT',       'zTkQ1PuHwOWJkmNPVlHbpj1Fl8UQEKAd7S4e042ZdHI=' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'www0_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define( 'WPLANG', 'en_GB' );

/**
 * Get email from control email
 *
 * Just set to default email fields during 1-click installation
 */
define( 'WPEMAIL', '' );

/**
 * Prevent file editing from WP admin.
 * Just set to false if you want to edit templates and plugins from WP admin.
 */
define('DISALLOW_FILE_EDIT', true);

/**
 * API for One.com wordpress themes and plugins
 */
define('ONECOM_WP_ADDONS_API', 'https://wpapi.one.com');

/**
 * Client IP for One.com logs
 */
if (getenv('HTTP_CLIENT_IP')){$_SERVER['ONECOM_CLIENT_IP'] = @getenv('HTTP_CLIENT_IP');}
else if(getenv('REMOTE_ADDR')){$_SERVER['ONECOM_CLIENT_IP'] = @getenv('REMOTE_ADDR');}
else{$_SERVER['ONECOM_CLIENT_IP']='0.0.0.0';}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';