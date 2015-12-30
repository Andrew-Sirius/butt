/**
 * Created by andrew on 25.11.15 17:05
 */
$(document).ready(function() {

    getTasks();
    getPoints();

    var modal = $('#pointsAndTasksModal');

    modal.on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        $('#send-button').text(button.data('modal-send-button')).attr('url', modal.data(button.data('url')));
        $('.modal-title').text(button.data('modal-title'));
        $('.modal-description p').text(button.data('modal-description'));
        $('#task-name').attr('placeholder', button.data('modal-input-placeholder'));
    });

    modal.on('shown.bs.modal', function() {
        modal.find('.form-group div:first-child input').focus();
    });

    modal.find('#send-button').on('click', function() {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: $(this).attr('url'),
            data: {task_name: modal.find('#task-name').val(), task_value: modal.find('#task-value').val()},
            beforeSend: function() {
                $('#modal-loader').show();
                $('.modal-header').addClass('blur');
                $('.modal-body').addClass('blur');
                $('.modal-footer').addClass('blur');
            },
            success: function(resp) {
                modal.modal('hide');
                if (resp.result == 'done') {
                    modal.find('#task-name').val('');
                    modal.find('#task-value').val(5);
                    if (resp.type == 'add-task') getTasks();
                    else {
                        getPoints();
                        getMyPoints();
                    }
                    showMessage(resp.message, 'success');
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
});

function getTasks() {
    var caption = '<tr><th style="width:1%;">#</th><th>Задача</th><th style="width:5%;">Баллы</th><th style="width:5%;">Добавить</th></tr>';

    if (role == 'manager' || role == 'admin') {
        var addTaskButton = '<tr><td colspan="4"><button type="button" class="btn btn-block btn-success" data-modal-title="Добавление новой задачи в систему" data-modal-description="Введите название задачи для выполнения и назначьте для неё нужное количество баллов." data-modal-input-placeholder="Название новой задачи" data-modal-send-button="Срочно добавить задачу!" data-url="add-task-link" data-toggle="modal" data-target="#pointsAndTasksModal">Добавить новую задачу</button></td></tr>';
    } else var addTaskButton = '';

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: $('#tasks-table').data('get-tasks-link'),
        beforeSend: function() {
            $('#tasks-table').html('<tr><td class="text-center"><div id="tasks-loader"><div class="loader-inner ball-pulse"><div></div><div></div><div></div></div></div></td></tr>');
        },
        success: function(resp) {
            var html = '';
            if (resp.result == 'done') {
                $(resp.tasks).each(function(i, item) {
                    html += '<tr><td>'+(i+1)+'</td><td>'+item.name+((item.daily == 1) ? '&nbsp;&nbsp;<sup><span class="label label-info">ежедневно</span></sup>' : '')+'</td><td align="center">'+item.value+'</td><td><button type="button" class="btn btn-success btn-xs center-block" data-loading-text="Ждите…" onclick="addPoints(this, '+item.id+');">Добавить</button></td></tr>';
                });
            } else {
                html = '<tr><td colspan="4" class="text-center"><h4>' + resp.message + '</h4></td></tr>';
            }
            $('#tasks-table').html(caption + html + addTaskButton);
        },
        error: function(resp) {
            console.log(resp);
        }
    });
}

function getPoints() {
    var div = $('#points-per-days');
    var days = div.data('days-with-points');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: div.data('get-points-link'),
        beforeSend: function() {
            div.html('<div class="text-center"><div id="tasks-loader"><div class="loader-inner ball-pulse"><div></div><div></div><div></div></div></div></div>');
        },
        success: function(resp) {
            var html = '';
            if (resp.result == 'done') {
                for (i = 0; i < days; i++) {
                    html = fillPointsTable(html,resp.points,i);
                }
            } else {
                showMessage(resp.message, 'error');
            }

            div.html(html);
        },
        error: function(resp) {
            console.log(resp);
        }
    });
}

function addPoints(e, id) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: $('#tasks-table').data('add-points-link'),
        data: {task: id},
        beforeSend: function() {
            $(e).button('loading');
        },
        success: function(resp) {
            if (resp.result == 'done') {
                getPoints();
                getMyPoints();
                showMessage(resp.message, 'success');
            } else {
                showMessage(resp.message, 'error');
            }
            $(e).button('reset');
        },
        error: function(resp) {
            console.log(resp);
        }
    });
}

function fillPointsTable(html,points,i) {

    html += '<div class="panel panel-default">';

    var today = new Date();
    var year = today.getFullYear();
    var month = (today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1);
    var day = today.getDate() < 10 ? '0' + today.getDate() : today.getDate();

    if (i == 0) {
        html += '<div class="panel-heading"><h3>Выполненные задачи за сегодня <small>'+day+'-'+month+'-'+year+'</small></h3></div>';
    } else if (i == 1) {
        var yesterday = new Date(today.valueOf() - 86400000);
        year = yesterday.getFullYear();
        month = (yesterday.getMonth() + 1) < 10 ? '0' + (yesterday.getMonth() + 1) : (yesterday.getMonth() + 1);
        day = yesterday.getDate() < 10 ? '0' + yesterday.getDate() : yesterday.getDate();

        html += '<div class="panel-heading"><h3>Выполненные задачи за вчера <small>'+day+'-'+month+'-'+year+'</small></h3></div>';
    } else {
        var prevDay = new Date(today.valueOf() - (i*86400000));
        year = prevDay.getFullYear();
        month = (prevDay.getMonth() + 1) < 10 ? '0' + (prevDay.getMonth() + 1) : (prevDay.getMonth() + 1);
        day = prevDay.getDate() < 10 ? '0' + prevDay.getDate() : prevDay.getDate();

        html += '<div class="panel-heading"><h3>Выполненные задачи за '+day+'-'+month+'-'+year+'</h3></div>';
    }

    html += '<table class="table table-condensed table-bordered table-hover">';
    html += '<tr><th style="width:2%;">#</th><th>Задание</th><th style="width:5%;">Баллы</th></tr>';

    var each = 0;
    var total = 0;
    var n = 1;

    $(points).each(function(j, item) {
        if (item.date.split(' ')[0] == year+'-'+month+'-'+day) {
            each++;
            total = total + Number(item.value);
            var taskName = item.task_name;
            var rowClass = '';
            if (item.task_name == null) {
                taskName = item.hold_reason;
                rowClass = ' class="danger"';
            }
            html += '<tr'+rowClass+'><td>'+n+'</td><td>'+taskName+' <sup>'+item.date.split(' ')[1]+'</sup></td><td>'+item.value+'</td></tr>';
            n++;
        }
    });

    if (each == 0) html += '<tr><td colspan="3" align="center"><h4>Нет данных для отображения!</h4></td></tr>';
    else html += '<tr><td colspan="2" align="right">Всего</td><td>'+total+'</td></tr>';

    html += '</table></div>';

    return html;
}