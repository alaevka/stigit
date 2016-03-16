<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\web\View;
use yii\helpers\Url;
$transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
<!-- Static navbar -->
<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?= Url::to(['site/index']); ?>"><img src="/images/logo_fullsize.png" height="40"></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<?php
					$permissions_for_change_permissions = \app\models\Permissions::find()->where('SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action', ['action' => \app\controllers\PermissionsController::_getPermissionsArray('permission_management'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2])->one();
					if($permissions_for_change_permissions) {
				?>
				<li style="padding-top: 12px;" <?php if(\Yii::$app->controller->id == 'permissions' && Yii::$app->controller->action->id == 'index') { ?>class="active"<?php } ?>><a id="permissions-link" href="<?= Url::to(['permissions/index']); ?>">Права доступа</a></li>
				<?php } ?>
				<?php
					$permissions_for_states_change = \app\models\Permissions::find()->where('SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action', ['action' => \app\controllers\PermissionsController::_getPermissionsArray('states_sequence'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2])->one();
					if($permissions_for_states_change) {
				?>
				<li style="padding-top: 12px;" <?php if(\Yii::$app->controller->id == 'permissions' && Yii::$app->controller->action->id == 'states') { ?>class="active"<?php } ?>><a id="states-change-link" href="<?= Url::to(['permissions/states']); ?>">Смена состояний</a></li>
				<?php } ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="user-info">
					Вы авторизованны как: <b><?= \Yii::$app->user->identity->LOGIN; ?></b> (номер транзакции: <?= $transactions->ID;  ?>)<br>
					<?php
					    echo \Yii::$app->session->get('user.user_dolg_podr_data_block');
					?>
				</li>
				<li style="margin-top: 10px;"><a data-method="post" href="<?= Url::to(['site/logout']); ?>"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Выйти</a></li>
			</ul>
		</div><!--/.nav-collapse -->
	</div>

</nav>
<div id="issue-view-preloader" style="z-index: 999;width: 90px; height: 90px; position: fixed; left: 45%; top: 30%; display: none;"><img src="/images/preloader.gif" /></div>
<div id="wrapper" style="padding-top: 53px;">

	<!-- Sidebar -->
	<div id="sidebar-wrapper">
		<?php if(\Yii::$app->controller->id != 'permissions') { ?>
		<ul class="sidebar-nav">
			<?php
				$permissions_task_create = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
					(SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'action' => \app\controllers\PermissionsController::_getPermissionsArray('task_create'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
				if($permissions_task_create) {
			?>
			<li>
				<button type="button" data-backdrop="static" class="btn btn-primary btn-block" data-toggle="modal" data-target="#issue-modal">
				  	Выдать задание
				</button>
			</li>
			<hr>
			<?php } ?>
			<li class="submenu-li"><a href="<?= Url::to(['site/index']); ?>">Все задания</a> <?php if(!isset(Yii::$app->request->getQueryParams()['own_issues']) && !isset(Yii::$app->request->getQueryParams()['podr_issues']) && !isset(Yii::$app->request->getQueryParams()['tasks_my']) && !isset(Yii::$app->request->getQueryParams()['overdue'])) { ?><i class="pull-right glyphicon glyphicon-ok"></i><?php } ?></li>
			<li class="submenu-li"><a href="<?= Url::to(['/site/index', 'own_issues' => 1]); ?>">Задания мне</a> <?php if(isset(Yii::$app->request->getQueryParams()['own_issues']) && Yii::$app->request->getQueryParams()['own_issues'] == 1) { ?><i class="pull-right glyphicon glyphicon-ok"></i><?php } ?></li>
			<?php
				$permissions_podr_tasks_my = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
					(SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['action' => \app\controllers\PermissionsController::_getPermissionsArray('podr_tasks_my'), 'subject_type' => 2, 'subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
				if($permissions_podr_tasks_my) {
					if($permissions_podr_tasks_my->PERM_LEVEL == 1 || $permissions_podr_tasks_my->PERM_LEVEL == 2) {
			?>
			<li class="submenu-li"><a href="<?= Url::to(['/site/index', 'podr_issues' => 1]); ?>">Задания моему подразделению</a> <?php if(isset(Yii::$app->request->getQueryParams()['podr_issues']) && Yii::$app->request->getQueryParams()['podr_issues'] == 1) { ?><i class="pull-right glyphicon glyphicon-ok"></i><?php } ?></li>
			<?php
					}
				}
			?>
			<?php
				$permissions_podr_tasks_my = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
					(SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['action' => \app\controllers\PermissionsController::_getPermissionsArray('autor_tasks_my'), 'subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
				if($permissions_podr_tasks_my) {
					if($permissions_podr_tasks_my->PERM_LEVEL == 1 || $permissions_podr_tasks_my->PERM_LEVEL == 2) {
			?>
			<li class="submenu-li"><a href="<?= Url::to(['/site/index', 'tasks_my' => 1]); ?>">Выданные мной</a> <?php if(isset(Yii::$app->request->getQueryParams()['tasks_my']) && Yii::$app->request->getQueryParams()['tasks_my'] == 1) { ?><i class="pull-right glyphicon glyphicon-ok"></i><?php } ?></li>
			<?php
					}
				}
			?>
			<?php
				//check if isset overdue issues

				$tasks = \app\models\Tasks::find()->all();
	            $list = [];
	            foreach($tasks as $task) {
	                $id = $task->ID;
	                $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
	                if($persons) {
	                    $states_array = [];
	                   
	                    foreach($persons as $person) {
	                        
	                        $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
	                       
	                        $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
	                        if($task_state) {
	                            $states_array[] = $task_state->STATE_ID;
	                        } else {
	                            $list[] = $id;
	                        }
	                    }
	                    if(!empty($states_array)) {
	                        $min_state = min($states_array);
	                        $state = \app\models\States::findOne($min_state);
	                    
	                    }
	                }
	                if(isset($state)) {

	                    if($state->ID != 7 || $state->ID != 9) {
	                        $list[] = $id;
	                    }
	                }

	            }

	            $list = array_unique($list);
	            $query = \app\models\Tasks::find();
		        
	            $dateFormat = 'YYYY-MM-DD hh24:mi:ss';
	            $query->andFilterWhere(['TASKS.ID' => $list]);
	            $now = date("Y-m-d");
	            $query->andFilterWhere(['<', 'TASKS.DEADLINE', new \yii\db\Expression("to_date('" . $now . "','{$dateFormat}')")]);
	            $query->joinWith('perstasks'); 
	            $query->andFilterWhere(['PERS_TASKS.TN' => \Yii::$app->user->id]);
	            
	            $counter_overdue = $query->count();
	            if($counter_overdue > 0) {

			?>
			<li class="submenu-li"><a style="color: #ff0000;" href="<?= Url::to(['/site/index', 'overdue' => 1]); ?>">Просроченные задания (<?= $counter_overdue; ?>)</a> <?php if(isset(Yii::$app->request->getQueryParams()['overdue']) && Yii::$app->request->getQueryParams()['overdue'] == 1) { ?><i class="pull-right glyphicon glyphicon-ok"></i><?php } ?></li>
			<?php } ?>
			<hr>
			<li class="submenu-li">
				<?php
					$states = \app\models\States::find()->all();
					foreach($states as $state) {
				?>
				<div style="padding-bottom: 3px;"><?= $state->getState_name_state_colour(); ?></div>
				<?php } ?>
			</li>
		</ul>
		<?php } ?>
	</div>
	<!-- /#sidebar-wrapper -->
    <?= $content ?>
</div>
<!-- /#wrapper -->
<?php $this->endBody() ?>
<?php if (Yii::$app->getSession()->hasFlash('flash_message_success')): ?>
	<?=
		$this->registerJs(
			"
				$.jGrowl('".Yii::$app->getSession()->getFlash('flash_message_success')."', {themeState: 'success-jg'});
			", 
			View::POS_END, 
			'flash_message'
		);
	?>
<?php endif; ?>
<?php if (Yii::$app->getSession()->hasFlash('flash_message_error')): ?>
	<?=
		$this->registerJs(
			"
				$.jGrowl('".Yii::$app->getSession()->getFlash('flash_message_error')."', {themeState: 'error-jg'});
			", 
			View::POS_END, 
			'flash_message'
		);
	?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
