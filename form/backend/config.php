<?php
return array(
    'mail' => array(
        'CharSet' => 'UTF-8',
        'SMTPAuth' => false,
        'mode' => 'mail', //sendmail, smtp, mail
        'host' => 'localhost',
        'user' => 'user_name',
        'password' => 'user_pass',
        'mailTo' => array(
            array(
                'mail' => 'to@site.ru',
                'name' => 'To'
            )
        ),
        'mailFrom' => array(
            array(
                'mail' => 'from@site.ru',
                'name' => 'From'
            )
        )
    )
);