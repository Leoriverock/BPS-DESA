jQuery(document).ready(function() {
    var validationMessage = jQuery('#validationMessage');
    var forgotPasswordDiv = jQuery('#forgotPasswordDiv');
    var loginFormDiv = jQuery('#loginFormDiv');
    loginFormDiv.find('#password').focus();
    loginFormDiv.find('a').click(function() {
        loginFormDiv.toggleClass('hide');
        forgotPasswordDiv.toggleClass('hide');
        validationMessage.addClass('hide');
    });
    forgotPasswordDiv.find('a').click(function() {
        loginFormDiv.toggleClass('hide');
        forgotPasswordDiv.toggleClass('hide');
        validationMessage.addClass('hide');
    });
    loginFormDiv.find('button').on('click', function() {
        var username = loginFormDiv.find('#username').val();
        var password = jQuery('#password').val();
        var result = true;
        var errorMessage = '';
        if (username === '') {
            errorMessage = 'Please enter valid username';
            result = false;
        } else if (password === '') {
            errorMessage = 'Please enter valid password';
            result = false;
        }
        if (errorMessage) {
            validationMessage.removeClass('hide').text(errorMessage);
        }
        return result;
    });
    forgotPasswordDiv.find('button').on('click', function() {
        var username = jQuery('#forgotPasswordDiv #fusername').val();
        var email = jQuery('#email').val();
        var email1 = email.replace(/^\s+/, '').replace(/\s+$/, '');
        var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/;
        var illegalChars = /[\(\)\<\>\,\;\:\\\"\[\]]/;
        var result = true;
        var errorMessage = '';
        if (username === '') {
            errorMessage = 'Please enter valid username';
            result = false;
        } else if (!emailFilter.test(email1) || email == '') {
            errorMessage = 'Please enter valid email address';
            result = false;
        } else if (email.match(illegalChars)) {
            errorMessage = 'The email address contains illegal characters.';
            result = false;
        }
        if (errorMessage) {
            validationMessage.removeClass('hide').text(errorMessage);
        }
        return result;
    });
    jQuery('input').blur(function(e) {
        var currentElement = jQuery(e.currentTarget);
        if (currentElement.val()) {
            currentElement.addClass('used');
        } else {
            currentElement.removeClass('used');
        }
    });
    loginFormDiv.find('#username').focus();
});