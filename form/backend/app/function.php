<?php
if (!function_exists('is_ajax')) {
    function is_ajax()
    {
        return !array_key_exists('no-ajax', $_REQUEST) && (array_key_exists('ajax', $_REQUEST) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
    }
}

if (!function_exists('sendEMail')) {
    $libPath = realpath(Option::get('path.vendor') . '/phpmailer/PHPMailerAutoload.php');

    if (null !== $libPath) {
        include_once($libPath);

        function sendEMail($cfg, $data, $title, $html)
        {
            $mail = new PHPMailer;
            $mail->isMail();

            if (isset($cfg['mailTo']) && !empty($cfg['mailTo'])) {
                foreach ((array) $cfg['mailTo'] as $val) {
                    $mail->addAddress($val['mail'], $val['name']);
                }
            } else {
                return false;
            }

            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->CharSet = $cfg['CharSet'];

            $fromDefault = false;

            if (isset($data['email']) && !empty($data['email'])) {
                foreach ((array) $data['email'] as $val) {
                    $mail->SetFrom($val, 'User');
                }
            } else if (isset($cfg['mailFrom']) && !empty($cfg['mailFrom'])) {
                foreach ((array) $cfg['mailFrom'] as $val) {
                    $mail->SetFrom($val['mail'], $val['name']);
                }
            } else {
                $fromDefault = true;
            }

            if ($fromDefault) {
                $mail->SetFrom('mail@site.ru', 'Empty');
            }

            $mail->Body = $html;

            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        }
    }
}