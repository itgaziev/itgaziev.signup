<?
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Authentication\Internal\UserPasswordTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;
use Bitrix\Main\Security\Password;
use Bitrix\Main\Mail\Event;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
Loc::loadMessages(__FILE__);

global $USER, $DB;
$arResult['COMPARE'] = true;
$arResult['FAILED'] = false;
if(isset($_REQUEST['logout'])) {
    $USER->Logout();
} else if(isset($_REQUEST['checkword']) && isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
    //PARSE REQUEST
    $USER_CHECKWORD = htmlspecialcharsbx($_REQUEST['checkword']);
    $USER_EMAIL = htmlspecialcharsbx($_REQUEST['email']);
    //TRY SEARCH USER BY EMAIL
    $dbUser = CUser::GetList(($by="id"), ($order="desc"), array('EMAIL' => $USER_EMAIL));

    if($arUser = $dbUser->fetch()) {
        //TODO : TRY COMPARE CHECKWORD
        $res = \Bitrix\Main\Security\Password::equals($arUser["CHECKWORD"], $USER_CHECKWORD);
        if($res) {
            //TODO : CHECKWORD IS EQUAL
            $arResult['USER_ID'] = $arUser['ID'];
            $arResult['COMPARE'] = true;
            $arResult['FAILED'] = false;
        } else {
            //TODO : The check word is deprecated
            if($arUser['ACTIVE'] == 'Y' && empty($arUser['CHECKWORD'])) {
                //TODO : This mail has already been confirmed
            }
            $arResult['USER_ID'] = $arUser['ID'];
            $arResult['COMPARE'] = false;
            $arResult['FAILED'] = false;
        }
    } else {
        // TODO : Unknown mail
        $arResult['USER_ID'] = null;
        $arResult['FAILED'] = true;
    }
    
} else if(isset($_GET['ajax_sign']) && $_GET['ajax_sign'] == 'signup') {
    $USER_EMAIL = $_POST['email'];
    $arResult['throw'] = null;
    $arResult['errors'] = [];

    $dbUser = CUser::GetList(($by="ID"), ($order="ASC"), array('EMAIL' => $USER_EMAIL));


    if($arUser = $dbUser->Fetch()) {
        if(!empty($arUser["CHECKWORD"]) && $arUser['ACTIVE'] == 'N') {
            $arResult['errors'][] = ['field' => 'email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ALREADY_MESSAGE_SEND")];
        } else {
            $arResult['errors'][] = ['field' => 'email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ALREADY_EMAIL_BUSY")];
        }
    } else {
        $cuser = new CUser;
        $USER_LOGIN = explode('@', $USER_EMAIL)[0] . '-' . time();
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
            "USER_HOST" => $_SERVER["REMOTE_ADDR"],
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
            } else {
                $arResult['throw'] = $cuser->LAST_ERROR;
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
    $arResult['throw'] = null;
    $arResult['errors'] = [];

    $USER_ID = intval($_POST['user_id']);
    $USER_FNAME = $_POST['user_first_name'];
    $USER_LNAME = $_POST['user_last_name'];
    $USER_CONTACT_PHONE = $_POST['user_contact_phone'];
    $USER_PASSWORD = $_POST['user_password'];
    $USER_CONFIRM_PASSWORD = $_POST['user_confirm_password'];
    //TODO: VALIDATE POST
    if($USER_PASSWORD !== $USER_CONFIRM_PASSWORD) {
        $arResult['errors'][] = ['field' => 'user_confirm_password', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_PASSWORD_NOT_EQUAL")];
        $arResult['errors'][] = ['field' => 'user_password', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_PASSWORD_NOT_EQUAL")];
    }
    if(empty($arResult['errors'])) {
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
        
        $res = $cuser->Update($USER_ID, $arFields);
        if($res) {
            $arResult['message'] = 'success';
            $arResult['back_url'] = $arParams['back_url'];
            $USER->Authorize($USER_ID, 'Y');
        } else {
            $arResult['throw'] = $cuser->LAST_ERROR;
        }
    }

    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo json_encode($arResult);
    die;
} else if(isset($_GET['ajax_sign']) && $_GET['ajax_sign'] == 'signin') {
    $USER_EMAIL = $_POST['user_email'];
    $USER_PASSWORD = $_POST['user_password'];
    $USER_REMEMBER = isset($_POST['user_remember']) ? 'Y' : 'N';
    $USER_LOGIN = false;
    //TODO : Validate post

    //TODO : Find user by email
    $dbUser = CUser::GetList(($by="ID"), ($order="ASC"), array('EMAIL' => $USER_EMAIL, 'ACTIVE' => 'Y'));
    $login_password_correct = false;
    if($arUser = $dbUser->Fetch()) {
        $USER_LOGIN = $arUser['LOGIN']; 
    }

    if($USER_LOGIN) {
        if (!is_object($USER)) $USER = new CUser;

        $arAuthResult = $USER->Login($USER_LOGIN, $USER_PASSWORD, $USER_REMEMBER, 'Y');
        if($arAuthResult === true) {
            $arResult['auth'] = $arAuthResult;
            $arResult['message'] = 'success';
            $arResult['back_url'] = $arParams['back_url'];
        } else {
            $arResult['errors'][] = ['field' => 'user_email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ERROR_SIGNIN")];
            $arResult['errors'][] = ['field' => 'user_password', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ERROR_SIGNIN")];
        }
    } else {
        $arResult['errors'][] = ['field' => 'user_email', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ERROR_SIGNIN")];
        $arResult['errors'][] = ['field' => 'user_password', 'text' => Loc::getMessage("ITGAZIEV_SIGNUP_ERROR_SIGNIN")];
    }

    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo json_encode($arResult);
    die;

}

$this->IncludeComponentTemplate();