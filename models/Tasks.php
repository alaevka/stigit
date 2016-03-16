<?php
/*
    Модель заданий
*/
namespace app\models;
use Yii;
use yii\helpers\Url;

class Tasks extends \yii\db\ActiveRecord
{
    //объявление используемых проперти и констант
    const SCENARIO_UPDATE_PERSON = 'update_person';
    const SCENARIO_UPDATE_BOSS = 'update_boss';

    public $podr_list;
    public $persons_list;
    public $task_type_date_3;
    public $transactions_tract_datetime;
    public $task_type_date_1;
    public $task_type_date_4;
    public $documentation;
    public $agreed_podr_list;
    public $transmitted_podr_list;
    public $state;
    public $hidden_ordernum;
    public $hidden_peoordernum;
    public $STAGENUM;
    public $DESIGNATION;
    public $REASON_ID;
    public $DOCUMENTID;
    

    /**
     * @метод, возвращающий имя таблицы TASKS
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.TASKS';
    }

    /**
     * @валидация
     */
    public function rules()
    {
        return [
            [['TASK_NUMBER', 'ORDERNUM', 'PEOORDERNUM', 'TASK_TEXT', 'DEADLINE', 'TRACT_ID'], 'required'],
            [['TASK_NUMBER'], 'integer'],
            [['TASK_NUMBER'], 'unique'],
            [['SOURCENUM', 'state', 'podr_list'], 'required', 'on' => self::SCENARIO_UPDATE_PERSON],
            [['SOURCENUM', 'podr_list'], 'required', 'on' => self::SCENARIO_UPDATE_BOSS],
            [['SOURCENUM'], 'string', 'max' => 25],
            [['STAGENUM', 'REASON', 'REASON_ID', 'DOCUMENTID', 'DEL_TRACT_ID', 'ADDITIONAL_TEXT', 'SOURCENUM', 'REPORT_TEXT', 'podr_list', 'persons_list', 'task_type_date_3', 'task_type_date_1', 'task_type_date_4', 'documentation', 'agreed_podr_list', 'transmitted_podr_list', 'state', 'hidden_ordernum', 'hidden_peoordernum'], 'safe'],
        ];
    }

    //реляция с таблицей TaskStates
    public function getTaskstates()
    {
        return $this->hasMany(\app\models\TaskStates::className(), ['TASK_ID' => 'ID'])->where(['IS_CURRENT' => 1]);
    }

    //реляция с таблицей PodrTasks
    public function getPodrtasks()
    {
        return $this->hasMany(\app\models\PodrTasks::className(), ['TASK_ID' => 'ID'])->where('PODR_TASKS.DEL_TRACT_ID = :del_tract_id', ['del_tract_id'=>0]);
    }

    //реляция с таблицей TaskConfirms
    public function getTaskconfirms()
    {
        return $this->hasMany(\app\models\TaskConfirms::className(), ['TASK_ID' => 'ID'])->where('TASK_CONFIRMS.DEL_TRACT_ID = :del_tract_id', ['del_tract_id'=>0]);
    }

    //реляция с таблицей PersTasks
    public function getPerstasks()
    {
        return $this->hasMany(\app\models\PersTasks::className(), ['TASK_ID' => 'ID'])->where('PERS_TASKS.DEL_TRACT_ID = :del_tract_id', ['del_tract_id'=>0]);
    }

    //реляция с таблицей TaskDocs
    public function getTaskdocs()
    {
        return $this->hasMany(\app\models\TaskDocs::className(), ['TASK_ID' => 'ID'])->where('TASK_DOCS.DEL_TRACT_ID = :del_tract_id', ['del_tract_id'=>0]);
    }

    //реляция с таблицей TaskDates
    public function getDatetype3()
    {
        return $this->hasOne(\app\models\TaskDates::className(), ['TASK_ID' => 'ID'])->where('TASK_DATES.DEL_TRACT_ID = :del_tract_id and TASK_DATES.DATE_TYPE_ID = :date_type_id', ['del_tract_id'=>0, 'date_type_id'=>3]);
    }

    //реляция с таблицей TaskDates
    public function getDatetype2()
    {
        return $this->hasOne(\app\models\TaskDates::className(), ['TASK_ID' => 'ID'])->where('TASK_DATES.DEL_TRACT_ID = :del_tract_id and TASK_DATES.DATE_TYPE_ID = :date_type_id', ['del_tract_id'=>0, 'date_type_id'=>2]);
    }

    //реляция с таблицей TaskDates
    public function getDatetype1()
    {
        return $this->hasOne(\app\models\TaskDates::className(), ['TASK_ID' => 'ID'])->where('TASK_DATES.DEL_TRACT_ID = :del_tract_id and TASK_DATES.DATE_TYPE_ID = :date_type_id', ['del_tract_id'=>0, 'date_type_id'=>1]);
    }

    //реляция с таблицей TaskDates
    public function getDatetype4()
    {
        return $this->hasOne(\app\models\TaskDates::className(), ['TASK_ID' => 'ID'])->where('TASK_DATES.DEL_TRACT_ID = :del_tract_id and TASK_DATES.DATE_TYPE_ID = :date_type_id', ['del_tract_id'=>0, 'date_type_id'=>4]);
    }

    /*
        Описание полей для формирования label
    */
    public function attributeLabels()
    {
        return [
            'DESIGNATION' => 'Основание',
            'TASK_NUMBER' => 'Исходящий номер',
            'ORDERNUM' => 'Заказ (изделие)',
            'PEOORDERNUM' => 'Заказ ПЭО',
            'TASK_TEXT' => 'Содержание задания',
            'DEADLINE' => 'Срок выполнения',
            'TRACT_ID' => 'id транзакции',
            'DOCUMENTID' => 'id основания',
            'DEL_TRACT_ID' => 'if deleted',
            'SOURCENUM' => 'Входящий номер',
            'ADDITIONAL_TEXT' => 'Дополнительные указания',
            'REPORT_TEXT' => 'Отчет о работе',
            'podr_list' => 'Подразделения',
            'persons_list' => 'Исполнитель',
            'task_type_date_3' => 'Дата поступления в сектор',
            'transactions_tract_datetime' => 'Дата поступления в группу',
            'task_type_date_1' => 'Дата поступления исполнителю',
            'task_type_date_4' => 'Дата завершения',
            'documentation' => 'Выпущенная документация',
            'agreed_podr_list' => 'Согласовано с',
            'transmitted_podr_list' => 'Передано в',
            'state' => 'Состояние',
            'REASON' => 'Основание',
            'STAGENUM' => 'Этап',
        ];
    }

    /*
        Получение иконки предыдущего статуса задания 
    */
    public function _getLastTaskStatus($id) {

        $task_state = \app\models\TaskStates::find()->where(['TASK_ID' => $id])->orderBy('ID DESC')->LIMIT(1)->one();
        if($task_state) {
            $this_task_state = \app\models\States::findOne($task_state->STATE_ID);
            return $this_task_state->getState_name_state_colour_without_text();
        } else {
            return '';
        }
    }

    /*
        Получение иконки и текста предыдущего статуса задания 
    */
    public function _getLastTaskStatusWithText($id) {
        $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
        if($pers_tasks) {
            $task_state = \app\models\TaskStates::find()->where(['PERS_TASKS_ID' => $pers_tasks->ID])->orderBy('ID DESC')->LIMIT(1)->one();
            if($task_state) {
                $this_task_state = \app\models\States::findOne($task_state->STATE_ID);
                return $this_task_state->getState_name_state_colour();
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /*
        Получение текущего статуса без текста, только иконка
    */
    public function _getCurrentTaskStatus($id) {
        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
        if($persons) {
            $states_array = [];
            foreach($persons as $person) {
                $query = new \yii\db\Query;
                $query->select('*')
                ->from('STIGIT.V_F_PERS')
                ->where('TN = \'' . $person->TN .'\'');
                $command = $query->createCommand();
                $data = $command->queryOne();

                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
               
                $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                if($task_state) {
                    $states_array[] = $task_state->STATE_ID;
                } else {
                    return '(не задано)';
                }
            }
            if(!empty($states_array)) {
                $min_state = min($states_array);
                $state = \app\models\States::findOne($min_state);
                return $state->getState_name_state_colour_without_text();
            }
        }
    }


    /*
        Получение текущего статуса с текстом
    */
    public function _getCurrentTaskStatusWithText($id) {
        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
        if($persons) {
            $states_array = [];
            foreach($persons as $person) {
                $query = new \yii\db\Query;
                $query->select('*')
                ->from('STIGIT.V_F_PERS')
                ->where('TN = \'' . $person->TN .'\'');
                $command = $query->createCommand();
                $data = $command->queryOne();

                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
               
                $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                if($task_state) {
                    $states_array[] = $task_state->STATE_ID;
                } else {
                    return '';
                }
            }
            if(!empty($states_array)) {
                $min_state = min($states_array);
                $state = \app\models\States::findOne($min_state);
                return $state->getState_name_state_colour();
            }
        }
    }


    /*
        Возвращает Id текущего
    */
    public function _getCurrentTaskStatusWithId($id) {
        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
        if($persons) {
            $states_array = [];
            foreach($persons as $person) {
                $query = new \yii\db\Query;
                $query->select('*')
                ->from('STIGIT.V_F_PERS')
                ->where('TN = \'' . $person->TN .'\'');
                $command = $query->createCommand();
                $data = $command->queryOne();
                
                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();

                $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                if($task_state) {
                    $states_array[] = $task_state->STATE_ID;
                } else {
                    return '';
                }
            }
            if(!empty($states_array)) {
                $min_state = min($states_array);
                $state = \app\models\States::findOne($min_state);
                return $state->ID;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function _getStatusPerson() {
        
        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $this->ID, 'DEL_TRACT_ID' => 0])->all();
        if($persons) {
            $list = '';
            foreach($persons as $person) {
                $query = new \yii\db\Query;
                $query->select('*')
                    ->from('STIGIT.V_F_PERS')
                    ->where('TN = \'' . $person->TN .'\'');
                $command = $query->createCommand();
                $data = $command->queryOne();
                //$list .= '<nobr><a href="'.Url::to(['user', 'id'=>$person->TN]).'">'.$data['FAM'].' '.mb_substr($data['IMJ'], 0, 1, 'UTF-8').'. '.mb_substr($data['OTCH'], 0, 1, 'UTF-8').'.</a></nobr><br>';
                //get current state 
                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$this->ID, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();

                $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $this->ID])->one();
                if($task_state) {
                    $state = $task_state->getState_name_state_colour_without_text();
                    $state_date = $task_state->getStateDate();
                } else {
                    $state = '';
                    $state_date = '';
                }

                $list .= '<nobr>'.$state.'&nbsp;<a href="'.Url::to(['user', 'id'=>$person->TN]).'">'.$data['FIO'].'</a></nobr><br>';
            }
            return $list;
        } else {
            $podr = \app\models\PodrTasks::find()->where(['TASK_ID' => $this->ID])->all();
            if($podr) {
                $list = '';
                foreach($podr as $task) {
                    $query = new \yii\db\Query;
                    $query->select('*')
                        ->from('STIGIT.V_F_PODR')
                        ->where('KODZIFR = \'' . trim($task->KODZIFR) .'\'');
                    $command = $query->createCommand();
                    $data = $command->queryOne();
                    if(isset($data['NAIMPODR']))
                        $list .= $data['NAIMPODR']."<br>";
                }
                return $list;
            }

        }

    }

    
}
