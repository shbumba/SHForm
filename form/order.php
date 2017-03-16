<?php
include_once 'backend/bootstrap.php';

$info = include 'info.php';

$dataForm = isset($info['dataForm']) ? $info['dataForm'] : array();
$items = isset($info['items']) ? $info['items'] : array();

$isAjax = is_ajax();

if ($isAjax) {
    header('Content-Type: application/json');
} else {
    header(sprintf('HTTP/%s %s %s', '1.0', 200, 'OK'), true, 200);
}

$sendName = $sendPhone = $sendEmail = $sendItem = '';

$itemID = 0;
$itemData = $dataFormSet = array();

if (!isset($_POST['item-id'])) {
    $_POST['item-id'] = $itemID;
}

PrepareInput::setData($_POST);

$errorOrderText = $successText = array();
$success = false;

if (PrepareInput::has('item-id')) {
    $itemID = PrepareInput::prepare('item-id');

    if (CheckInput::data($itemID, $items)) {
        $itemData = $items[$itemID];
        $dataFormSet = $itemData['data'];
    }
}

if (PrepareInput::has('orderForm')) {
    $mailTitle = 'Заполнена форма';

    $sendName = PrepareInput::prepare('orderForm.name');
    $sendPhone = PrepareInput::prepare('orderForm.phone');
    $sendEmail = PrepareInput::prepare('orderForm.email');
    $sendItem = PrepareInput::prepare('orderForm.item');
    $sendType = PrepareInput::prepare('orderForm.tip-bani');
    $sendView = PrepareInput::prepare('orderForm.vid-bani');

    if (!CheckInput::string($sendName, 3)) {
        $errorOrderText['name'] = str_replace('{field}', '"Имя"', CheckInput::getError());
    }

    if (!CheckInput::phone($sendPhone)) {
        $errorOrderText['phone'] = CheckInput::getError();
    }

    /*if (!CheckInput::email($sendEmail)) {
        $errorOrderText['email'] = CheckInput::getError();
    }*/

    if (!CheckInput::data($sendItem, $items)) {
        $errorOrderText['item'] = str_replace('{field}', '"Элемент"', CheckInput::getError());
    }

    if (!CheckInput::data($sendType, $dataForm['tip-bani'])) {
        $errorOrderText['tip-bani'] = str_replace('{field}', '"Тип бани"', CheckInput::getError());
    }

    if (!CheckInput::data($sendView, $dataForm['vid-bani'])) {
        $errorOrderText['vid-bani'] = str_replace('{field}', '"Вид бани"', CheckInput::getError());
    }
    if (count($errorOrderText) === 0) {
        $getDataVal = function ($result, $data) {
            return array_map(function ($val) use ($data) {
                return $data[$val];
            }, (array) $result);
        };

        $sendItem = $getDataVal($sendItem, $items);
        $sendItem = array_map(function ($val) {
            return $val['name'];
        }, $sendItem);
        $sendType = $getDataVal($sendType, $dataForm['tip-bani']);
        $sendView = $getDataVal($sendView, $dataForm['vid-bani']);

        $dataMail = array(
            'name' => $sendName,
            'phone' => $sendPhone,
            'email' => $sendEmail,
            //'item' => implode('|', $sendItem),
            'type' => implode('|', $sendType),
            'view' => implode('|', $sendView)
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
            <tr>
                <td>Почта</td>
                <td>' . $dataMail['email'] . '</td>
            </tr>
            <tr>
                <td>Тип</td>
                <td>' . $dataMail['type'] . '</td>
            </tr>
            <tr>
                <td>Вид</td>
                <td>' . $dataMail['view'] . '</td>
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
        '$sendEmail' => $sendEmail,
        '$itemID' => $itemID,
        '$items' => $items,
        '$dataForm' => $dataForm,
        '$itemData' => $itemData,
        '$dataFormSet' => $dataFormSet,
        '$isAjax' => $isAjax,

        'error' => $errorOrderText,
        'success' => $successText
    );

    unset($sendName, $sendPhone, $sendEmail, $sendItem, $items, $dataForm, $errorOrderText, $successText, $dataMail, $mailTitle);

    include_once 'template/order.html';
}
