    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-header">Работа с баллами</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3>Бонусная таблица</h3>
                </div>
                <div class="panel-body">
                    <p>Чтобы добавить выполненную задачу на свой счёт, небходимо нажать на кнопочку <span class="label label-success">Добавить</span> напротив задачи.</p>
                    <p>Для отнимания баллов нужно воспользоваться кнопкой <span class="label label-danger">Удержать баллы</span></p>
                </div>
                <table id="tasks-table" class="table table-condensed table-bordered table-hover" data-get-tasks-link="<?=Tools::url('tasks/get')?>" data-add-points-link="<?=Tools::url('points/add')?>">
                    <tr><td class="text-center"><div id="tasks-loader"><div class="loader-inner ball-pulse"><div></div><div></div><div></div></div></div></td></tr>
                </table>
                <div class="panel-footer">
                    <button type="button" class="btn btn-danger btn-block" data-modal-title="Удержание баллов" data-modal-description="Введите причину и количество для удержания баллов." data-modal-input-placeholder="Причина удержания баллов" data-modal-send-button="Снять баллы!" data-url="hold-points-link" data-toggle="modal" data-target="#pointsAndTasksModal">Удержать баллы</button>
                </div>
            </div>
        </div>

        <div id="points-per-days" class="col-md-6" data-days-with-points="<?=Registry::get('days_with_points')?>" data-get-points-link="<?=Tools::url('points/get')?>">
            <div class="text-center"><div id="tasks-loader"><div class="loader-inner ball-pulse"><div></div><div></div><div></div></div></div></div>
        </div>
    </div>

    <!-- pointsAndTasksModal -->
    <div class="modal fade" id="pointsAndTasksModal" tabindex="-1" role="form" data-add-task-link="<?=Tools::url('tasks/add')?>" data-hold-points-link="<?=Tools::url('points/hold')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                </div>
                <div class="modal-body">
                    <div class="modal-description"><p></p></div>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-10">
                                <input id="task-name" class="form-control" type="text">
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
                            <button type="button" class="btn btn-success btn-lg" id="send-button"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- pointsAndTasksModal -->

    <script src="<?=Tools::url('js/points.js')?>"></script>