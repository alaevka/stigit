<?php
/*
    Модель для таблицы  pers_tasks
*/
namespace app\models;

use Yii;

class PersTasks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.PERS_TASKS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TASK_ID', 'TN', 'TRACT_ID'], 'required'],
            [['DEL_TRACT_ID'], 'safe'],
        ];
    }

   
}
