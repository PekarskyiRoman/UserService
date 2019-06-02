<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Users and balances';
?>
<div>
    <h2 class="text-center">Active users and their balances</h2>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'columns' => [
        'login',
        'balance'
    ],
]); ?>