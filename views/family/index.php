    <h1 class="page-header">Моя семья<br><small>подробный просмотр баллов участников</small></h1>
<? if ($this->family) {
    foreach ($this->family as $key => $user) { ?>
        <div class="row">
            <div class="col-xs-12">
                <h3><?=$user['name']?> <small>набрано баллов: <?=$user['total_points'] ? $user['total_points'] : '0'?></small></h3>
                <div class="col-md-6">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3>Бонусная таблица</h3>
                        </div>
                    <? if ($this->familyPoints) { $k=1;?>
                        <table id="tasks-table" class="table table-condensed table-bordered table-hover">
                            <tr>
                                <th>#</th><th style="width:98%">Задача</th><th>Баллы</th>
                            </tr>
                        <? foreach ($user['tasks'] as $task) {?>
                            <tr>
                                <td><?=$k++?></td><td><?=$task['name']?><?=$task['daily'] ? '&nbsp;&nbsp;<sup><span class="label label-info">ежедневно</span></sup>' : ''?></td><td><?=$task['value']?></td>
                            </tr>
                        <? } ?>
                        </table>
                        <div class="panel-footer">
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-user-id="<?=$key?>" data-target="#holdPointsModal">Удержать баллы</button>
                        </div>
                    <? } else { ?>
                        <div class="panel-footer">
                            <h4 class="text-center text-muted">У данного пользователя пока нет заданий</h4>
                        </div>
                    <? } ?>
                    </div>
                </div>

                <div class="col-md-6">
                <? for ($i=0; $i<Registry::get('days_with_points'); $i++) {
                    if ($i == 0) $day = 'сегодня <small>' . date('d-m-Y', strtotime('-' . $i . ' days')) . '</small>';
                    else if ($i == 1) $day = 'вчера <small>' . date('d-m-Y', strtotime('-' . $i . ' days')) . '</small>';
                    else $day = date('d-m-Y', strtotime('-' . $i . ' days'));?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Выполненные задачи за <?=$day?></h3>
                        </div>
                    <? if ($this->familyPoints) { ?>
                        <table class="table table-condensed table-bordered table-hover">
                            <tr><th>#</th><th style="width:98%">Задание</th><th>Баллы</th></tr>
                        <? $j=1; $total = 0;
                        foreach ($this->familyPoints as $points) {
                        if ($points->id == $key && explode(' ', $points->date)[0] == date('Y-m-d', strtotime('-' . $i . ' days'))) { ?>
                            <tr<?=$points->hold_reason ? ' class="danger"' : ''?>><td><?=$j++?></td><td><?=$points->task_name ? $points->task_name : $points->hold_reason?> <sup><?=explode(' ', $points->date)[1]?></sup></td><td><?=$points->value?></td></tr>
                        <? $total += $points->value;
                            }
                        }
                        if ($j>1) { ?>
                            <tr><td colspan="2" align="right">Всего</td><td><?=$total?></td></tr>
                        <? } else { ?>
                            <tr><td colspan="3" align="center"><h4>Нет данных для отображения!</h4></td></tr>
                        <? } ?>
                        </table>
                    <? } else {}?>
                    </div>
                <? } ?>
                </div>
            </div>
        </div>
    <? } ?>
    <!-- holdPointsModal -->
    <div class="modal fade" id="holdPointsModal" tabindex="-1" role="form" data-hold-points-link="<?=Tools::url('points/hold')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header">
                    <h3 class="modal-title">Удержание баллов</h3>
                </div>
                <div class="modal-body">
                    <div class="modal-description"><p>Введите причину и количество для удержания баллов.</p></div>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-10">
                                <input id="task-name" class="form-control" type="text" placeholder="Причина удержания баллов">
                            </div>
                            <div class="col-xs-2">
                                <input id="task-value" class="form-control" type="number" min="<?=Registry::get('min_task_value')?>" max="<?=Registry::get('max_task_value')?>" value="5">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-group" id="modal-buttons">
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-success btn-lg" id="send-button">Снять баллы!</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- holdPointsModal -->

    <script>
        var modal = $('#holdPointsModal');
        modal.on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            modal.attr('user-id', button.data('user-id'));
        });

        $('#send-button').on('click', function() {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: modal.data('hold-points-link'),
                data: {
                    user_id: modal.attr('user-id'),
                    task_name: $('#task-name').val(),
                    task_value: $('#task-value').val()
                },
                beforeSend: function() {
                    $('#modal-loader').show();
                },
                success: function(resp) {
                    $('#modal-loader').hide();
                    if (resp.result == 'done') {
                        showMessage(resp.message, 'success');
                        modal.modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showMessage(resp.message, 'error');
                    }
                },
                error: function(resp) {
                    console.log(resp);
                }
            });
        });
    </script>
<? } else { ?>
    <div class="row">
        <div class="col-xs-12">
            <p class="lead text-center">В семье ещё нет участников, кроме Вас!<br><br>
            <a class="btn btn-default btn-lg" href="<?=Tools::url('user/index/section/users')?>">Добавить участников</a></p>
        </div>
    </div>
<? } ?>