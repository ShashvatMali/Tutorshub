<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tutorshub' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'T~.+8/j{]!lY_(jzyJTwzWO?y4R8g#,|_aGVL~i7k[XjI%nbWm,vz5f-R?~qfjOH' );
define( 'SECURE_AUTH_KEY',  'g;U ](G/lwdLT3bwSA@!oHg],^s4vY;VN*99v_=x=_sd9%H4]/NJTRo;;{mcuVx%' );
define( 'LOGGED_IN_KEY',    'K&W~MqqdBb[$}{X x4Mq+#]Q2oz6`pl!JlJv|oZsmlbwi!(Usfksz_iC@Lt4JMib' );
define( 'NONCE_KEY',        '@K#9V`8$/Yo8%`kRw)QQt.C&F_ws(-xF*7~v*pI*0aUJx[mYV}YJU`!<x)5?9(sI' );
define( 'AUTH_SALT',        '7;*/v;0{U%N*BIa.o5&@XrC%G5e)SZ]=V+W,+~i|Lzj_dpV3ksm{5j$4H4L8^ lx' );
define( 'SECURE_AUTH_SALT', 'eo4R<&1KpL^p~H;t|N2Z}U8$EmISuofV4GP9BhZb)f(D^0j(Xltytb>z[.ZAY<Js' );
define( 'LOGGED_IN_SALT',   'o9R^PY.jS>Q.6(t_SAi ta!x.Ny-Dpl`SNZ0xL{d[*Z#ofoO])C||3b;?|v y>F:' );
define( 'NONCE_SALT',       '5m>p#lGMz!!pFgsg,^]WS]2k_^B?c+ ~^zR(RK(8dS:wUp~}1f[4|{+aSfq,~nt_' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
