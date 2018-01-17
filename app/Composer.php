<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2017
 * @package aimeos
 */


namespace App;

use Composer\Script\Event;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Performs setup during composer installs
 *
 * @package aimeos
 */
class Composer
{
	/**
	 * Creates a new admin account.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function account( Event $event )
	{
		$io = $event->getIO();

		$io->write( 'Create admin account' );
		flush(); // Enforce order of messages

		$email = $io->ask( '- E-Mail: ' );
		$passwd = $io->askAndHideAnswer( '- Password: ' );

		$options = [
			escapeshellarg( $email ),
			'--password=' . escapeshellarg( $passwd ),
			'--super',
			'--admin'
		];

		self::executeCommand( $event, 'aimeos:account', $options );
	}


	/**
	 * Configures the .env file.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function configure( Event $event )
	{
		$io = $event->getIO();
		$filename = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . '.env';

		if( ( $content = file_get_contents( $filename ) ) === false ) {
			throw \RuntimeException( sprintf( 'Can not read file "%1$s"', $filename ) );
		}

		$matches = [];
		if( preg_match( "/^APP_KEY\=(.*)$/m", $content, $matches ) === 1 ) {
			$content = preg_replace( "/^APP_KEY\=.*$/m", 'APP_KEY="' . trim( $matches[1], '"' ) . '"', $content );
		}

		if( ( $config = parse_ini_string( $content ) ) === false ) {
			throw \RuntimeException( sprintf( 'Can not parse file "%1$s"', $filename ) );
		}


		$io->write( 'Database setup' );
		flush(); // Enforce order of messages

		foreach( ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'] as $key ) {
			$config[$key] = $io->ask( '- ' . $key . ' (' . $config[$key] . '): ', $config[$key] );
		}
		$config['DB_PASSWORD'] = $io->askAndHideAnswer( '- DB_PASSWORD: ', $config['DB_PASSWORD'] );

		$io->write( 'Mail setup' );
		flush(); // Enforce order of messages

		foreach( ['MAIL_DRIVER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_ENCRYPTION', 'MAIL_USERNAME'] as $key ) {
			$config[$key] = $io->ask( '- ' . $key . ' (' . $config[$key] . '): ', $config[$key] );
		}
		$config['MAIL_PASSWORD'] = $io->askAndHideAnswer( '- MAIL_PASSWORD: ', $config['MAIL_PASSWORD'] );

		if( file_put_contents( $filename, self::createIniString( $config ) ) === false ) {
			throw \RuntimeException( sprintf( 'Can not write file "%1$s"', $filename ) );
		}
	}


	/**
	 * Sets up the shop database.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function setup( Event $event )
	{
		$options = [];

		if( $event->isDevMode() ) {
			$options[] = '--option=setup/default/demo:1';
		}

		self::executeCommand( $event, 'aimeos:setup', $options );
	}


	/**
	 * Creates a INI file compatible string from key/value pairs
	 *
	 * @param array $config Associative list of key/value pairs
	 * @return string INI file compatible string
	 */
	protected static function createIniString( array $config )
	{
		$content = '';

		foreach( $config as $key => $value ) {
			$content .= $key . '=' . ( is_bool( $value ) ? (int) $value : $value ) . "\n";
		}

		return $content . "\n";
	}


	/**
	 * Executes a Symphony command.
	 *
	 * @param Event $event Command event object
	 * @param string $cmd Command name to execute, e.g. "aimeos:update"
	 * @param array List of configuration options for the given command
	 * @throws \RuntimeException If the command couldn't be executed
	 */
	protected static function executeCommand( Event $event, $cmd, array $options = array() )
	{
		$process = new Process( self::getPhp() . ' artisan ' . $cmd . ' ' . implode( ' ', $options ) );
		$process->setWorkingDirectory( getcwd() )->setTimeout( null )->setIdleTimeout( null );

		$process->run( function( $type, $buffer ) use ( $event ) {
			$event->getIO()->write( $buffer, false );
		} );

		if( !$process->isSuccessful() ) {
			throw new \Symfony\Component\Process\Exception\ProcessFailedException( $process );
		}
	}


	/**
	 * Returns the path to the PHP interpreter.
	 *
	 * @return string Path to the PHP command
	 * @throws \RuntimeException If PHP interpreter couldn't be found
	 */
	protected static function getPhp()
	{
		$phpFinder = new PhpExecutableFinder;

		if( !( $phpPath = $phpFinder->find() ) ) {
			throw new \RuntimeException( 'The php executable could not be found, add it to your PATH environment variable and try again' );
		}

		return $phpPath;
	}
}
