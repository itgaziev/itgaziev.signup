<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;

Loc::loadMessages(__FILE__);

class ITGaziev_SIGNUP extends CModule {
    var $exclusionAdminFiles;

    function __construct() {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        $this->exclusionAdminFiles = array(
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php'
        );

        $this->MODULE_ID = 'itgaziev.signup';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('ITGAZIEV_SIGNUP_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITGAZIEV_SIGNUP_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITGAZIEV_SIGNUP_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITGAZIEV_SIGNUP_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    function InstallDB() {
        //Loader::includeModule($this->MODULE_ID);
    }

    function UnInstallDB() {
        //Loader::includeModule($this->MODULE_ID);
    }

    function InstallEvents() {
        // \Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'MODULE', $this->MODULE_ID, '\ITGaziev\Excell\Event', 'eventHandler');
        // TODO : ADD MAIL EVENT
        $rsSites = CSite::GetList($by="sort", $order="desc", array());
        $arSites = [];
        while ($arSite = $rsSites->Fetch())
        {
            $arSites[] = $arSite['LID'];
        }

        $arFilter = array("TYPE_ID" => "ITGAZIEV_SIGNUP_USERMAIL");
        $rsET = CEventType::GetList($arFilter);
        if($arET = $rsET->Fetch()) return;

        $obEventType = new CEventType;
        $obEventType->Add(array(
            "EVENT_NAME"    => "ITGAZIEV_SIGNUP_USERMAIL",
            "NAME"          => "Потвреждение почты нового пользователя",
            "LID"           => "ru",
            "DESCRIPTION"   => "
                #URI# - Ссылка потверждения
                #USER_EMAIL# - Почта нового пользователя
                #DEFAULT_EMAIL_FROM# - E-Mail адрес по умолчанию (устанавливается в настройках)
                #SITE_NAME# - Название сайта (устанавливается в настройках)
                #SERVER_NAME# - URL сервера (устанавливается в настройках)
                "
        ));
        $obEventType->Add(array(
            "EVENT_NAME"    => "ITGAZIEV_SIGNUP_USERMAIL",
            "NAME"          => "Notification of user's mail",
            "LID"           => "en",
            "DESCRIPTION"   => "
                #URI# - Acknowledgment link
                #USER_EMAIL# - New user's mail
                #DEFAULT_EMAIL_FROM# - E-Mail address by default (set in the settings)
                #SITE_NAME# - Site name (set in the settings)
                #SERVER_NAME# - server URL (set in the settings)
            "
        ));

        $obTemplate = new CEventMessage;
        $arr = [];
        $arr["ACTIVE"]      = "Y";
        $arr["EVENT_NAME"]  = "ITGAZIEV_SIGNUP_USERMAIL";
        $arr["LID"]         = $arSites[0];
        $arr["LANGUAGE_ID"] = "en";
        $arr["EMAIL_FROM"]  = "#DEFAULT_EMAIL_FROM#";
        $arr["EMAIL_TO"]    = "#USER_EMAIL#";
        $arr["BCC"]         = "";
        $arr["SUBJECT"]     = "Welcome to #SITE_NAME# - User account activation";
        $arr["BODY_TYPE"]   = "text";
        $arr["MESSAGE"]     = "
        Hi #USER_EMAIL#,

        Your user account with the e-mail address #USER_EMAIL# has been created.

        Please follow the link below to activate your account.
        Click here #URI#

        You will be able to change your (password, username, etc.) once your account is activated.



        The #SITE_NAME# team.

        #SERVER_NAME#. 
        ";
        $obTemplate->Add($arr);
        $arr = [];
        $arr["ACTIVE"]      = "Y";
        $arr["EVENT_NAME"]  = "ITGAZIEV_SIGNUP_USERMAIL";
        $arr["LID"]         = $arSites[0];
        $arr["LANGUAGE_ID"] = "ru";
        $arr["EMAIL_FROM"]  = "#DEFAULT_EMAIL_FROM#";
        $arr["EMAIL_TO"]    = "#USER_EMAIL#";
        $arr["BCC"]         = "";
        $arr["SUBJECT"]     = "Добро пожаловать на сайт #SITE_NAME# - потверждения учетной записи пользователя";
        $arr["BODY_TYPE"]   = "text";
        $arr["MESSAGE"]     = "
        Привет, #USER_EMAIL#!

        Ваша учетная запись пользователя с адресом электронной почты #USER_EMAIL# создана.

        Пожалуйста, перейдите по ссылке ниже, чтобы активировать свою учетную запись.
        Нажмите здесь #URI#

        Вы сможете изменить свой (пароль, имя пользователя и т. Д.), Как только ваша учетная запись будет активирована.



        Команда #SITE_NAME#.

        #SERVER_NAME#. 
        ";
        $obTemplate->Add($arr);
    }

    function UnInstallEvents() {
        // \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'MODULE', $this->MODULE_ID, '\ITGaziev\Excell\Event', 'eventHandler');

        //TODO : REMOVE MAIL EVENT
        $et = new CEventType;
        $et->Delete("ITGAZIEV_SIGNUP_USERMAIL");
    }

    function InstallFiles() {
        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        CopyDirFiles(__DIR__ . '/templates', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates', true, true);
        CopyDirFiles(__DIR__ . '/site', $_SERVER['DOCUMENT_ROOT'] . '/', true, true);
    }

    function UnInstallFiles() {
        DeleteDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components');
        //DeleteDirFiles(__DIR__ . '/site', $_SERVER['DOCUMENT_ROOT'] . '/' . $this->MODULE_ID); // отключил потому что не испортить пользовательские файлы
    }

    function DoInstall() {
        global $APPLICATION;

        if($this->isVersionD7()) {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        } else {
            $APPLICATION->ThrowException(Loc::getMessage("ITGAZIEV_SIGNUP_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("ITGAZIEV_SIGNUP_INSTALL_TITLE"), $this->GetPath() . '/install/step.php');
    }

    function DoUnInstall() {
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request['step'] < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage("ITGAZIEV_SIGNUP_UNINSTALL_TITLE"), $this->GetPath() . '/install/unstep1.php');
        } else if($request['step'] == 2) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();

            if($request['savedata'] != 'Y') $this->UnInstallDB();

            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("ITGAZIEV_SIGNUP_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");
        }
    }

    
    function isVersionD7() {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    function GetPath($notDocumentRoot = false) {
        if($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    function GetModuleRightsList() {
        return array(
            'reference_id' => array('D', 'K', 'S', 'W'),
            'reference' => array(
                '[D] ' . Loc::getMessage("ITGAZIEV_SIGNUP_DENIED"),
                '[K] ' . Loc::getMessage("ITGAZIEV_SIGNUP_READ_COMPONENT"),
                '[S] ' . Loc::getMessage("ITGAZIEV_SIGNUP_WRITE_SETTINGS"),
                '[W] ' . Loc::getMessage("ITGAZIEV_SIGNUP_FULL")
            )
        );
    }
}