<?php

//Alternativen: https://stackoverflow.com/questions/14752470/creating-a-config-file-in-php

return array(
    // Datenbank lokal...
    'env' => 'dev', // 'prod'
    'env_dev' => [
        'dsn' => 'mysql:dbname=my_database;host=127.0.0.1',
        'user' => 'username',
        'password' => 'password',
        'mailaccount' => [
            'smtp_server' => 'smtp.xxxxxxxxxxx.com',
            'username' => 'username',
            'password' => 'password',
            'email_from_email' => 'from_email@address.com',
            'email_from_name' => 'from_email_name',
            'email_to_email' => 'info_to_email@address.com'
        ]
    ],
    'env_prod' => [
        'dsn' => 'mysql:dbname=my_database;host=127.0.0.1',
        'user' => 'username',
        'password' => 'password',
        'mailaccount' => [
            'smtp_server' => 'smtp.xxxxxxxxxxx.com',
            'username' => 'username',
            'password' => 'password',
            'email_from_email' => 'from_email@address.com',
            'email_from_name' => 'from_email_name',
            'email_to_email' => 'info_to_email@address.com'
        ]
    ]   
);
        
    

