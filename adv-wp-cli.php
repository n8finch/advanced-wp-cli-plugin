<?php
/**
 * Plugin Name:     Adv Wp Cli
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     adv-wp-cli
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Adv_Wp_Cli
 */

/**
 * Implements example command.
 */
class Greeting {

    /**
     * Prints a greeting.
     *
     * ## OPTIONS
     *
     * <name>
     * : The name of the person to greet.
     *
     * [--type=<type>]
     * : Whether or not to greet the person with success or error.
     * ---
     * default: success
     * options:
     *   - success
     *   - error
     *   - warning
     * ---
     *
     * ## EXAMPLES
     *
     *     wp example hello Newman
     *
     * @when after_wp_load
     */
    function hello( $args, $assoc_args ) {
        list( $name ) = $args;
		print_r($args);
		print_r($assoc_args);
        // Print the message with type
        $type = $assoc_args['type'];
        WP_CLI::$type( "Hello, $name!" );
    }

	public function __invoke( $args ) {
		print_r($args);
		if ( 'hello' === $args[0] ) {
			WP_CLI::success( "Top of the morning, $args[1]" );
		}
		if ( 'hey' === $args[0] ) {
			WP_CLI::warning( "Top of the morning, $args[1]" );
		}
		if ( 'what' === $args[0] ) {
			WP_CLI::error( "Top of the morning, $args[1]" );
		}
    }
}

WP_CLI::add_command( 'greeting', 'Greeting' );

/**
 * Implements example command.
 */
class User_Seeder {

    /**
     * Seeds users. Sees 5 users.
     *
     * ## EXAMPLES
     *
     *     wp seed-users
     *
     * @when after_wp_load
     */

	public function __invoke() {
		$counter = 0;
		while( 5 > $counter ) {
			$login = substr( hash( 'md5', rand() * 10 ), 0, 10 );
			$email = $login . '@sample.com'; 
			$role = 'bad-role';

			WP_CLI::runcommand( "user create $login $email --role=$role --porcelain" );

			$counter++;
		}
		WP_CLI::success( "Users created." );
	}
}

WP_CLI::add_command( 'seed-users', 'User_Seeder' );

/**
 * Implements example command.
 */
class Role_Updater {

    /**
     * Updates bad-roles to good-roles
     *
	 * ## OPTIONS
	 * [--dry-run]
	 * : Whether or not to perform a dry-run. If the flag is not included, the update will run.
     * ## EXAMPLES
     *
     *     wp update-roles [--dry-run]
     *
     * @when after_wp_load
     */

	public function __invoke( $args, $assoc_args ) {
		$dry_run = $assoc_args['dry-run'] ?? false;
		$counter = 0;

		$users = get_users( [ 'role' => 'bad-role' ] );

		if ( empty( $users ) ) {
			WP_CLI::error( 'No bad-role users found.' );
			return;
		}

		foreach ( $users as $user ) {
			if ( ! $dry_run ) {	
				$user->add_cap( 'good-role' );
				$user->remove_cap( 'bad-role' );
			}
			$counter++;
		}
		if ( $dry_run ) {
			WP_CLI::warning( 'Dry run, nothing changed.' );
		}
		WP_CLI::success( "$counter Users updated.");
	}
}

WP_CLI::add_command( 'update-roles', 'Role_Updater' );

/**
 * Implements example command.
 */
class Remove_Seeds {

    /**
     * Updates bad-roles to good-roles
     *
	 * ## OPTIONS
	 * [--dry-run]
	 * : Whether or not to perform a dry-run. If the flag is not included, the update will run.
     * ## EXAMPLES
     *
     *     wp update-roles [--dry-run]
     *
     * @when after_wp_load
     */

	public function __invoke( $args, $assoc_args ) {
		$dry_run = $assoc_args['dry-run'] ?? false;
		$counter = 0;

		$users = get_users( [ 'role' => 'good-role' ] );

		if ( empty( $users ) ) {
			WP_CLI::error( 'No good-role users found.' );
			return;
		}

		foreach ( $users as $user ) {
			if ( ! $dry_run ) {	
				wp_delete_user( $user->ID );
			}
			$counter++;
		}
		if ( $dry_run ) {
			WP_CLI::warning( 'Dry run, nothing changed.' );
		}
		WP_CLI::success( "$counter Users removed.");
	}
}

WP_CLI::add_command( 'remove-seeds', 'Remove_Seeds' );
