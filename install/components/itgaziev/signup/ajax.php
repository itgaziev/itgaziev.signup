<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Authentication\Internal\UserPasswordTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;
use Bitrix\Main\Security\Password;
use Bitrix\Main\Mail\Event;

Loc::loadMessages(__FILE__);

global $USER, $DB, $APPLICATION;

if(isset($_GET['ajax_sign']) && $_GET['ajax_sign'] == 'signup') {
    $USER_EMAIL = $_POST['email'];

    $arResult['errors'] = [];
    $dbUser = CUser::GetList(array(), "desc", array('EMAIL' => $USER_EMAIL));

    if($arUser = $dbUser->Fetch()) {
        if(!empty($arUser["CHECKWORD"]) && $arUser['ACTIVE'] == 'N') {
            $arResult['errors'][] = ['field' => 'email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ALREADY_MESSAGE_SEND")];
        } else {
            $arResult['errors'][] = ['field' => 'email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ALREADY_EMAIL_BUSY")];
        }
    } else {
        $USER_LOGIN = explode('@', $USER_EMAIL)[0];
        $USER_PASSWORD = Random::getString(8, true);
        $USER_CHECKWORD = md5(uniqid().CMain::GetServerUniqID());
        $CONFIRM_CODE = Random::getString(32, true);

        $arFields = array(
            "LOGIN" => $USER_LOGIN,
            "PASSWORD" => $USER_PASSWORD,
            "CHECKWORD" => Password::hash($USER_CHECKWORD),
            "~CHECKWORD_TIME" => $DB->CurrentTimeFunction(),
            "CONFIRM_PASSWORD" => $USER_PASSWORD,
            "EMAIL" => $USER_EMAIL,
            "ACTIVE" => 'N',
            "CONFIRM_CODE" => $CONFIRM_CODE,
            "SITE_ID" => SITE_ID,
            "LANGUAGE_ID" => LANGUAGE_ID,
            "USER_IP" => $_SERVER["REMOTE_ADDR"],
            "USER_HOST" => @gethostbyaddr($_SERVER["REMOTE_ADDR"]),
            "GROUP_ID" => array(2)
        );

        //TODO: CALL EVENT OnBeforeUserRegister
        $bOk = true;
        $result_message = true;
        // foreach(GetModuleEvents("main", "OnBeforeUserRegister", true) as $arEvent)
        // {
        //     if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
        //     {
        //         if($err = $APPLICATION->GetException())
        //         {
        //             $result_message = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
        //         }
        //         else
        //         {
        //             $APPLICATION->ThrowException("Unknown error");
        //             $result_message = array("MESSAGE"=>"Unknown error"."<br>", "TYPE"=>"ERROR");
        //         }

        //         $bOk = false;
        //         break;
        //     }
        // }

        if($bOk) {
            if($ID = $cuser->Add($arFields)) {
                //TODO : Send Message Confirm
                $CURRENT_PAGE = (CMain::IsHTTPS()) ? "https://" : "http://";
                $CURRENT_PAGE .= $_SERVER["HTTP_HOST"];
                $CURRENT_PAGE .= $APPLICATION->GetCurPage();

                Event::send(array(
                    "EVENT_NAME" => "ITGAZIEV_SIGNUP_USERMAIL",
                    "LID" => SITE_ID,
                    "C_FIELDS" => array(
                        "URI"          => $CURRENT_PAGE . '?checkword=' . $arFields['CHECKWORD'] . '&email=' . $USER_EMAIL,
                        "USER_EMAIL" => $USER_EMAIL,
                    ),
                )); 

                $arResult['message'] = Loc::getMessage("ITGAZIEV_SIGNUP_SUCCESS_SEND");
            }
        } else {
            $arResult['throw'] = $result_message;
        }
    }

    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo json_encode($arResult);
    die;
} else if(isset($_GET['ajax_sign']) && $_GET['ajax_sign'] == 'saveuser') {
    $USER_ID = intval($_POST['user_id']);
    $USER_FNAME = $_POST['user_first_name'];
    $USER_LNAME = $_POST['user_last_name'];
    $USER_CONTACT_PHONE = $_POST['user_contact_phone'];
    $USER_PASSWORD = $_POST['user_password'];
    $USER_CONFIRM_PASSWORD = $_POST['user_confirm_password'];

    //TODO: VALIDATE POST

    $cuser = new CUser;
    $arFields = Array(
        "NAME"              => $USER_FNAME,
        "LAST_NAME"         => $USER_LNAME,
        "PERSONAL_PHONE"    => $USER_CONTACT_PHONE,
        "CHECKWORD"         => "",
        "ACTIVE"            => "Y",
        "GROUP_ID"          => array(2),
        "PASSWORD"          => $USER_PASSWORD,
        "CONFIRM_PASSWORD"  => $USER_CONFIRM_PASSWORD,
    );
    $cuser->Update($USER_ID, $arFields);

    $arResult['message'] = 'success';

    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo json_encode($arResult);
    die;
}