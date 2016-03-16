<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Авторизация в системе';
?>

    <div class="login-section">
            <!-- начало формы авторизации -->
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'well', 'role' => 'login'],
            ]); ?>
                    <div class="row">
                        <div class="col-xs-3">
                            <img src="/images/logo.png" width="80">
                        </div>
                        <div class="col-xs-9">
                            <h4><?= Yii::$app->params['system_title']; ?></h4> 
                            <p class="help-block login-help"><?= Yii::$app->params['system_subtitle']; ?></p>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <?= $form->field($model, 'username', ['template' => "{input}<span class=\"glyphicon glyphicon-user\"></span>\n{hint}\n{error}"])->label(false)->textInput(['placeholder' => 'Логин', 'class' => 'form-control login-input-box']) ?>
                    </div>
                    <div>
                        <?= $form->field($model, 'password', ['template' => "{input}<span class=\"glyphicon glyphicon-lock\"></span>\n{hint}\n{error}"])->label(false)->passwordInput(['placeholder' => 'Пароль', 'class' => 'form-control login-input-box']) ?>
                    </div>
                
                    <?= Html::submitButton('Вход', ['class' => 'btn btn-block btn-primary disabled', 'id' => 'login-submit-button', 'name' => 'login-button']) ?>
                    
                    <div class="login-copyrights">&copy; 2015 <?= Yii::$app->params['system_copyrights']; ?></div> 
            <?php ActiveForm::end(); ?>
            <!-- конец формы авторизации -->
    </div>    
