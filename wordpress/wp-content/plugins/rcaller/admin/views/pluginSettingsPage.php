<?
const SETTINGS_FORM_USERNAME = "rcaller_username";
const SETTINGS_FORM_PASSWORD = "rcaller_password";

$checkCredentialsStatus = null;

/**
 * @return mixed
 */
function isCheckCredentialsRequest()
{
    return $_POST["checkCredentials"];
}

/**
 * @return mixed
 */
function isSaveSettingsRequest()
{
    return $_POST["save"];
}

/**
 * @return bool
 */
function shouldHandlePost()
{
    return isCheckCredentialsRequest() || isSaveSettingsRequest();
}

/**
 * @return bool
 */
function isPostMethod()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function doCheckCredentials()
{
    $userName = $_POST[SETTINGS_FORM_USERNAME];
    $password = $_POST[SETTINGS_FORM_PASSWORD];
    return RCallerSettingsPageRenderer::checkCredentials($userName, $password);
}

function doSaveSettings()
{
    $userName = $_POST[SETTINGS_FORM_USERNAME];
    $password = $_POST[SETTINGS_FORM_PASSWORD];

    RCallerSettingsPageRenderer::saveSettings($userName, $password);
}

if (isPostMethod() && shouldHandlePost()) {
    if (isCheckCredentialsRequest()) {
        $checkCredentialsStatus = doCheckCredentials();
        if ($checkCredentialsStatus === "success") {
            doSaveSettings();
        }
    } else if (isSaveSettingsRequest()) {
        doSaveSettings();
    }
}
?>


<br/>
<br/>
<br/>
<br/>

<div>Configure RCaller credentials</div>

<form method="post">

    <input name="<? echo SETTINGS_FORM_USERNAME ?>" type="text" size="25"
           value="<? form_option(RCallerAdminConstants::USER_NAME_OPTION) ?>">
    <input name="<? echo SETTINGS_FORM_PASSWORD ?>" type="password" size="25"
           value="<? form_option(RCallerAdminConstants::PASSWORD_OPTION) ?>">

    <input type="submit" name="checkCredentials" value="Check credentials">
    <input type="submit" name="save" value="Save">

</form>

<?
if ($checkCredentialsStatus != null) {
    ?>
    <div>RCaller credentials status: <? echo $checkCredentialsStatus ?></div>
    <?
}
?>
