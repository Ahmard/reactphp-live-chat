<?php


namespace Database\Seeds;


use Server\Database\Connection;
use Server\Database\SeederInterface;

class UserSeeder implements SeederInterface
{

    public function seed(): void
    {
        $seeds = [
            [
                'username' => 'Admin',
                'email' => 'admin@chat.test',
                'type' => 'admin',
                'password' => password_hash(1234, PASSWORD_DEFAULT)
            ],
            [
                'username' => 'Ahmard',
                'email' => 'ahmard@chat.test',
                'type' => 'user',
                'password' => password_hash(1234, PASSWORD_DEFAULT)
            ],
            [
                'username' => 'Anonymous',
                'email' => 'anonymous@chat.test',
                'type' => 'admin',
                'password' => password_hash(1234, PASSWORD_DEFAULT)
            ]
        ];

        foreach ($seeds as $seed){
            Connection::get()
                ->query('INSERT INTO users(username, email, type, password) VALUES (?, ?, ?, ?)', array_values($seed))
                ->otherwise(function (\Throwable $throwable){
                    var_dump($throwable->getMessage());
                });
        }
    }
}