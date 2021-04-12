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
define( 'DB_NAME', 'veats' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'zDrlSJ(1 !qSD@2TU~(E)oj>6G|`>9J}_D^NhD_s%iP`I3jni2HOth3YI{5!/o]-' );
define( 'SECURE_AUTH_KEY',  'lJ2x4V[af?pI.%IW>56Yn>OCvd/VmTAWk2=`DiRiTG|gPmwzg$#m#wFf?{wvU]R-' );
define( 'LOGGED_IN_KEY',    '{Qz<O/U1$o/S>leNoj0h#i51)OE!:3i`;E%y.KbN=AE9c:0Vd:f4rP&P8uYGJkkf' );
define( 'NONCE_KEY',        '=nfM$T.|}8bt{Em%w&w8vw7&,rC6`_~6F1Do[{pbaZEz]AH;%[NNO]Qad8}dAqAl' );
define( 'AUTH_SALT',        '^3$qe_=R5a9fQy_[Fc5rU=pIZLu8dj<`#c{O:#p{sOF.q%??Xa}Rh4Xb8CAS<O:x' );
define( 'SECURE_AUTH_SALT', 'W/Cj;/?%cM~o1%6DefiX?]9IP[W&x!6|Gp+}]&2-Ek,5?-}aPr#&!4zWEYIvu6#p' );
define( 'LOGGED_IN_SALT',   '^?N.{69p@-.KV@Uu~x`3tJamx}GR){$5P2/[f|RrVZ {h JiuSv+7CN=E%Z1.*bQ' );
define( 'NONCE_SALT',       'iW,;_<z_;9<-v &{.MjOX&UGJ|H}P$4@0%S],}Eb>?24%[7I>M@DsrKFza5dK<F?' );

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

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
