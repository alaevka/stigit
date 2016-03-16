<?php

namespace app\models;

use Yii;

class StatesNext extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.STATES_NEXT';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['STATE_ID', 'NEXT_STATE_ID', 'TRACT_ID'], 'required'],
            [['DEL_TRACT_ID'], 'safe'],
        ];
    }

    public function getState()
    {
        return $this->hasOne(\app\models\States::className(), ['ID' => 'NEXT_STATE_ID'])->where(['DEL_TRACT_ID' => 0]);
    }

    public function getState_name_state_colour()
    {
        //$this->STATE_ID;
        return '<img height="16" src="/images/items_status/'.$this->state->STATE_COLOUR.'.png"> '.$this->state->STATE_NAME;
    }

    
}
