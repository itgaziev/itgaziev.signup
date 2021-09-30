<?php
use \Bitrix\Main\Localization\Loc;
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/itgaziev.signup/prolog.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/itgaziev.signup/include.php';

if(!check_bitrix_sessid()) return;

if($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage(array(
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        'HTML' => true
    ));
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}

?>
<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>" />
    <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>" />
</form>