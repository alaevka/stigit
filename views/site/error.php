<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->context->layout = 'empty';
?>
<!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row">

                <h1>Ошибка</h1>

                <div class="alert alert-danger">
                    <?= nl2br(Html::encode($message)) ?>
                </div>
                
            </div>
        </div>
    </div>
