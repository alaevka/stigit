<?php

namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{

    //public $TN, $LOGIN, $PASSWORD;

    public static function tableName()
    {
        return 'SYS.V_USER_SECURE';
    }

    public function rules()
    {
        return [
            [['LOGIN', 'PASSWORD'], 'required'],
            [['LOGIN', 'PASSWORD'], 'string', 'max' => 24]            
        ];
    }

    /*
        Связь с таблицей транзакций
    */
    public function getTransactions()
    {
        return $this->hasMany(\app\models\Transactions::className(), ['TN' => 'ID']);
    }


    /**
     * Поиск пользователя по TN
     */
    public static function findIdentity($id)
    {
        return static::findOne(['TN' => $id]);
    }

    /**
     * @inheritdoc Поиск пользователя по токену (пока не используется)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    // /**
    //  * Поиск пользователя по логину
    //  *
    //  * @param  string      $username
    //  * @return static|null
    //  */
    public static function findByUsername($username)
    {
        return static::findOne(['LOGIN' => $username]);
    }


    /**
     * @Получение id(TN) пользователя
     */
    public function getId()
    {
        return $this->TN;
    }

    /**
     * @inheritdoc (не используется)
     */
    public function getAuthKey()
    {
        return $this->TN;
    }

    /**
     * @inheritdoc (не используется)
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Валидация пароля
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->PASSWORD === md5($password);
    }
}
