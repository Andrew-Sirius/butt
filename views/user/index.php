    <h1 class="page-header">Личный кабинет<?=(Registry::get('user')->role == 'admin')?' <button class="btn btn-success" data-toggle="modal" data-target="#addFamilyManagerModal">Добавить новую семью</button>':''?></h1>

    <div class="row">
        <div class="col-xs-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation"<?=$this->section=='users'?' class="active"':false?>><a href="#users" aria-controls="home" role="tab" data-toggle="tab" data-url="<?=Tools::url('user/index/section/users')?>">Участники системы</a></li>
                <li role="presentation"<?=$this->section=='profile'?' class="active"':false?>><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" data-url="<?=Tools::url('user/index/section/profile')?>">Мой профиль</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane<?=$this->section=='users'?' active':false?>" id="users">
                    <div class="col-xs-12">
                        <p>В данной таблице отображены пользователи системы, которые относятся к Вашей семье.</p>
                        <table id="table-users" class="table table-responsive table-striped table-hover">
                            <tr>
                                <th>#</th>
                                <th>Имя</th>
                                <th>Email/Логин</th>
                                <th>Роль</th>
                                <th>Баллы</th>
                            <? if (Registry::get('is_manager')) { ?>
                                <th class="text-center">Задачи</th>
                            <? } ?>
                            </tr>
                        <? foreach ($this->family as $key => $member) { ?>
                            <tr>
                                <td><?=$key+1?></td>
                                <td><?=$member->name?></td>
                                <td><?=$member->email?></td>
                                <td><?=$member->role?></td>
                                <td><?=empty($member->points) ? 0 : $member->points?></td>
                            <? if (Registry::get('is_manager')) { ?>
                                <td><button class="btn btn-xs btn-success center-block" data-toggle="modal" data-target="#showUserTasksModal">Задачи участника</button></td>
                            <? } ?>
                            </tr>
                        <? } ?>
                        </table>
                    <? if (Registry::get('is_manager')) { ?>
                        <button class="btn btn-lg btn-success center-block" data-toggle="modal" data-target="#addNewUserModal">Добавить участника</button>
                    <? } ?>
                    </div>
                    &nbsp;<br>
                </div>
                <div role="tabpanel" class="tab-pane<?=$this->section=='profile'?' active':false?>" id="profile">
                    <div class="col-xs-12">
                        <p>Здесь можно изменить своё имя, которое отображается в системе и пароль</p>
                        <form class="form-horizontal col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label for="family-name" class="col-sm-4 control-label">Семья</label>
                            <div class="col-sm-8">
                            <? if (Registry::get('is_manager')) { ?>
                                <input id="family-name" type="text" class="form-control" value="<?=$this->user->family_name?>">
                            <? } else { ?>
                                <p class="form-control"><?=$this->user->family_name?></p>
                            <? } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user-name" class="col-sm-4 control-label">Имя в системе</label>
                            <div class="col-sm-8">
                                <input id="user-name" type="text" class="form-control" value="<?=$this->user->name?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label for="get-change-password">
                                        <input id="get-change-password" type="checkbox"> Изменить пароль
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="password-group" class="form-group hide">
                            <p class="col-sm-offset-4 col-sm-8">Длина пароля должна быть в пределах от <?=Registry::get('min_password_length')?> до <?=Registry::get('max_password_length')?> символов.</p>
                            <label for="user-password" class="col-sm-4 control-label">Новый пароль</label>
                            <div class="col-sm-8">
                                <input id="user-password" type="password" class="form-control" placeholder="Введите новый пароль">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button id="send-change-profile" type="button" class="btn btn-success" data-change-profile-link="<?=Tools::url('user/changeProfile')?>">Отправить</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- addNewUserModal -->
    <div class="modal fade" id="addNewUserModal" tabindex="-1" role="form" data-add-new-user-link="<?=Tools::url('user/add')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header">
                    <h3 class="modal-title">Добавить нового участника в систему</h3>
                </div>
                <div class="modal-body">
                    <div class="modal-description">
                        <p>Введите имя, Email или логин и пароль для создания нового участника в системе.</p>
                        <p>Вводите Email в качестве логина в том случае, если новый пользователь будет пользоваться данным Email'ом для получения корреспонденции.</p>
                        <p>Длина пароля должна быть в пределах от <?=Registry::get('min_password_length')?> до <?=Registry::get('max_password_length')?> символов.</p>
                    </div>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="new-user-name" type="text" class="col-xs-12 form-control" placeholder="Имя нового участника">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <input id="new-user-email" class="form-control" type="text" placeholder="Email или логин нового участника">
                            </div>
                            <div class="col-xs-6">
                                <input id="new-user-password" class="form-control" type="password" placeholder="Пароль нового участника">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-group" id="modal-buttons">
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-success btn-lg" id="send-button">Срочно добавить участника!</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- addNewUserModal -->

    <!-- showUserTasksModal -->
    <div class="modal fade" id="showUserTasksModal" tabindex="-1" role="form" data-show-user-tasks-link="<?=Tools::url('tasks/get')?>">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header"><h3 class="modal-title">Задачи участника …</h3></div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <div class="form-group" id="modal-buttons">
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addUserTaskModal">Добавить задачу</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- showUserTasksModal -->

    <!-- editUserTaskModal -->
    <div class="modal fade" id="editUserTaskModal" tabindex="-1" role="form" data-edit-user-task-link="<?=Tools::url('tasks/getTaskById')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header"><h3 class="modal-title">Редактирование задачи</h3></div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-10">
                                <input name="task-name" class="form-control" type="text">
                            </div>
                            <div class="col-xs-2">
                                <input name="task-value" class="form-control" type="number" min="<?=Registry::get('min_task_value')?>" max="<?=Registry::get('max_task_value')?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="daily"> Ежедневная задача
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-group" id="modal-buttons">
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-success btn-lg" name="save-task" data-save-user-task="<?=Tools::url('tasks/edit')?>">Сохранить задачу</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- editUserTaskModal -->

    <!--  addUserTaskModal -->
    <div class="modal fade" id="addUserTaskModal" tabindex="-1" role="form" data-add-user-tasks-link="<?=Tools::url('tasks/add')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header">
                    <h3 class="modal-title">Добавить задачу</h3>
                </div>
                <div class="modal-body">
                    <div class="modal-description">
                        <p>Напишите название задачи и установите ценность её в баллах.</p>
                        <p>Минимальное количество баллов - <?=Registry::get('min_task_value')?>, максимальное - <?=Registry::get('max_task_value')?></p>
                    </div>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-10">
                                <input id="task-name" class="form-control" type="text" placeholder="Название задачи">
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
                            <button type="button" class="btn btn-success btn-lg" id="add-task-button">Добавить задачу</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- addUserTaskModal -->
<? if (Registry::get('user')->role == 'admin') { ?>
    <!--  addFamilyManagerModal -->
    <div class="modal fade" id="addFamilyManagerModal" tabindex="-1" role="form" data-add-family-manager-link="<?=Tools::url('tasks/add')?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="modal-loader"><div class="loader-inner ball-scale-ripple"><div></div></div></div>
                <div class="modal-header">
                    <h3 class="modal-title">Добавить новую семью с менеджером в систему</h3>
                </div>
                <div class="modal-body">
                    <div class="modal-description">
                        <p>Введите имя, email, пароль менеджера и название семьи</p>
                    </div>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-6">
                                <input id="family-manager-name" class="form-control" type="text" placeholder="Имя менеджера">
                            </div>
                            <div class="col-xs-6">
                                <input id="family-manager-email" class="form-control" type="text" placeholder="Email менеджера">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <input id="family-manager-password" class="form-control" type="text" placeholder="Пароль менеджера">
                            </div>
                            <div class="col-xs-6">
                                <input id="new-family-name" class="form-control" type="text" placeholder="Название семьи">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-group" id="modal-buttons">
                        <div class="text-center">
                            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-success btn-lg" id="add-family-manager-button">Добавить менеджера и семью</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- addFamilyManagerModal -->

    <script>
        $('#addFamilyManagerModal #add-family-manager-button').on('click', function() {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '<?=Tools::url('user/addFamily')?>',
                data: {
                    name: $('#family-manager-name').val(),
                    email: $('#family-manager-email').val(),
                    password: $('#family-manager-password').val(),
                    family: $('#new-family-name').val()
                },
                beforeSend: function() {
                    $('#addFamilyManagerModal #modal-loader').show();
                },
                success: function(resp) {
                    $('#addFamilyManagerModal #modal-loader').hide();
                    if (resp.result == 'done') {
                        showMessage(resp.message, 'success');
                    } else {
                        showMessage(resp.message, 'error');
                    }
                    $('#addFamilyManagerModal').modal('hide');
                },
                error: function(resp) {
                    console.log(resp);
                }
            });
        });
    </script>

<? } ?>
    <script src="<?=Tools::url('js/user.js')?>"></script>