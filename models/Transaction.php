<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string $sender_login
 * @property string $recipient_login
 * @property string $amount
 *
 * @property User $recipientLogin
 * @property User $senderLogin
 */
class Transaction extends \yii\db\ActiveRecord
{
    const MINIMUM_BALANCE = -1000;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_login', 'recipient_login', 'amount'], 'required'],
            [['amount'], 'number', 'min' => 0.01],
            [['sender_login', 'recipient_login'], 'string', 'max' => 255],
            [['recipient_login'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['recipient_login' => 'login']],
            [['sender_login'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender_login' => 'login']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_login' => 'Sender Login',
            'recipient_login' => 'Recipient Login',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientLogin()
    {
        return $this->hasOne(User::className(), ['login' => 'recipient_login']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSenderLogin()
    {
        return $this->hasOne(User::className(), ['login' => 'sender_login']);
    }

    public static function getUserTransactions($login)
    {
        $query = self::find()->where(['or', ['sender_login' => $login], ['recipient_login' => $login]])->orderBy('id DESC');
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
    }

    public function checkBalance()
    {
        if(($this->senderLogin->balance - $this->amount) >= self::MINIMUM_BALANCE) {
            return true;
        }
        return false;
    }

    public function completeFundsMovement()
    {
        $this->senderLogin->reduceBalance($this->amount);
        $this->recipientLogin->addBalance($this->amount);
    }
}
