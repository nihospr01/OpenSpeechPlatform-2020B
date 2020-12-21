<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socket:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates communication between server and osp. Make sure osp is running first.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $address = 'localhost';
        $port = '8001';

        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            echo "Socket not created: reason: " . socket_strerror(socket_last_error()) . "\n";
        }

        echo "Connecting socket. \n";

        if (($connection = socket_connect($sock, $address, $port)) === false){
            echo "Socket could not connect: reason: " . socket_strerror(socket_last_error()) . "\n";
        }

        echo "Socket connected successfully. \n";


        //32 length
        $request_action = ["REQUEST_ACTION" => 3, "VERSION" => 2];
        $user_str_len = ["LS" => 6];
        //17 length
        $user = ["URI" => "us_rhr"];


        echo "writing: ".json_encode($request_action)." ";
        echo socket_write($sock, json_encode($request_action), 32);
        echo "\n";
        echo "writing: ".json_encode($user_str_len)." ";
        echo socket_write($sock, json_encode($user_str_len), 8);
        echo "\n";
        echo "writing: ".json_encode($user)." ";
        echo socket_write($sock, json_encode($user), 17);
        echo "\n";


        $listening_socket = socket_create_listen('8005');
        do{
            $other_socket = socket_accept($listening_socket);
            $input = socket_read($other_socket, 2024);

            if(strlen($input) > 0){
                echo $input."\n";

                socket_write($sock, $input, strlen($input));

                $input = '';
            }
        } while (true);


    }
}
