<?php
include_once 'backend/bootstrap.php';

$isAjax = is_ajax();

if ($isAjax) {
    header('Content-Type: application/json');
} else {
    header(sprintf('HTTP/%s %s %s', '1.0', 200, 'OK'), true, 200);
}

$sendName = $sendPhone = $sendEmail = $sendItem = '';

PrepareInput::setData($_POST);

$errorOrderText = $successText = array();
$success = false;

if (PrepareInput::has('orderForm')) {
    $mailTitle = 'Обратный звонок';

    $sendName = PrepareInput::prepare('orderForm.name');
    $sendPhone = PrepareInput::prepare('orderForm.phone');

    if (!CheckInput::string($sendName, 3)) {
        $errorOrderText['name'] = str_replace('{field}', '"Имя"', CheckInput::getError());
    }

    if (!CheckInput::phone($sendPhone)) {
        $errorOrderText['phone'] = CheckInput::getError();
    }

    if (count($errorOrderText) === 0) {
        $dataMail = array(
            'name' => $sendName,
            'phone' => $sendPhone
        );

        $sendText = '
        <table>
            <tr>
                <td>Имя</td>
                <td>' . $dataMail['name'] . '</td>
            </tr>
            <tr>
                <td>Телефон</td>
                <td>' . $dataMail['phone'] . '</td>
            </tr>
        </table>
    ';

        if (!sendEMail(Option::get('mail'), $dataMail, $mailTitle, $sendText)) {
            $errorOrderText['sendForm'] = 'Отправка не удалась';
        } else {
            
            $successText['sendForm'] = 'Заявка отправлена';
            $success = true;
            unset($_POST);
        }
    }
}

if ($isAjax) {
    echo json_encode(array(
        'error' => $errorOrderText,
        'success' => $successText
    ), JSON_UNESCAPED_UNICODE);
} else {
    $template = array(
        '$sendName' => $sendName,
        '$sendPhone' => $sendPhone,

        '$isAjax' => $isAjax,

        'error' => $errorOrderText,
        'success' => $successText
    );

    unset($sendName, $sendPhone, $sendItem, $errorOrderText, $successText, $dataMail, $mailTitle);

    include_once 'template/callback.html';
}
