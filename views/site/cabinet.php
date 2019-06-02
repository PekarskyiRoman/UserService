<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Cabinet';
?>
<div>
    <span>Your balance :</span>
    <span><?= Yii::$app->user->identity->balance ?></span>
</div>
<div>
    <?= Html::a('Send funds to another user', Url::to(['/site/send-funds']), ['class' => 'btn btn-success']) ?>
</div>
<div>
    <h2 class="text-center">Your transactions history</h2>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'columns' => [
        'id',
        'sender_login',
        'recipient_login',
        'amount'
    ],
]); ?>