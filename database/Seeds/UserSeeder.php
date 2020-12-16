<?php


namespace Database\Seeds;


use App\Core\Database\Connection;
use App\Core\Database\SeederInterface;

class UserSeeder implements SeederInterface
{

    public function seed()
    {
        $seeds = [
            [
                'username' => 'Admin',
                'email' => 'admin@chat.test',
                'type' => 'admin',
                'password' => password_hash(1234, PASSWORD_DEFAULT),
                'time' => time(),
            ],
            [
                'username' => 'Ahmard',
                'email' => 'ahmard@chat.test',
                'type' => 'user',
                'password' => password_hash(1234, PASSWORD_DEFAULT),
                'time' => time(),
            ],
            [
                'username' => 'Anonymous',
                'email' => 'anonymous@chat.test',
                'type' => 'admin',
                'password' => password_hash(1234, PASSWORD_DEFAULT),
                'time' => time(),
            ]
        ];

        foreach ($seeds as $seed){
            Connection::get()
                ->query('INSERT INTO users(username, email, type, password, time) VALUES (?, ?, ?, ?, ?)', array_values($seed))
                ->otherwise(function (\Throwable $throwable){
                    var_dump($throwable->getMessage());
                });
        }
    }
}