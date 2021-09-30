<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeTemplateLangFile(__FILE__);
include_once (__DIR__ . '/assets.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <? $APPLICATION->ShowHead(); ?>

    <title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>
<? $APPLICATION->ShowPanel(); ?>
<div class="app">