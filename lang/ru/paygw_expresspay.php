<?php
/**
 * Strings for component 'paygw_expresspay', language 'ru'
 *
 * @package    paygw_expresspay
 * @copyright  LLC «TriInkom»
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['amountmismatch'] = 'The amount you attempted to pay does not match the required fee. Your account has not been debited.';
$string['authorising'] = 'Authorising the payment. Please wait...';
// Settings
$string['testmode'] = 'Использовать тестовый режим';
$string['testmode_help'] = 'Использовать тестовый режим';
$string['serviceno'] = 'Номер услуги';
$string['serviceno_help'] = 'Номер услуги в севрисе Эксперсс Палтежи';
$string['token'] = 'API-ключ';
$string['token_help'] = 'Генерируется в личном кабинете серваиса Эксперсс Палтежи';
$string['secretwordforsigninginvoices'] = 'Секретное слово для подписи счетов';
$string['secretwordforsigninginvoices_help'] = 'Секретное слово, известное только серверу и клиенту. Используется для создания цифровой подписи. Установить в панели express-pay.by';
$string['usedigitallysignnotifications'] = 'Использовать цифровую подпись для подписи уведомлений';
$string['usedigitallysignnotifications_help'] = 'Использовать цифровую подпись для подписи уведомлений';
$string['secretwordforsigningnotifications'] = 'Секретное слово для подписи уведомлений';
$string['secretwordforsigningnotifications_help'] = 'Секретное слово, известное только серверу и клиенту. Используется для создания цифровой подписи. Установить в панели express-pay.by';
$string['notificationlink'] = 'Адрес для уведомлений';
$string['notificationlink_help'] = 'Адрес для уведомлений установите его в настройках сервиса';
$string['isnameeditable'] = 'Разрешено изменение имени плательщика';
$string['isnameeditable_help'] = 'Допускается изменение имени плательщика при оплате счета';
$string['isaddresseditable'] = 'Разрешено менять адрес плательщика';
$string['isaddresseditable_help'] = 'Допускается изменение адреса плательщика при оплате счета';
$string['isamounteditable'] = 'Разрешено изменять сумму платежа';
$string['isamounteditable_help'] = 'Допускается изменение суммы платежа при оплате счета';
$string['sendemail'] = 'Отправить уведомление по электронной почте клиенту';
$string['sendemail_help'] = 'Отправить уведомление по электронной почте клиенту';
$string['sendsms'] = 'Отправка SMS-уведомления клиенту';
$string['sendsms_help'] = 'Отправка SMS-уведомления клиенту';
//End Setttings
$string['cannotfetchorderdatails'] = 'Не удалось получить детали платежа от Экспресс Платежи.';
$string['gatewaydescription'] = 'Плагин «Экспресс Платежи» для Moodle позволяет автоматически выставлять и отправлять счета, принимать платежи и получать уведомления о проведенных платежах.';
$string['gatewayname'] = 'Экспресс Платежи';
$string['internalerror'] = 'Произошла внутренняя ошибка. Пожалуйста свяжитесь с нами.';
$string['paymentnotcleared'] = 'Платеж не очищен Экспресс Платежи.';
$string['pluginname'] = 'Экспресс Платежи';
$string['pluginname_desc'] = 'Плагин Экспресс Палтежи позволяет получать платежи через сервис Экспресс Платежи';
$string['privacy:metadata'] = 'Плагин Экспресс Палтежи не хранит никаких личных данных.';
$string['repeatedorder'] = 'Этот заказ уже был обработан ранее.';
