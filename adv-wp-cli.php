<?php

/**
 * Plugin Name:     Advanced Wp Cli
 * Plugin URI:      https://n8finch.com
 * Description:     Advanced WP-CLI custom command.
 * Author:          Nate Finch
 * Author URI:      YOUR SITE HERE
 * Text Domain:     advanced-wp-cli
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Advanced_Wp_Cli
 */

class Hello {
	public function __invoke( $args, $assoc_args ) {
		if ( 'hello' === $args[0] ) {
			WP_CLI::success( 'Top of the morning, ' . $args[1] );
		}
		if ( 'hey' === $args[0] ) {
			WP_CLI::warning( 'Hey back, ' . $args[1] );
		}
		if ( 'what' === $args[0] ) {
			WP_CLI::error( 'Bleh, ' . $args[1] );
		}
	}
}
WP_CLI::add_command( 'greeting', 'Hello' );


class User_Seeder {

	public function __invoke() {
		$counter = 0;
		while( 25 > $counter ) {
			// Variables
			$login = substr( hash( 'md5', rand() * 10 ), 0, 10 );
			$email = $login . '@notreallyreal.com';
			$role = 'old-role';

			// Create a new user with an old-role.
			WP_CLI::runcommand( "user create $login $email --role=$role --porcelain" );
			$counter++;
		}

		WP_CLI::success( "$counter users were created!");
	}
}
WP_CLI::add_command( 'seed-users', 'User_Seeder' );


class Role_Updater {
	/**
     * Updates user roles from old to new.
     *
     * ## OPTIONS
     *
     * <old>
     * : The key of the role we want to update from.
	 * 
     * <new>
     * : The key of the role we want to update to.
     *
     * [--dry-run[=<type>]]
     * : Whether or not to actually update the data.
     * ---
     * default: true
     * options:
     *   - true
     *   - false
     * ---
     *
     * ## EXAMPLES
     *
     *     wp update-roles old-role new-role --dry-run=false
     *
     * @when after_wp_load
     */
	public function __invoke( $args, $assoc_args ) {
		$dry_run = ( array_key_exists( 'dry-run',  $assoc_args ) && 'false' === $assoc_args['dry-run'] ) ? false : true;
		$counter = 0;
		// get WP Users by old-role
		$users = get_users( [ 'role' => $args[0] ] );

		if ( ! is_array( $users ) || empty( $users ) ) {
			WP_CLI::error( 'No users found.' );
			return false;
		}

		// Loop through and switch roles
		foreach( $users as $key => $user ) {
			if ( ! $user instanceof WP_User ) {
				WP_CLI::warning( "Key $key is not a user." );
				continue;
			}
			if ( ! $dry_run ) {
				$user->remove_cap( $args[0] );
				$user->add_cap( $args[1] );
			}
			
			$counter++;
		}
		// Success
		if ( $dry_run ) {
			WP_CLI::warning( "Dry run, nothing actually updated 😅.");
		}
		WP_CLI::success( "$counter users were updated! 👍 😎");
	}
}
WP_CLI::add_command( 'update-roles', 'Role_Updater' );


