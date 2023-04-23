function processRestoreSettings() {
    setRestoreHiddenInput();

    removeCodeGetParam();
}

function setRestoreHiddenInput() {
    var restoreInput =
        document.getElementsByName(
            'plugin_wc_import_google_sheet_options[settings_auth_restore]'
        );

    if (restoreInput[0]) {
        restoreInput = restoreInput[0];
        restoreInput.setAttribute('value', 'true');
    }
}

// we need do remove code param from redirect link
// it help the user to see empty input field for code after page update
function removeCodeGetParam() {
    if (typeof URLSearchParams !== 'function') {
        return;
    }

    var params = new URLSearchParams(window.location.search);
    var code = params.get('code');

    var redirectElement = document.getElementsByName('_wp_http_referer');

    if (redirectElement[0] && redirectElement[0].value && code) {
        var redirectLink = redirectElement[0].value;

        if (redirectLink.indexOf('code=' + encodeURIComponent(code)) !== -1) {
            redirectElement[0].value =
                redirectElement[0].value.replace(
                    'code=' + encodeURIComponent(code), ''
                );
        }
    }
}
