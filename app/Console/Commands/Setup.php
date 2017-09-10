<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aimeosdist:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up database and mail services';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = getcwd() . DIRECTORY_SEPARATOR . '.env';

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


        $this->info( 'Database setup' );

        foreach( ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key )
        {
            if( ( $value = $this->ask( $key, $config[$key] ?: false ) ) !== false ) {
                $config[$key] = $value;
            }
        }

        $this->info( 'Mail setup' );

        foreach( ['MAIL_DRIVER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION'] as $key )
        {
            if( ( $value = $this->ask( $key, $config[$key] ?: false ) ) !== false ) {
                $config[$key] = $value;
            }
        }


        if( file_put_contents( $filename, $this->createString( $config ) ) === false ) {
            throw \RuntimeException( sprintf( 'Can not write file "%1$s"', $filename ) );
        }
    }


    protected function createString( array $config )
    {
        $content = '';

        foreach( $config as $key => $value ) {
            $content .= $key . '=' . ( is_bool( $value ) ? (int) $value : $value ) . "\n";
        }

        return $content . "\n";
    }
}
