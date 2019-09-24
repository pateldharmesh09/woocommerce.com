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
define( 'DB_NAME', 'wordpress3' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'mysql' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'NIY,1GT|7=>PwycX5J6EAR)QO]jKrDOS&i[=hTFeC{@B;mSnDYCZ,p~s6x[B3k6w' );
define( 'SECURE_AUTH_KEY',  'Fy7<M.8hBfxeIPxjX;d*Pa.?qg$`mOJucwC{PY`R%|]M%|-orkG2}3-pv?*xZCOP' );
define( 'LOGGED_IN_KEY',    'os)uz.|2B*VCkN0%%_0vRm%Kc.{R<D3x3}E5uohI?3TOMWko10CH(4N/?Yu6:zHG' );
define( 'NONCE_KEY',        'vTYxx}bJs?mPZ ,RU*x[GL^-5Bg d^/&%H*u6Bg^6<$)JRjIDgOUmpB{m1i;Yf<p' );
define( 'AUTH_SALT',        'tKDVq]9#J6AeP~et`yWz^>V M`9GX1QAORu~jI4q..qRbcu5[@9nj2KLYjvp+0_P' );
define( 'SECURE_AUTH_SALT', 'WaC =_0M?E2_m}u)+<F+,H6L}O=aWdu}7QO1IzhoYz`3UU^X#+EXJ70RHGc8%X{e' );
define( 'LOGGED_IN_SALT',   '&lsG_-4$^I/Po#DqbZsiYWyc*z;B86l+Aa}Yf,=P@}d(!i=)5bv-MV=y/,@DQRgu' );
define( 'NONCE_SALT',       'Y.bm-a!S<8;y=:iv[gB,zUWT|7V:FwQ`ZKW>_0;$PrUlc)VtY%ow93uiQki+2Gwi' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/* remove FTP credentials*/
   define("FS_METHOD", "direct");

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
