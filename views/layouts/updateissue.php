<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\web\View;

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
<script>
// window.onbeforeunload = function (e) {
//     e = e || window.event;

//     // For IE and Firefox prior to version 4
//     if (e) {
//         e.returnValue = 'Вы уверены, что хотите закрыть вкладку редактирования задания?';
//     }

//     // For Safari
//     return 'Вы уверены, что хотите закрыть вкладку редактирования задания?';
// };
</script>
<?php $this->beginBody() ?>
	<div class="container">
    <?= $content ?>
   	</div>
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
