<?php

/*
 * Определение данных в сообщении о платеже
 *
 * На входе строка с сообщением
 *
 * На выходе массив ['Account' => 'Номер кошелька', 'Amount' => 'сумма платежа',
 *      'Code' => 'код подтверждения', 'Message' => 'текст сообщения']
 *
 * Ограничения
 * Номер кошелька https://kassa.yandex.ru/tech/payout/wallet.html
 *      Номер кошелька пользователя в Яндекс.Деньгах, например 4100175017397. Длина — от 11 до 20 цифр.
 *
 * Код
 *   От 4х до 6ти цифр, судя по ответам эмулятора и картинкам в интернете
 *
 * Сумма
 *   от 1 до 400000 рублей, могут быть копейки (отделены запятой или точкой)
 *   https://money.yandex.ru/page?id=523014
 */

function parseReg($message, $regexp) {
    $result = [];

    $matches = [];
    $match = preg_match($regexp, $message, $matches);

    if (1 == $match) {
        $result = $matches[1];
    }

    return $result;
}

function parseAccount($message) {
    return parseReg($message, '/(\d{11,20})/');
}

function parseAmount($message)
{
    return parseReg($message, '/(\d{1,6}([,.]\d{1,2}))/');
}

function parseCode($message)
{
    return parseReg($message, '/[^\d](\d{4,6})[^,.\d]/');
}

function parse($message) {
    $result = ['Account' => '', 'Amount' => '', 'Code' => ''];
    $messages = ['Account' => 'Не найден номер кошелька', 'Amount' => 'Не найдена сумма', 'Code' => 'Не найден код'];

    foreach ($result as $param => &$value) {
        $func = 'parse' . $param;
        $data = $func($message);
        if ($data) {
            $value = $data;
        } else {
            $result['Message'] = $messages[$param] ?? 'Сообщение не задано';
            break;
        }
    }

    return $result;
}

$result = parse("Никому не говорите пароль! Его спрашивают только мошенники.
    Вы потратите 5728,65р.
Пароль: 20131
Перевод на счет 4100175017397
");

return $result;
