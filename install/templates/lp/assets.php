<?
use Bitrix\Main\Page\Asset;

//CSS STYLES
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/bootstrap.css');
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/styles.css');


//JS SCRIPTS
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/jquery.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/popper.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/bootstrap.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/scripts.js');

//STRING HEADER
Asset::getInstance()->addString('<link rel="icon" href="https://it-gaziev.ru/assets/new-design/img/fabicon.png" />');