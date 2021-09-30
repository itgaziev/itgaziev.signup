<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Страница пользователя");

$APPLICATION->IncludeComponent('itgaziev:signup', '.default', [
    'back_url' => '/'
], false);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>