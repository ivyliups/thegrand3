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
define( 'DB_NAME', 'booloola_thegrand' );

/** MySQL database username */
define( 'DB_USER', 'booloola_thegrand' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Csb65zHdJjv9' );

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
define( 'AUTH_KEY',         'sTnW9LE&V;t0/5[^]42U W8tmlLcqe5$G4P=($tOhz3Xq=q,|Qcw*7X>F*wZ%Z0V' );
define( 'SECURE_AUTH_KEY',  '!|NWf5d@ad#34a#=[eT/T#QjntI#7*97tXi!x>5M2Nhb$R7a%7iova^z~Lt6W%|?' );
define( 'LOGGED_IN_KEY',    '|}7r=twt-qjd>Es#<XG$7KmY~!HA)z;6Vd$E<-V5=e)a`p-Xxu1WT]&w xFtA2%M' );
define( 'NONCE_KEY',        '1zfE<6dq{G$@##1]lo`-GXb24)hy1j]|SB[0o&6bejy9pS3u*EC@)a,0wS7ww]%S' );
define( 'AUTH_SALT',        '3H?0KOOh2~Ry{a%wzs~<:z=5/v:=tVZOVZ-.LPCwn$I89Fc;]M#Um=EInLSYd/8-' );
define( 'SECURE_AUTH_SALT', 'evB48S1Td6F^B>3.I,U;(;Z?`.14NprW(AH%99(Q5GOw25i17Q1t DPIU1@{I!U6' );
define( 'LOGGED_IN_SALT',   ';,2F`/ EL6O%x`reQm{pHIG$=$HP9`:.jg%;U3_~2)*VplzZlofiIQ0D2-GXM{ZM' );
define( 'NONCE_SALT',       'j.c]` #p`]Tv%BMam@(_0!7(Lm*9l^G=W{q,k-1)sTR]9W92N_t3O%5;!@1Gmrg]' );

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
