<?php
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\web\JsExpression;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
$this->title = 'Перечень заданий';
$transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
?>


	<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">
			<!-- <div style="position: fixed; top: 50px; right: 0%; background-color: #eee; padding: 10px; z-index: 999;"></div> -->
			<div class="row">
				<div class="col-md-12" id="items-panel-col">
					<?php
						$summary = '
							<ul class="nav nav-pills pull-right">
							  	<li role="presentation"><a href="#">Всего заданий {totalCount}</a></li>
							  	<li role="presentation" class="selected_issues_link dropdown" style="display: none;">
							  		<a id="selected_issues_link_dropdown" class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
								      	
								    </a>
								    <ul class="dropdown-menu" style="width: 250px;">
								      	<li>
								      		<div style="padding-left: 10px; padding-top: 10px;" id="state-dropdown-block">
								      			<div style="text-align:center;"><b>Изменить статус</b></div>
								      			<hr>
								      			'.Html::radioList("changed_status", null, yii\helpers\ArrayHelper::map(\app\models\States::find()->orderBy('ID asc')->all(), 'ID', 'STATE_NAME'), ['separator' => '', 'class' => 'state-checkbox-selected']).'
								      			<hr>
								      			<div style="text-align:center;"><button id="change_state_for_selected_rows" class="btn btn-sm btn-primary">Изменить</button></div>
								      		</div>
								      	</li>
								    </ul>
							  	</li>
							  	<li>
							  	<a id="show_filter_panel" href="#">Показать фильтры</a>
							  	</li>
							</ul>
						';

						echo GridView::widget([
						    'dataProvider' => $dataProvider,
						    //'filterModel' => $searchModel,
						    'layout' => '<div class="row"><div class="col-md-4">{pager}</div><div class="col-md-8">{summary}</div></div><div>{items}</div>',
						    'summary' => $summary,//'<div class="pull-right selected_issues_link"></div><div class="summary pull-right">Всего заданий {totalCount}</div>',
						    'hover'=>true,
						    'headerRowOptions' => ['class' => 'grid-header-row'],
						    'rowOptions' => function ($model, $key, $index, $grid) {
					            return ['id' => $model['ID'], 'class' => 'issue-row'];
					        },
					        'pjax'=>true, 
						    'columns' => [
						    	[
							        'class' => '\kartik\grid\CheckboxColumn',
							        'rowSelectedClass' => GridView::TYPE_INFO,
							    ],
							    [
							    	'attribute' => 'STATUS',
							    	'label' => '',
							    	'format' => 'html',
							    	'value' => function ($model, $key, $index, $widget) {
							    		//return $model->_getLastTaskStatus($model->ID);
							    		return $model->_getCurrentTaskStatus($model->ID);
							    	}
							    ],
							    [
							    	'attribute' => 'persons_list',
							    	'label' => 'Исполнитель',
							    	'format' => 'html',
							    	'value' => function ($model, $key, $index, $widget) {
							    		return $model->_getStatusPerson();
							    	},
							    	'contentOptions' => ['style' => 'width: 250px;']
							    ],
							    [
							    	'label' => 'Основание',
							    	'format' => 'html',
							    	'value' => function ($model, $key, $index, $widget) {
							    		return '
							    			Заказ: <b>'.$model->ORDERNUM.'</b><br>
							    			Заказ ПЭО: <b>'.$model->PEOORDERNUM.'</b><br>
							    			Входящий: <b>'.$model->SOURCENUM.'</b><br>
							    			Исходящий: <b>'.$model->TASK_NUMBER.'</b>
							    		';
							    	},
							    	'contentOptions' => ['style' => 'width: 270px;']
							    ],
						        [
						        	'attribute' => 'TASK_TEXT',
						        	'enableSorting' => false
						        ],
						        [

						        	'value' => function ($model, $key, $index, $widget) {
						        		$old_task_date_2 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 2])->one();
							            if(!$old_task_date_2) {
							                $transactions_for_date = \app\models\Transactions::findOne($model->TRACT_ID);
							                $group_date_for_table = \Yii::$app->formatter->asDate($transactions_for_date->TRACT_DATETIME, 'php:d-m-Y');
							            } else {
							                $group_date_for_table = \Yii::$app->formatter->asDate($old_task_date_2->TASK_TYPE_DATE, 'php:d-m-Y');
							            }
							            $transactions = \app\models\Transactions::findOne($model->TRACT_ID);
							            $query = new \yii\db\Query;
						                $query->select('FAM')
						                    ->from('STIGIT.V_F_PERS')
						                    ->where('TN = \'' . $transactions->TN .'\'');
						                $command = $query->createCommand();
						                $data = $command->queryOne();

							            return $group_date_for_table.'<br><b>'.$data['FAM'].'</b><br>'.\Yii::$app->formatter->asDate($model->DEADLINE, 'php:d-m-Y');
							            
						        	},
						        	'label' => 'Выдано Срок',
						        	'format' => 'html',
						        	'contentOptions' => function ($model, $key, $index, $column) {

						        		$status = $model->_getCurrentTaskStatusWithId($model->ID);

						        		if(($status != 7 || $status != 9) && (time() > \Yii::$app->formatter->asTimestamp($model->DEADLINE))) {
						        			return ['style' => 'width: 110px; text-align: center; background-color: #f9dfe0;'];
						        		} else {
						        			return ['style' => 'width: 110px; text-align: center;'];
						        		}
						        		
						        	}
						        ]
						    ],
						]);
					?>
				</div>
				<div class="col-md-0" id="filter-panel-col">
					<div class="row">
						
						<div class="panel-group fixed panel-fixed-default" style="display: none; height: 500px; overflow: auto;" id="accordion" role="tablist" aria-multiselectable="true">
							<div class="filters-header fixed" style="z-index: 99;">Фильтры<a class="pull-right filter-form-submit" href="#"><span style="font-size: 16px;" class="glyphicon glyphicon-filter"></span></a></div>
							
							<?php $form_filter = ActiveForm::begin([
					                'id' => 'filter-form',
					                'method' => 'get',
					                'action' => ['index'],
					                'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
					        ]); ?>
							<div class="panel panel-default" style="margin-top: 30px;">
								<div class="panel-heading" role="tab" id="headingOne">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
											Состояние
											<div class="what-selected pull-right" id="state_moment">
												<?php echo $searchModel->getSelectedTasksStatesNames(); ?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'states', ['template' => "{label}\n{input}"])
										    ->label(false)
										    ->checkboxList(yii\helpers\ArrayHelper::map(\app\models\States::find()->orderBy('ID asc')->all(), 'ID', 'STATE_NAME'), ['separator' => '', 'class' => 'state-checkbox']); ?>
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingTwo">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
											Подразделение
											<div class="what-selected pull-right" id="podr_list_moment"><?php if($searchModel->podr_list) { ?>выбрано: <?= count(explode(',', $searchModel->podr_list)).' подразделение(ия)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'podr_list', [
									        'inputOptions'=>['class'=>'form-control input-sm'],
									        'template' => "<div class=\"col-sm-10\">{input}</div><div class=\"col-sm-2\" style=\"text-align: right;\"><button type=\"button\" id=\"add-podr-button-filter\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>", 
									    ])->textInput()->label(false) ?>
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingThree">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
											Исполнитель
											<div class="what-selected pull-right" id="persons_list_moment"><?php if($searchModel->persons_list) { ?>выбрано: <?= count(explode(',', $searchModel->persons_list)).' исполнителя(ей)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'persons_list', [
									        'inputOptions'=>['class'=>'form-control input-sm'],
									        'template' => "<div class=\"col-sm-10\">{input}</div><div class=\"col-sm-2\" style=\"text-align: right;\"><button type=\"button\" id=\"add-persons-button-filter\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>", 
									    ])->textInput()->label(false) ?>								
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingFour">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
											Исходящий номер
											<div class="what-selected pull-right" id="task_number_moment"><?php if($searchModel->TASK_NUMBER) { ?>выбрано: <?= $searchModel->TASK_NUMBER; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'TASK_NUMBER', [
									        'inputOptions'=>['class'=>'form-control input-sm'],
									    ])->textInput(['onkeyup' => 'viewWhatSelectedInFilter(this.value, \'task_number_moment\');'])->label(false) ?>
									</div>
								</div>
							</div>


							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingFive">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
											Входящий номер
											<div class="what-selected pull-right" id="task_sourcenum_moment"><?php if($searchModel->SOURCENUM) { ?>выбрано: <?= $searchModel->SOURCENUM; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'SOURCENUM', [
									        'inputOptions'=>['class'=>'form-control input-sm'],
									    ])->textInput(['onkeyup' => 'viewWhatSelectedInFilter(this.value, \'task_sourcenum_moment\');'])->label(false) ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingSix">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
											Заказ
											<div class="what-selected pull-right" id="task_ordernum_moment"><?php if(!empty($searchModel->ORDERNUM)) { ?>выбрано: <?= count($searchModel->ORDERNUM). ' заказ(а)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'ORDERNUM', [
									        'template' => "<div class=\"col-sm-12\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-4 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(Select2::classname(), [
										    'options' => ['placeholder' => '', 'multiple' => true],
										    'pluginOptions' => [
										        'tags' => true,
										        'minimumInputLength' => 3,
										        'maximumInputLength' => 25,
										        'ajax' => [
										            'url' => Url::to(['site/filterordernumsearch']),
										            'dataType' => 'json',
										            'data' => new JsExpression('function(params) { return {q:params.term}; }')
										        ],
										    ],
										    'pluginEvents' => [
										    	"select2:select" => "function(e) { 
										    		var selected_data = $(this).val();
										    		if(selected_data.length)
										    			$('#task_ordernum_moment').html('выбрано: '+selected_data.length+' заказ(а)');
										    	}",
										    	"select2:unselect" => "function(e) { 
										    		var selected_data = $(this).val();
											    		if(selected_data != null) {
											    			$('#task_ordernum_moment').html('выбрано: '+selected_data.length+' заказ(а)');
											    		} else {
											    			$('#task_ordernum_moment').html('');
											    		}
										    		
										    	}",

										    ]
										])->label(false);
									    ?>
									    <div><p class="help-block"><small>Вы можете ввести несколько заказов для поиска</small></p></div>	
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingSeven">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
											Заказ ПЭО
											<div class="what-selected pull-right" id="task_peoordernum_moment"><?php if(!empty($searchModel->PEOORDERNUM)) { ?>выбрано: <?= count($searchModel->PEOORDERNUM). ' заказ(а)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseSeven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSeven">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'PEOORDERNUM', [
									        'template' => "<div class=\"col-sm-12\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-4 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(Select2::classname(), [
										    'options' => ['placeholder' => '', 'multiple' => true],
										    'pluginOptions' => [
										        'tags' => true,
										        'minimumInputLength' => 3,
										        'maximumInputLength' => 25,
										        'ajax' => [
										            'url' => Url::to(['site/filterpeoordernumsearch']),
										            'dataType' => 'json',
										            'data' => new JsExpression('function(params) { return {q:params.term}; }')
										        ],
										    ],
										    'pluginEvents' => [
										    	"select2:select" => "function(e) { 
										    		var selected_data = $(this).val();
										    		if(selected_data.length)
										    			$('#task_peoordernum_moment').html('выбрано: '+selected_data.length+' заказ(а)');
										    	}",
										    	"select2:unselect" => "function(e) { 
										    		var selected_data = $(this).val();
											    		if(selected_data != null) {
											    			$('#task_peoordernum_moment').html('выбрано: '+selected_data.length+' заказ(а)');
											    		} else {
											    			$('#task_peoordernum_moment').html('');
											    		}
										    		
										    	}",

										    ]
										])->label(false);
									    ?>
									    <div><p class="help-block"><small>Вы можете ввести несколько заказов для поиска</small></p></div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingEight">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
											Срок выполнения
											<div class="what-selected pull-right" id="task_deadline_moment">
												<?php 
													if($searchModel->deadline_from != '' && $searchModel->deadline_to != '') {
														echo 'от '.$searchModel->deadline_from.' до '.$searchModel->deadline_to;
													} else if($searchModel->deadline_from == '' && $searchModel->deadline_to != '') {
														echo 'до '.$searchModel->deadline_to;
													} else if($searchModel->deadline_from != '' && $searchModel->deadline_to == '') {
														echo 'от '.$searchModel->deadline_from;
													} else if($searchModel->deadline_from == '' && $searchModel->deadline_to == '') {
													
													}
												?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseEight" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEight">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'deadline_from', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_deadline_moment');
										    	}",
										    ]
									    ]);
									    ?>
									    <?= $form_filter->field($searchModel, 'deadline_to', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_deadline_moment');
										    	}",
										    ]
									    ]);
									    ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingNine">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
											Дата поступления в сектор
											<div class="what-selected pull-right" id="task_type_date_3_moment">
												<?php 
													if($searchModel->task_type_date_3_from != '' && $searchModel->task_type_date_3_to != '') {
														echo 'от '.$searchModel->task_type_date_3_from.' до '.$searchModel->task_type_date_3_to;
													} else if($searchModel->task_type_date_3_from == '' && $searchModel->task_type_date_3_to != '') {
														echo 'до '.$searchModel->task_type_date_3_to;
													} else if($searchModel->task_type_date_3_from != '' && $searchModel->task_type_date_3_to == '') {
														echo 'от '.$searchModel->task_type_date_3_from;
													} else if($searchModel->task_type_date_3_from == '' && $searchModel->task_type_date_3_to == '') {
													
													}
												?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseNine" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNine">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'task_type_date_3_from', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_3_moment');
										    	}",
										    ]
									    ]);
									    ?>
									    <?= $form_filter->field($searchModel, 'task_type_date_3_to', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_3_moment');
										    	}",
										    ]
									    ]);
									    ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingTen">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
											Дата поступления в группу
											<div class="what-selected pull-right" id="task_type_date_2_moment">
												<?php 
													if($searchModel->task_type_date_2_from != '' && $searchModel->task_type_date_2_to != '') {
														echo 'от '.$searchModel->task_type_date_2_from.' до '.$searchModel->task_type_date_2_to;
													} else if($searchModel->task_type_date_2_from == '' && $searchModel->task_type_date_2_to != '') {
														echo 'до '.$searchModel->task_type_date_2_to;
													} else if($searchModel->task_type_date_2_from != '' && $searchModel->task_type_date_2_to == '') {
														echo 'от '.$searchModel->task_type_date_2_from;
													} else if($searchModel->task_type_date_2_from == '' && $searchModel->task_type_date_2_to == '') {
													
													}
												?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseTen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTen">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'task_type_date_2_from', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_2_moment');
										    	}",
										    ]
									    ]);
									    ?>
									    <?= $form_filter->field($searchModel, 'task_type_date_2_to', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_2_moment');
										    	}",
										    ]
									    ]);
									    ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingEleven">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
											Дата поступления исполнителю
											<div class="what-selected pull-right" id="task_type_date_1_moment">
												<?php 
													if($searchModel->task_type_date_1_from != '' && $searchModel->task_type_date_1_to != '') {
														echo 'от '.$searchModel->task_type_date_1_from.' до '.$searchModel->task_type_date_1_to;
													} else if($searchModel->task_type_date_1_from == '' && $searchModel->task_type_date_1_to != '') {
														echo 'до '.$searchModel->task_type_date_1_to;
													} else if($searchModel->task_type_date_1_from != '' && $searchModel->task_type_date_1_to == '') {
														echo 'от '.$searchModel->task_type_date_1_from;
													} else if($searchModel->task_type_date_1_from == '' && $searchModel->task_type_date_1_to == '') {
													
													}
												?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseEleven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEleven">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'task_type_date_1_from', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_1_moment');
										    	}",
										    ]
									    ]);
									    ?>
									    <?= $form_filter->field($searchModel, 'task_type_date_1_to', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_1_moment');
										    	}",
										    ]
									    ]);
									    ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingTwelve">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
											Дата завершения
											<div class="what-selected pull-right" id="task_type_date_4_moment">
												<?php 
													if($searchModel->task_type_date_4_from != '' && $searchModel->task_type_date_4_to != '') {
														echo 'от '.$searchModel->task_type_date_4_from.' до '.$searchModel->task_type_date_4_to;
													} else if($searchModel->task_type_date_4_from == '' && $searchModel->task_type_date_4_to != '') {
														echo 'до '.$searchModel->task_type_date_4_to;
													} else if($searchModel->task_type_date_4_from != '' && $searchModel->task_type_date_4_to == '') {
														echo 'от '.$searchModel->task_type_date_4_from;
													} else if($searchModel->task_type_date_4_from == '' && $searchModel->task_type_date_4_to == '') {
													
													}
												?>
											</div>
										</a>
									</h4>
								</div>
								<div id="collapseTwelve" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwelve">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'task_type_date_4_from', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_4_moment');
										    	}",
										    ]
									    ]);
									    ?>
									    <?= $form_filter->field($searchModel, 'task_type_date_4_to', [
									        'template' => "{label}<div class=\"col-sm-10\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-2 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(DatePicker::classname(), [
									    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
									    	'pluginOptions' => [
										        'todayHighlight' => true,
										        'todayBtn' => true,
										        'format' => 'dd-mm-yyyy',
										        'autoclose' => true,
										    ],
										    'pluginEvents' => [
										    	"changeDate" => "function(e) { 
										    		showSelectedDateRange('task_type_date_4_moment');
										    	}",
										    ]
									    ]);
									    ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingthirteen">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsethirteen" aria-expanded="false" aria-controls="collapsethirteen">
											Выпущенная документация
											<div class="what-selected pull-right" id="task_documentation_moment"><?php if(!empty($searchModel->documentation)) { ?>выбрано: <?= count($searchModel->documentation). ' документ(а)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapsethirteen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthirteen">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'documentation', [
									        'template' => "<div class=\"col-sm-12\">{input}</div>", 
									        'labelOptions'=>['class'=>'col-sm-4 control-label'],
									        'inputOptions'=>['class'=>'form-control input-sm']
									    ])->widget(Select2::classname(), [
										    'options' => ['placeholder' => '', 'multiple' => true],
										    'pluginOptions' => [
										        'tags' => true,
										        'minimumInputLength' => 3,
										        'maximumInputLength' => 25,
										        'ajax' => [
										            'url' => Url::to(['site/filterdocumentationsearch']),
										            'dataType' => 'json',
										            'data' => new JsExpression('function(params) { return {q:params.term}; }')
										        ],
										    ],
										    'pluginEvents' => [
										    	"select2:select" => "function(e) { 
										    		var selected_data = $(this).val();
										    		if(selected_data.length)
										    			$('#task_documentation_moment').html('выбрано: '+selected_data.length+' документ(а)');
										    	}",
										    	"select2:unselect" => "function(e) { 
										    		var selected_data = $(this).val();
											    		if(selected_data != null) {
											    			$('#task_documentation_moment').html('выбрано: '+selected_data.length+' документ(а)');
											    		} else {
											    			$('#task_documentation_moment').html('');
											    		}
										    		
										    	}",

										    ]
										])->label(false);
									    ?>
									    <div><p class="help-block"><small>Вы можете ввести несколько документов для поиска</small></p></div>	
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="headingFourteen">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFourteen" aria-expanded="false" aria-controls="collapseFourteen">
											Согласовано с
											<div class="what-selected pull-right" id="agreed_podr_list_moment"><?php if($searchModel->agreed_podr_list) { ?>выбрано: <?= count(explode(',', $searchModel->agreed_podr_list)).' подразделение(ия)'; } ?></div>
										</a>
									</h4>
								</div>
								<div id="collapseFourteen" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFourteen">
									<div class="panel-body">
										<?= $form_filter->field($searchModel, 'agreed_podr_list', [
									        'inputOptions'=>['class'=>'form-control input-sm'],
									        'template' => "<div class=\"col-sm-10\">{input}</div><div class=\"col-sm-2\" style=\"text-align: right;\"><button type=\"button\" id=\"add-agreed-button-filter\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>", 
									    ])->textInput()->label(false) ?>
									</div>
								</div>
							</div>

							<div class="filter-submit-block pull-right">
								<?= Html::a('Очистить фильтр', ['index'], ['class' => 'btn btn-default']) ?>
								<?= Html::submitButton('Применить фильтр', ['class' => 'btn btn-primary', 'id' => 'filter-submit-button']) ?>

								<?php
									$permissions_report_task_search = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
										(SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'action' => 82, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
									if($permissions_report_task_search) {
										//get selected issues ids
										$selected_issues = $dataProvider->getModels();
										if($selected_issues) {
											$selected_issues_array = [];
											foreach($selected_issues as $issue) {
												$selected_issues_array[] = $issue->ID;
											}
											echo Html::a('<i class="glyphicon glyphicon-save"></i>', ['/reports/excel', 'ids' => implode(',', $selected_issues_array)], ['class' => 'btn btn-success', 'title' => 'Сформировать отчет']);
										}
										
									}
								?>
							</div>
							<?php ActiveForm::end(); ?>
							<?php
									if($searchModel->podr_list) {
										$podr_tasks_list = '';
										$chk_podr_list = '';
										$podr_list_array = explode(',', $searchModel->podr_list);
										foreach($podr_list_array as $podr) {
											$query = new \yii\db\Query;
									        $query->select('NAIMPODR AS name, KODPODR AS id, KODRODIT as parent, KODZIFR as code')
									                ->from('STIGIT.V_F_PODR')
									                ->where('KODZIFR = \''.trim($podr).'\'');
									        $command = $query->createCommand();
									        $data = $command->queryOne();
									        if($data) {
												$podr_tasks_list .= '{value: '.$data['code'].', label: \''.$data['name'].'\'},';
												$chk_podr_list .= '$("#podr-check-list-filter").find("#checkbox_filter_'.$data['code'].'").prop("checked", true);';
									        }
									        
										}
									} else {
										$podr_tasks_list = '';
										$chk_podr_list = '';
									}

									if($searchModel->agreed_podr_list) {
										$agreed_tasks_list = '';
										$chk_agreed_list = '';
										$agreed_list_array = explode(',', $searchModel->agreed_podr_list);
										foreach($agreed_list_array as $podr) {
											$query = new \yii\db\Query;
									        $query->select('NAIMPODR AS name, KODPODR AS id, KODRODIT as parent, KODZIFR as code')
									                ->from('STIGIT.V_F_PODR')
									                ->where('KODZIFR = \''.trim($podr).'\'');
									        $command = $query->createCommand();
									        $data = $command->queryOne();
									        if($data) {
												$agreed_tasks_list .= '{value: '.$data['code'].', label: \''.$data['name'].'\'},';
												$chk_agreed_list .= '$("#agreed-check-list-filter").find("#checkbox_filter_agreed_'.$data['code'].'").prop("checked", true);';
									        }
									        
										}
									} else {
										$agreed_tasks_list = '';
										$chk_agreed_list = '';
									}

									if($searchModel->persons_list) {
										$pers_tasks_list = '';
										$chk_pers_list = '';
										$persons_list_array = explode(',', $searchModel->persons_list);
										foreach($persons_list_array as $pers) {
											$query = new \yii\db\Query;
									        $query->select('*')
									                ->from('STIGIT.V_F_PERS')
									                ->where('TN = \''.trim($pers).'\'');
									        $command = $query->createCommand();
									        $data = $command->queryOne();
									        if($data) {
												$pers_tasks_list .= '{value: '.$data['TN'].', label: \''.$data['FIO'].'\'},';
												$chk_pers_list .= '$("#persons-check-list-filter").find("#checkbox_'.$data['TN'].'").prop("checked", true);';
									        }
										}
									} else {
										$pers_tasks_list = '';
										$chk_pers_list = '';
									}


									$this->registerJs('$(document).ready(function(){ 
												$("#searchtasks-podr_list").tokenfield(\'setTokens\', ['.substr_replace($podr_tasks_list ,"",-1).']); '.$chk_podr_list.'
												$("#searchtasks-agreed_podr_list").tokenfield(\'setTokens\', ['.substr_replace($agreed_tasks_list ,"",-1).']); '.$chk_agreed_list.'
												$("#searchtasks-persons_list").tokenfield(\'setTokens\', ['.substr_replace($pers_tasks_list ,"",-1).']); 
									        	var selected_values = {};
												$(\'#podr-check-list-filter input:checked\').each(function() {
												    selected_values[$(this).attr(\'value\')] = $(this).attr(\'data-title\');
												});
												var csrfToken = $(\'meta[name="csrf-token"]\').attr(\'content\');
											    $.ajax({
										        	type: "POST",
										        	dataType: \'json\',
										        	url: "index.php?r=site/getpersons",
										        	data: "selected_podr="+JSON.stringify(selected_values)+"&_csrf="+csrfToken,
										        	success: function(data,status){
										        		$(\'#persons-check-list-filter\').html(data);
										        		$(\'#persons-check-list-filter\').tree({checkbox: false});
										        		'.$chk_pers_list.'
										        	},
											    });
									    		}); ', View::POS_END, 'filter_update');



							?>
								
							    
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /#page-content-wrapper -->



<!-- Modal -->
<div class="modal fade" id="issue-modal" role="dialog" aria-labelledby="issue-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" id="close-label-issue" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="issue-modal-label">Выдача задания</h4>
			</div>
			<?php $form = ActiveForm::begin([
	                'id' => 'issue-form',
	                //'enableAjaxValidation' => true,
	                'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
	        ]); ?>
			<div class="modal-body">
				<?= $form->errorSummary($model); ?>
						
					<?php
				    	$reason_types = yii\helpers\ArrayHelper::map(\app\models\ReasonTypes::find()->orderBy('ID ASC')->all(), 'ID', 'REASON_TYPE');
				    	echo $form->field($model, 'reason_type', [
					        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
					        'labelOptions'=>['class'=>'col-sm-4 control-label'],
					        'inputOptions'=>['class'=>'form-control input-sm']
					    ])->widget(\kartik\select2\Select2::classname(), [
						    'options' => ['placeholder' => ''],
						    'hideSearch' => false,
						    'data' => $reason_types,
						    
						]);
						
				    ?>

				    <?= $form->field($model, 'designation', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->widget(Select2::classname(), [
				    	//'theme' => 'todc',
					    'initValueText' => '',
					    'options' => ['placeholder' => ''],
					    'pluginOptions' => [
					        'allowClear' => true,
					        'tags' => true,
					        'minimumInputLength' => 3,
					        'maximumInputLength' => 25,
					        'ajax' => [
					            'url' => Url::to(['site/designationsearch']),
					            'dataType' => 'json',
					            'data' => new JsExpression('function(params) { return {q:params.term}; }')
					        ],
					        'createSearchChoice' => new JsExpression('function (term) { return {id: term, text: term}; }'),
					        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
					        'templateResult' => new JsExpression('function(designation) { return designation.text; }'),
					        'templateSelection' => new JsExpression('function (designation) { return designation.text; }'),
					    ],
					    'pluginEvents' => [
					    	"select2:selecting" => "function(e) { 
					    		var selected_data = e.params.args.data; 
					    		if (typeof selected_data.ordernum != 'undefined') {
						    		$(\"#issueform-ordernum\").val(selected_data.ordernum);
						    		$(\"#issueform-peoordernum\").val(selected_data.peoordernum);
						    		$(\"#issueform-documentid\").val(selected_data.id);
						    		$(\"#issueform-income_number\").val(selected_data.income);
						    		$(\"#issueform-stagenum\").val(selected_data.stagenum);
						    	} else {
						    		$(\"#issueform-documentid\").val('');
						    		$(\"#issueform-ordernum\").val('');
						    		$(\"#issueform-peoordernum\").val('');
						    		$(\"#issueform-income_number\").val('');
						    		$(\"#issueform-stagenum\").val('');
						    	}
					    	}",
					    ]
					]);
				    ?>

				    <?= $form->field($model, 'documentid', ['options' => ['class' => '']])->hiddenInput()->label(false) ?>

				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'income_number', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm'],
				        'enableAjaxValidation' => true
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'task_number', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm'],
				        'enableAjaxValidation' => true
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'podr_list', [
				        'template' => "{label}<div class=\"col-sm-6\">{input}</div><div class=\"col-sm-2\" style=\"text-align: right;\"><button type=\"button\" id=\"add-podr-button\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'persons_list', [
				        'template' => "{label}<div class=\"col-sm-6\">{input}</div><div class=\"col-sm-2\" style=\"text-align: right;\"><button type=\"button\" id=\"add-persons-button\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'ordernum', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'peoordernum', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'stagenum', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textInput() ?>
				    <div class="hr-line-dashed"></div>

				    <?= $form->field($model, 'date', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->widget(DatePicker::classname(), [
				    	'type' => DatePicker::TYPE_COMPONENT_APPEND,
				    	'pluginOptions' => [
					        'todayHighlight' => true,
					        'todayBtn' => true,
					        'format' => 'dd-mm-yyyy',
					        'autoclose' => true,
					    ]
				    ]);
				    ?>
				    <div class="hr-line-dashed"></div>


				    <?= $form->field($model, 'message', [
				        'template' => "{label}<div class=\"col-sm-8\">{input}</div>\n{hint}", 
				        'labelOptions'=>['class'=>'col-sm-4 control-label'],
				        'inputOptions'=>['class'=>'form-control input-sm']
				    ])->textArea() ?>
				    <div class="hr-line-dashed"></div>

				    
				    

				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="issue-cancel-button">Отмена</button>
				<?= Html::submitButton('Выдать', ['class' => 'btn btn-primary', 'id' => 'issue-submit-button']) ?>
			</div>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>

<div class="modal fade" id="podr-select-modal" role="dialog" aria-labelledby="podr-select-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-podr">Выбор подразделений</h4>
			</div>
			<div class="modal-body" id="podr-check-list">
				<?= $podr_data; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
				<!-- <button type="button" id="select-podr" class="btn btn-primary">Выбрать указанные подразделения</button> -->
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="podr-select-modal-filter" role="dialog" aria-labelledby="podr-select-modal-filter-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-podr-filter">Фильтр: выбор подразделений</h4>
			</div>
			<div class="modal-body" id="podr-check-list-filter">
				<?= $podr_data_filter; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="agreed-select-modal-filter" role="dialog" aria-labelledby="agreed-select-modal-filter-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-agreed-filter">Фильтр: выбор подразделений</h4>
			</div>
			<div class="modal-body" id="agreed-check-list-filter">
				<?= $agreed_data_filter; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="persons-select-modal" role="dialog" aria-labelledby="podr-select-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-pers">Выбор исполнителей</h4>
			</div>
			<div class="modal-body" id="persons-check-list">
				<div class="alert alert-warning" role="alert">Пожалуйста, сначала укажите подразделения</div>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
				<button type="button" id="select-persons" class="btn btn-primary">Выбрать указанных исполнителей</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="issue-view-modal" role="dialog" data-backdrop="static"  data-keyboard="false" aria-labelledby="podr-select-modal-label">
	<div class="modal-dialog" role="document" style="width: 800px;">
		<div class="modal-content">
			<div class="modal-header">
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<a href="" target="_blank" id="update-issue-top-button" style="margin-right: 10px; font-size: 13px; padding-top: 1px;" class="close"><span alt="редактировать" title="редактировать" class="glyphicon glyphicon-edit" aria-hidden="true"></span> редактировать</a>
				<h4 class="modal-title" id="myModalLabel-issue"></h4>
			</div>
			<div class="modal-body" id="issue-view-table">
				
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<!-- <button type="button" id="update-issue-button" class="btn btn-primary">Редактировать (модальное окно)</button> -->
			<a href="#" id="update-issue-button-new-tab" target="_blank" class="btn btn-primary">Редактировать (вкладка)</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="issue-update-modal" role="dialog" data-backdrop="static"  data-keyboard="false" aria-labelledby="issue-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="partial-update-form">
			
				
			
		</div>
	</div>
</div>


<div class="modal fade" id="podr-select-modal-update" role="dialog" aria-labelledby="podr-select-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-podr-update">Выбор подразделений</h4>
			</div>
			<div class="modal-body" id="podr-check-list-update">
				<?= $podr_data; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="persons-select-modal-filter" role="dialog" aria-labelledby="podr-select-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel-pers">Фильтр: выбор исполнителей</h4>
			</div>
			<div class="modal-body" id="persons-check-list-filter">
				<div class="alert alert-warning" role="alert">Пожалуйста, сначала укажите подразделения</div>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
				<button type="button" id="select-persons-filter" class="btn btn-primary">Выбрать указанных исполнителей</button>
			</div>
		</div>
	</div>
</div>

