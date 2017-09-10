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

        $reader = new \Piwik\Ini\IniReader();
        $config = $reader->readFile( $filename );

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

        $writer = new \Piwik\Ini\IniWriter();
        $content = $writer->writeToString(['' => $config] );

        if( file_put_contents( $filename, substr( $content, 3 ) ) === false ) {
            throw \RuntimeException( sprintf( 'Could not write file "%1$s"', $filename ) );
        }
    }
}
