<?
//TODO : Auhtorizate user
if($USER->IsAuthorized()):
?>
<div class="form-sign">
    <p class="h2 greeting-user"><?= GetMessage("ITGAZIEV_SIGNUP_GREETING_TITLE", ['#USER_NAME#' => $USER->GetFullName()]) ?></p>
    <a href="?logout" class="btn btn-primary logout-user"><?= GetMessage("ITGAZIEV_SIGNUP_LOGOUT") ?></a>
</div>
<?
//TODO : User save data
elseif($arResult['USER_ID'] && $arResult['COMPARE']):
?>
<div class="form-signin">
    <div class="sign changeuser">
        <form action="<?= $APPLICATION->GetCurPage() ?>?ajax_sign=saveuser" class="signin">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="user_id" value="<?= $arResult['USER_ID'] ?>" />
            <div class="text-center mb-4">
                <p class="h2"><?= GetMessage("ITGAZIEV_SIGNUP_CHANGE_USERDATA_TITLE") ?></p>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="text" id="inputFirstName" name="user_first_name" class="form-control" placeholder="First Name" required>
                <label for="inputUsername"><?= GetMessage("ITGAZIEV_SIGNUP_USER_FIRST_NAME") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="text" id="inputLastName" name="user_last_name" class="form-control" placeholder="Last Name" required>
                <label for="inputLastName"><?= GetMessage("ITGAZIEV_SIGNUP_USER_LAST_NAME") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="tel" id="inputPhone" name="user_contact_phone" class="form-control" placeholder="+7(___) ___ - __ - __" required>
                <label for="inputPhone"><?= GetMessage("ITGAZIEV_SIGNUP_USER_CONTACT_PHONE") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="password" id="inputPassword" name="user_password" class="form-control" placeholder="Password" required>
                <label for="inputPassword"><?= GetMessage("ITGAZIEV_SIGNUP_USER_PASSWORD") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="password" id="inputRepeatPassword" name="user_confirm_password" class="form-control" placeholder="Repeat Password" required>
                <label for="inputRepeatPassword"><?= GetMessage("ITGAZIEV_SIGNUP_USER_REPEAT_PASSWORD") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <input class="btn btn-lg btn-primary btn-block" type="submit" value="<?= GetMessage("ITGAZIEV_SIGNUP_BTN_SAVE") ?>" name="saveuser"/>
        </form>
    </div>
</div>
<?
elseif($arResult['FAILED'] || !$arResult['COMPARE']):
//TODO : The check word is deprecated
?>
<div class="form-sign">
    <p class="greeting-user">Ошибка подтверждения почты, ссылка устарела!</p>
</div>
<?
else:
//TODO : Sign In & Sign Up
?>
<div class="form-signin" data-initial="signup">
    <div class="tab-sign signin">
        <form action="<?= $APPLICATION->GetCurPage() ?>?ajax_sign=signin" class="signin">
            <?=bitrix_sessid_post()?>

            <div class="text-center mb-4">
                <p class="h2"><?= GetMessage("ITGAZIEV_SIGNUP_SIGNIN_TITLE") ?></p>
            </div>
            <div class="note-form"></div>
            <div class="form-label-group">
                <input type="email" id="inputLogin" class="form-control" name="user_email" placeholder="Email address" required autofocus>
                <label for="inputLogin"><?= GetMessage("ITGAZIEV_SIGNUP_USER_EMAIL") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-label-group">
                <input type="password" id="inputPassword" class="form-control" name="user_password" placeholder="Password" required>
                <label for="inputPassword"><?= GetMessage("ITGAZIEV_SIGNUP_USER_PASSWORD") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <div class="checkbox mb-3">
                <label><input type="checkbox" name="user_remember" value="remember-me"><?= GetMessage("ITGAZIEV_SIGNUP_USER_REMEMBER") ?></label>
            </div>
            <input class="btn btn-lg btn-primary btn-block" type="submit" value="<?= GetMessage("ITGAZIEV_SIGNUP_BTN_SIGN_IN") ?>" name="signin" />
            <button class="btn btn-lg btn-light btn-block initialBtn" data-toggle-initial="signup" type="button"><?= GetMessage("ITGAZIEV_SIGNUP_BTN_SIGN_UP") ?></button>
        </form>
    </div>
    <div class="tab-sign signup">
        <form action="<?= $APPLICATION->GetCurPage() ?>?ajax_sign=signup" class="signin" method="post" enctype="multipart/form-data">
            <?=bitrix_sessid_post()?>

            <div class="text-center mb-4">
                <p class="h2"><?= GetMessage("ITGAZIEV_SIGNUP_SIGNUP_TITLE") ?></p>
            </div>
            <div class="note-form"></div>
            <div class="form-label-group">
                <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
                <label for="inputEmail"><?= GetMessage("ITGAZIEV_SIGNUP_USER_EMAIL") ?></label>
                <div class="invalid-feedback"></div>
            </div>
            <input class="btn btn-lg btn-primary btn-block" type="submit" value="<?= GetMessage("ITGAZIEV_SIGNUP_BTN_SIGN_UP") ?>" name="signup"/>
            <button class="btn btn-lg btn-light btn-block initialBtn" data-toggle-initial="signin" type="button"><?= GetMessage("ITGAZIEV_SIGNUP_HAVE_ACCOUNT") ?></button>
        </form>
    </div>
</div>
<? endif; ?>