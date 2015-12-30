/**
 * Created by andrew on 29.11.15 15:52
 */

$('a[role="tab"]').on('shown.bs.tab',function(){pushHistoryState($(this).data('url'));});

$('#get-change-password').on('click', function() {
    if ($(this).prop('checked')) $('#password-group').removeClass('hide');
    else $('#password-group').addClass('hide');
});

$('#send-change-profile').on('click', function() {
    var button = $(this);
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: button.data('change-profile-link'),
        data: {
            user_name: $('#user-name').val(),
            family_name: $('#family-name').val(),
            get_change_password: $('#get-change-password').prop('checked'),
            user_password: $('#user-password').val()
        },
        beforeSend: function() {
            $('#page-loader').show();
        },
        success: function(resp) {
            $('#page-loader').hide();
            if (resp.result == 'done') {
                showMessage(resp.message, 'success');
                $('#current-user-name').text(resp.name);
            } else {
                showMessage(resp.message, 'error');
            }
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

var newUserModal = $('#addNewUserModal');
newUserModal.find('#send-button').on('click', function() {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: newUserModal.data('add-new-user-link'),
        data: {
            name: $('#new-user-name').val(),
            email: $('#new-user-email').val(),
            password: $('#new-user-password').val()
        },
        beforeSend: function() {
            $('#modal-loader').show();
            $('.modal-header').addClass('blur');
            $('.modal-body').addClass('blur');
            $('.modal-footer').addClass('blur');
        },
        success: function(resp) {
            $('#modal-loader').hide();
            $('.modal-header').removeClass('blur');
            $('.modal-body').removeClass('blur');
            $('.modal-footer').removeClass('blur');
            if (resp.result == 'done') {
                showMessage(resp.message, 'success');
                $('#new-user-name').val('');
                $('#new-user-email').val('');
                $('#new-user-password').val('');
                var i = Number($('#table-users tr:last-child td:first-child').text());
                var button = (role == 'manager' || role == 'admin') ? '<td><button class="btn btn-xs btn-success center-block" data-toggle="modal" data-target="#showUserTasksModal">Задачи участника</button></td>' : '';
                $('#table-users').append('<tr><td>'+(i+1)+'</td><td>'+resp.user[1]+'</td><td>'+resp.user[2]+'</td><td>'+resp.user[4]+'</td><td>0</td>'+button+'</tr>');
                newUserModal.modal('hide');
            } else {
                showMessage(resp.message, 'error');
            }
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

var showUserTasks = $('#showUserTasksModal');
showUserTasks.on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var email = button.parents('tr').find('td:nth-child(3)').text();
    $(this).find('.modal-title').text('Задачи участника ' + button.parents('tr').find('td:nth-child(2)').text());
    $('#addUserTaskModal').attr('user-email', email);
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: showUserTasks.data('show-user-tasks-link'),
        data: {email: email},
        beforeSend: function() {
            $('#modal-loader').show();
        },
        success: function(resp) {
            $('#modal-loader').hide();
            var html = '<table id="table-user-tasks" class="table table-striped table-hover"><tr><th>#</th><th style="width:98%">Задача</th><th>Баллы</th></tr>';
            if (resp.result == 'done') {
                $(resp.tasks).each(function(i, item) {
                    html += '<tr><td>'+(i+1)+'</td><td><button class="btn btn-success btn-xs" data-toggle="modal" data-target="#editUserTaskModal" data-task-id="'+item.id+'">Edit</button>&nbsp;&nbsp;'+item.name+(item.daily == 1 ? '&nbsp;&nbsp;<sup><span class="label label-info">ежедневно</span></sup>' : '')+'</td><td class="text-center">'+item.value+'</td></tr>';
                });
            } else {
                html += '<tr id="no-user-tasks"><td colspan="3" align="center">'+resp.message+'</td></tr>';
            }
            showUserTasks.find('.modal-body').html(html+'</table>');
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

var editUserTask = $('#editUserTaskModal');
editUserTask.on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var task_id = button.data('task-id');
    editUserTask.attr('task_id', task_id);
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: editUserTask.data('edit-user-task-link'),
        data: {task_id: task_id},
        beforeSend: function() {
            //
        },
        success: function(resp) {
            if (resp.result = 'done') {
                editUserTask.find('input[name="task-name"]').val(resp.name);
                editUserTask.find('input[name="task-value"]').val(resp.value);
                editUserTask.find('input[name="daily"]').prop('checked', (resp.daily == 1 ? true : false));
            } else {
                showMessage(resp.message, 'error');
            }
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

editUserTask.find('button[name="save-task"]').on('click', function() {
    var url = $(this).data('save-user-task');
    var task_id = editUserTask.attr('task_id');
    var name = editUserTask.find('input[name="task-name"]').val();
    var value = editUserTask.find('input[name="task-value"]').val();
    var daily = editUserTask.find('input[name="daily"]').prop('checked');

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: url,
        data: {id: task_id, name: name, value: value, daily: daily},
        beforeSend: function() {
            //
        },
        success: function(resp) {
            if (resp.result == 'done') {
                editUserTask.modal('hide');
                var tr = showUserTasks.find('button[data-task-id="' + task_id + '"]').parents('tr');
                var button = '<button class="btn btn-success btn-xs" data-toggle="modal" data-target="#editUserTaskModal" data-task-id="' + task_id + '">Edit</button>&nbsp;&nbsp;';
                tr.find('td:nth-child(2)').html(button + name + ((daily == true) ? '&nbsp;&nbsp;<sup><span class="label label-info">ежедневно</span></sup>' : ''));
                tr.find('td:nth-child(3)').html(value);
                showMessage(resp.message, 'success');
            } else {
                showMessage(resp.message, 'error');
            }
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});

$('#addUserTaskModal #add-task-button').on('click', function() {
    var modal = $('#addUserTaskModal');
    var name = modal.find('#task-name');
    var value = modal.find('#task-value');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: modal.data('add-user-tasks-link'),
        data: {task_name: name.val(), task_value: value.val(), email: modal.attr('user-email')},
        beforeSend: function() {
            modal.find('#modal-loader').show();
            modal.find('.modal-header').addClass('blur');
            modal.find('.modal-body').addClass('blur');
            modal.find('.modal-footer').addClass('blur');
        },
        success: function(resp) {
            modal.modal('hide');
            if (resp.result == 'done') {
                var i = Number($('#table-user-tasks tr:last-child td:first-child').text());
                if (isNaN(i)) {
                    showUserTasks.find('#no-user-tasks').remove();
                    var tr = '<tr><td>1</td><td>'+name.val().ucfirst()+'</td><td align="center">'+value.val()+'</td></tr>';
                } else {
                    var tr = '<tr><td>'+(i+1)+'</td><td>'+name.val().ucfirst()+'</td><td align="center">'+value.val()+'</td></tr>';
                }
                showUserTasks.find('.modal-body table').append(tr);
                name.val('');
                value.val(5);
                showMessage(resp.message, 'success');
            } else {
                showMessage(resp.message, 'error');
            }

            modal.find('#modal-loader').hide();
            modal.find('.modal-header').removeClass('blur');
            modal.find('.modal-body').removeClass('blur');
            modal.find('.modal-footer').removeClass('blur');
        },
        error: function(resp) {
            console.log(resp);
        }
    });
});