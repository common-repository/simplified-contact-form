jQuery(document).ready(function ($) {


    var ajaxURL = cpVars.ajaxURL;

    var contactFormSubmit = $('.scf-form-contact input[type=button]');
    if (contactFormSubmit.length) {

        contactFormSubmit.click(function () {

            $('.scf-form-error').remove();

            var contactForm = $(this).parents('form');
            var loadingContainer = contactForm.find('.scf-loading-container');
            var notificationContainer = contactForm.find('.scf-notification-container');
            var acceptanceCheckbox = contactForm.find('.acceptance-checkbox');

            loadingContainer.fadeIn();
            notificationContainer.removeClass('sent failed').hide();;

            contactForm.find('input').each(function () {
                validateField($(this));
            });

            contactForm.find('textarea').each(function () {
                validateField($(this));
            });

            console.log('Errors: ' + $('.scf-form-error').length);
            if ($('.scf-form-error').length == 0) {


                if (acceptanceCheckbox.prop('checked')) {

                    var values = 'action=simplified_contact_form_handler_lead_action&' + contactForm.serialize();

                    $.get(ajaxURL, values, function (response) {
                        
                        $data = $.parseJSON(response);
                        console.log(response);
                        console.log($data);

                        loadingContainer.hide();

                        if ($data.email_status == 1){
                            notificationContainer.html('<p>Thank you! We\'ll get back to you soon. </p>').addClass('sent').fadeIn();
                            contactForm.find("input[type=text], input[type=email], textarea").val("");
                            acceptanceCheckbox.prop('checked', false);
                        }else{
                            notificationContainer.html('<p>Error found.</p>').addClass('failed').fadeIn()
                        }
                        
                    });

                } else {
                    notificationContainer.html('<p>Please accept the terms and privacy policy.</p>').addClass('failed').fadeIn();
                    loadingContainer.hide();
                }



            } else {
                loadingContainer.hide();
                notificationContainer.html('<p>Please fill in all required fields.</p>').addClass('failed').fadeIn();
            }


        });
    }


    /*
     * Helper
     * Version: 1.0.0
     * Updated: 10-17-18
     */
    function validateField(element, e) {

        var restriction = element.attr('data-restriction');
        var errorText = element.attr('data-error-text');
        var required = element.attr('data-required');

        var name = element.attr('name');
        var value = element.val();

        var label = '<span class="error-label">' + element.siblings('label').html() + '</span>';
        var tagName = element.prop('tagName').toLowerCase();

        if (typeof restriction !== 'undefined') {
            if (restriction == 'numeric') {

                if (typeof e !== 'undefined') {

                    // Allow: backspace, delete, tab, escape, enter and .
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                        // Allow: Ctrl+A, Command+A
                        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                        // Allow: home, end, left, right, down, up
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                        // let it happen, don't do anything
                        return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }

                }
            }
        };

        if (required == 'yes') {

            console.log(tagName);

            switch (tagName) {

                case 'input':

                    var type = element.attr('type');

                    switch (type) {

                        case 'text':
                            if (element.val() == '') {
                                element.after('<span class="scf-form-error form-error-' + name + '">Please enter ' + label + '.</span>');
                                element.addClass('invalid');
                                element.removeClass('valid');
                            } else {
                                element.removeClass('invalid');
                                element.addClass('valid');
                                $('.scf-form-error-' + name).remove();
                            }
                            break;


                        case 'password':

                            console.log('Password validation');

                            if (element.val() == '') {
                                element.after('<span class="scf-form-error form-error-' + name + '">Please enter ' + label + '.</span>');
                                element.addClass('invalid');
                                element.removeClass('valid');
                            } else {


                                if (element.hasClass('form-control-re-type-password')) {
                                    if (element.val() != $('.form-control-password').val()) {
                                        element.after('<span class="scf-form-error form-error-' + name + '">Password and Re-type password must match.</span>');
                                        element.addClass('invalid');
                                        element.removeClass('valid');
                                    } else {
                                        element.removeClass('invalid');
                                        element.addClass('valid');
                                        $('.scf-form-error-' + name).remove();

                                    }
                                } else {
                                    element.removeClass('invalid');
                                    element.addClass('valid');
                                    $('.scf-form-error-' + name).remove();
                                }


                            }
                            break;


                        case 'email':

                            if (element.val() == '') {

                                element.after('<span class="scf-form-error form-error-' + name + '">Please enter your correct email address.</span>');
                                element.addClass('invalid');
                                element.removeClass('valid');

                                console.log(element.val() + ' invalid empty');

                            } else if (!validateEmail(element.val())) {

                                element.after('<span class="scf-form-error form-error-' + name + '">Please enter your correct email address.</span>');
                                console.log(element.val() + ' invalid' + name);
                                element.removeClass('valid');
                                element.addClass('invalid fsdfsd');

                            } else {

                                element.removeClass('invalid');
                                element.addClass('valid');
                                $('.scf-form-error-' + name).remove();
                                console.log(element.val() + ' valid');

                            }

                            break;

                        default:

                    }
                    break;


                case 'select':

                    if (element.val() == '') {
                        element.after('<span class="scf-form-error form-error-' + name + '">Please choose ' + label + '.</span>');
                        element.addClass('invalid');
                        element.removeClass('valid');
                    } else {
                        element.removeClass('invalid');
                        element.addClass('valid');
                        $('.scf-form-error-' + name).remove();
                    }
                    break;

                case 'textarea':

                    if (element.val() == '') {
                        element.after('<span class="scf-form-error form-error-' + name + '">Please enter ' + label + '.</span>');
                        element.addClass('invalid');
                        element.removeClass('valid');
                    } else {
                        element.removeClass('invalid');
                        element.addClass('valid');
                        $('.scf-form-error-' + name).remove();
                    }
                    break;


            }
        }
    }

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

});
