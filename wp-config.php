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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwp' );

/** MySQL database username */
define( 'DB_USER', 'remotedeveloper' );

/** MySQL database password */
define( 'DB_PASSWORD', 'SujanSql@2021#' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'ta>pG!!ads<`jv~|x3S}.j3Nsb]o.|L^o}GYW=>FdSEelRz,+&%(yzFJIGUjPjPW' );
define( 'SECURE_AUTH_KEY',  's[t7c/,x8Z])QV>k1!zLPmWi-1ARscVZ60D85l.2A&liYV;_JH2;u|pIy4kF.5VD' );
define( 'LOGGED_IN_KEY',    'I{p[ZqwqxbgSrQa=M63QErEHMft#wZd@YDv#B5z)Kj6W^|]Vsyx#;>}9]VK1~+Gu' );
define( 'NONCE_KEY',        'NxVb/V(~/d_DM|7M=E,1|Den3/~D6Cr)Moq+DN2eg5!iO+s{&;QgJS$Njn[(`K#(' );
define( 'AUTH_SALT',        'FSEG#L{QGX58RG9HH]HM4Y3EzZ;|>RyqbQkQmS pi}r1@Yn0q[nckR_IJ^}TH(H*' );
define( 'SECURE_AUTH_SALT', 'x4a8P`Sb!H>Mn$LMZ~SmsA*TEVC>(UY$2t1M`;3rtL{iRL]P^{qex)@G.Hm(1^Ww' );
define( 'LOGGED_IN_SALT',   '_1H1_dbIF9I1>f/ta7gn*?@tww^--P3T{tXD]nN5mCMdLT{{A@5DVWxlYk??hA].' );
define( 'NONCE_SALT',       'Rv*:FXqvNC(4]jx:Z;U[szYb_M_=F/Ma+7S@G/o)zBQ;G&>HLAR[x[3l/sKKFJ;~' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'veats_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

define('FS_METHOD', 'direct');

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
