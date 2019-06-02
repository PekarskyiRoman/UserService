<?php

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $transactionModel \app\models\Transaction */
/* @var $recipients array */

$this->title = 'Send funds';
$form = ActiveForm::begin([
    'method' => 'POST',
    'id' => 'send_funds_form'
]); ?>

<?= $form->field($transactionModel, 'sender_login')->hiddenInput(['value' => Yii::$app->user->identity->login])->label(false);?>

<?= $form->field($transactionModel, 'recipient_login')->widget(Select2::className(), [
    'data' => $recipients,
    'options' => [
        'placeholder' => 'Select User',
    ],
    'pluginOptions' => [
        'allowClear' => true
    ]
]);?>

<?= $form->field($transactionModel, 'amount')->input('number', ['min' => 0.01, 'step' => 0.01]) ?>

<?= Html::submitButton('Send', ['class' => 'btn btn-success']); ?>

<?php ActiveForm::end();