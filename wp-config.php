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
define( 'DB_NAME', 'autometrix' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         '~Jjq>c=5Kh7.)u%]V5G>f-NyvWkqp3i7Y`k%L[W7J{{(dLQm>av*]X#(:/vj7;BG' );
define( 'SECURE_AUTH_KEY',  '%MXUOsQ7ppNBq)9THknwgN =./:O{Be< t}:~Lwa&7Ez]h4KhM[OB[(IC$7[rVSV' );
define( 'LOGGED_IN_KEY',    'lfg*rk)km}X7r/Nj gq7IfoHWUkOJi1.Z+}!o>;Y?r,WYD5|wRdzcwyW0f+rd3h9' );
define( 'NONCE_KEY',        'Hb^HYFxxis0IT&|1a-!3K!wv+?arHV3!1^@>HK*)r^T$tZZh/~S$Rl2Y_ItAxyP`' );
define( 'AUTH_SALT',        'YkMfK~WRG(-<G7Lz`# >FK%b5TPW&{E55^dP,DUEDlm#}:)q5PZuab$H6G,7dzP6' );
define( 'SECURE_AUTH_SALT', 'XWUy}2^a+[75.6;*3`u`&Z(&qYZ{:[7%Z*w`MAS@F+k<ic*&<L=>qf~;oJipY5](' );
define( 'LOGGED_IN_SALT',   'CJAN]T_6yfc;[?oh)P4d0QnQ~]L32Qj7@V3@p~Q(#bUCqS?Hv_ji:cYQ}_me}#@_' );
define( 'NONCE_SALT',       'RlD`vT1 !=No+Ghm?$2smQb*;Z@8C`PBn,`I,rSn%p[0/`w/TYU_(m]No9975Iu|' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpbh_';

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
