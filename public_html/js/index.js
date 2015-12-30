/**
 * Created by andrew on 11.11.2015 10.32
 */
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

$('#remind-link').on('click', function () {
    $('#loginModal .modal-title').text('Забыли пароль?');
    $('#modal-description').html('<p>Введите в поле свою электронную почту, которая зарегистрирована на сайте.<br>' +
        'На этот почтовый ящик будет отправлено письмо с инструкцией по восстановлению пароля.</p>');
    $('#email').attr('data-type','remind');
    $('#send-button').text('Отправить инструкцию!').attr('data-mode', 'remind').prop('disabled', true);
    $('#remember-me').parents('.form-group').addClass('hide');
    $('#password').parents('.form-group').addClass('hide');
    $('#login-link').removeClass('hide');
    $('#registration-link').removeClass('hide');
    $('#remind-link').addClass('hide');
    $('#check-email').slideUp(150);
});

$('#registration-link').on('click', function () {
    $('#loginModal .modal-title').text('Регистрация в системе');
    $('#modal-description').html('<p>Для регистрации в системе необходимо верно и внимательно ввести свои данные.<br>' +
        'После отправки данных на сервер, Вам на почту придёт письмо с ссылкой для подтверждения почтового ящика.<br>' +
        'Не забудьте подтвердить свой почтовый ящик, так как иначе Вы не сможете в полной мере воспользоваться услугами сайта.</p>');
    $('#email').attr('data-type','registration');
    $('#send-button').text('Немедленно зарегистрировать!').attr('data-mode', 'registration').prop('disabled', true);
    $('#remember-me').parents('.form-group').addClass('hide');
    $('#password').parents('.form-group').removeClass('hide');
    $('#login-link').removeClass('hide');
    $('#registration-link').addClass('hide');
    $('#remind-link').removeClass('hide');
    $('#check-email').slideUp(150);
});

$('#login-link').on('click', function () {
    $('#loginModal .modal-title').text('Вход в систему');
    $('#modal-description').html('<p>Введите свою электронную почту в качестве логина и пароль.</p>');
    $('#email').attr('data-type','login');
    $('#send-button').text('Пропустите! Я вхожу!').attr('data-mode', 'login').prop('disabled', false);
    $('#remember-me').parents('.form-group').removeClass('hide');
    $('#password').parents('.form-group').removeClass('hide');
    $('#login-link').addClass('hide');
    $('#registration-link').removeClass('hide');
    $('#remind-link').removeClass('hide');
    $('#check-email').slideUp(150);
});

$('#show-password').on('click', function () {
    if ($('#password').attr('type') == 'password') {
        $('#password').attr('type', 'text');
        $(this).text('◎');
    } else {
        $('#password').attr('type', 'password');
        $(this).text('◉');
    }
});

$('#message-box').on('click', '.message', function() {
    $(this).remove();
});

$('#send-button').on('click', function() {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: $('#'+$(this).attr('data-mode')+'-link').data('url'),
        data: {email: $('#email').val(), password: $('#password').val(), remember: $('#remember-me').prop('checked')},
        beforeSend: function() {
            $('#modal-loader').show();
            $('.modal-header').addClass('blur');
            $('.modal-body').addClass('blur');
            $('.modal-footer').addClass('blur');
        },
        success: function(resp) {
            //console.log(resp.result);
            if (resp.result == 'done') {
                $('#loginModal').modal('hide');
                showMessage(resp.message,'success');
                setTimeout(function(){
                    if (session_from_url != '') {
                        window.location.href = session_from_url;
                    } else {
                        window.location.href = '';
                    }
                }, 2000);
            } else {
                showMessage(resp.message, 'error');
            }

            $('#modal-loader').hide();
            $('.modal-header').removeClass('blur');
            $('.modal-body').removeClass('blur');
            $('.modal-footer').removeClass('blur');
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

$(document).on('blur', '[data-type="registration"]', function() {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: $('#registration-link').data('url'),
        data: {mode: 'check-email', email: $('#email').val()},
        beforeSend: function() {
            $('#check-email').text('Идёт проверка Email’a на совпадения…').removeClass('text-success').removeClass('text-danger').addClass('text-info').slideDown(250);
        },
        success: function(resp) {
            if (resp.result == 'done') {
                $('#check-email').text(resp.message).removeClass('text-info').removeClass('text-danger').addClass('text-success');
                $('#send-button').prop('disabled', false);
            } else {
                $('#check-email').text(resp.message).removeClass('text-info').removeClass('text-success').addClass('text-danger');
                $('#send-button').prop('disabled', true);
            }
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});