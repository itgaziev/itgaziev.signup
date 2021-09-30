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

    }

    function UnInstallEvents() {
        // \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'MODULE', $this->MODULE_ID, '\ITGaziev\Excell\Event', 'eventHandler');
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