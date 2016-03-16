<?php
/*
    Модель, наследующая класс Tasks, 
    содержит в себе методы для фильтрации заданий
*/
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tasks;

class SearchTasks extends Tasks
{
    //переменные для фильтра таблицы
    public $states;
    public $deadline_from;
    public $deadline_to;
    public $task_type_date_3_from;
    public $task_type_date_3_to;
    public $task_type_date_1_from;
    public $task_type_date_1_to;
    public $task_type_date_4_from;
    public $task_type_date_4_to;
    public $task_type_date_2_from;
    public $task_type_date_2_to;
    private $dateFormat = 'YYYY-MM-DD hh24:mi:ss';
    public $ORDERNUM;
    public $PEOORDERNUM;
    public $TASK_NUMBER;
    public $SOURCENUM;


   

    /*
        валидация данных
    */
    public function rules()
    {
        return [
            [['DESIGNATION', 'TASK_NUMBER', 'ORDERNUM', 'PEOORDERNUM', 'SOURCENUM', 'DEADLINE', 'TASK_TEXT', 'states', 'podr_list', 'persons_list', 'deadline_from', 'deadline_to',
            'task_type_date_3_from', 'task_type_date_3_to', 'task_type_date_1_from', 'task_type_date_1_to', 'task_type_date_4_from', 'task_type_date_4_to', 
            'task_type_date_2_from', 'task_type_date_2_to', 'documentation', 'agreed_podr_list'], 'safe'],
        ];
    }

    //наследование сценариев родителя
    public function scenarios()
    {
        return Model::scenarios();
    }

    //возвращает объект с названиями статусов
    public function getSelectedTasksStatesNames()
    {
        $selected_states = \app\models\States::find()->where(['ID' => $this->states])->all();
        if($selected_states) {
            $selected_states_string = 'выбрано: ';
            foreach($selected_states as $state) {
                $selected_states_string .= $state->STATE_NAME.' ';
            }  
            return $selected_states_string;
        }
        
    }

    /*
        название полей поиска для label
    */
    public function attributeLabels()
    {
        return [
            'deadline_from' => 'От',
            'deadline_to' => 'До',
            'task_type_date_3_from' => 'От',
            'task_type_date_3_to' => 'До',
            'task_type_date_1_from' => 'От',
            'task_type_date_1_to' => 'До',
            'task_type_date_4_from' => 'От',
            'task_type_date_4_to' => 'До',
            'task_type_date_2_from' => 'От',
            'task_type_date_2_to' => 'До',
        ];
    }

    /*
        метод поиска заданий по заданым фильтрам
    */
    public function search($params)
    {
        $query = Tasks::find();
        //формируем провайдер
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //задание сортировки по умолчанию
        $dataProvider->sort->attributes = ['TASKS.ID' => [
            'asc' => ['TASKS.ID' => SORT_ASC],
            'desc' => ['TASKS.ID' => SORT_DESC],
        ]];

        $dataProvider->sort->defaultOrder = ['TASKS.ID' => SORT_DESC];

        //overdue issues filter
        if(isset($params['overdue']) && $params['overdue'] == 1) {
            
            $tasks = \app\models\Tasks::find()->all();
            $list = [];
            foreach($tasks as $task) {
                $id = $task->ID;
                $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
                if($persons) {
                    $states_array = [];
                   
                    foreach($persons as $person) {
                        
                        $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
                       
                        $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                        if($task_state) {
                            $states_array[] = $task_state->STATE_ID;
                        } else {
                            $list[] = $id;
                        }
                    }
                    if(!empty($states_array)) {
                        $min_state = min($states_array);
                        $state = \app\models\States::findOne($min_state);
                    
                    }
                }
                if(isset($state)) {

                    if($state->ID != 7 || $state->ID != 9) {
                        $list[] = $id;
                    }
                }

            }

            $list = array_unique($list);

            $query->andFilterWhere(['TASKS.ID' => $list]);
            $now = date("Y-m-d");
            $query->andFilterWhere(['<', 'TASKS.DEADLINE', new \yii\db\Expression("to_date('" . $now . "','{$this->dateFormat}')")]);
            $query->joinWith('perstasks'); 
            $query->andFilterWhere(['PERS_TASKS.TN' => \Yii::$app->user->id]);
        }


        //own issues filter
        if(isset($params['own_issues']) && $params['own_issues'] == 1) {
            $query->joinWith('perstasks'); 
            $query->andFilterWhere(['PERS_TASKS.TN' => \Yii::$app->user->id]);
        }

        if(isset($params['for_person']) && $params['for_person'] != '') {
            $query->joinWith('perstasks'); 
            $query->andFilterWhere(['PERS_TASKS.TN' => $params['for_person']]);
        }

        if(isset($params['for_podr']) && $params['for_podr'] != '') {
            $query->joinWith('podrtasks'); 
            $query->andFilterWhere(['PODR_TASKS.KODZIFR' => $params['for_podr']]);
        }

        //podr issues filter
        if(isset($params['podr_issues']) && $params['podr_issues'] == 1) {

            //check permission
            $permissions_podr_tasks_my = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
                (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :id_dolg and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'id_dolg' =>  \Yii::$app->session->get('user.user_iddolg'), 'action' => 21, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
            if($permissions_podr_tasks_my) {
                if($permissions_podr_tasks_my->PERM_LEVEL == 1 || $permissions_podr_tasks_my->PERM_LEVEL == 2) {

                    //get podr id of this user
                    $query_dao = new \yii\db\Query;
                    $query_dao->select('*')
                        ->from('STIGIT.V_F_PERS')
                        ->where('TN = \'' . \Yii::$app->user->id .'\'');
                    $command = $query_dao->createCommand();
                    $data = $command->queryOne();
                    //вот тут решить что означает выданные моему подразделению
                    $query->joinWith('podrtasks'); 
                    $query->andFilterWhere(['PODR_TASKS.KODZIFR' => trim($data['KODZIFR'])]);
                } else {
                    throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Выданные любым задания"'); 
                }
            } else {
                throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Выданные любым задания"'); 
            }
        }

        //tasks my filter
        if(isset($params['tasks_my']) && $params['tasks_my'] == 1) {
            //check permission
            $permissions_podr_tasks_my = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
            (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :id_dolg and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'id_dolg' =>  \Yii::$app->session->get('user.user_iddolg'), 'action' => 23, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
                if($permissions_podr_tasks_my) {
                    if($permissions_podr_tasks_my->PERM_LEVEL == 1 || $permissions_podr_tasks_my->PERM_LEVEL == 2) {
                        //get all current user transactions
                        $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id])->all();
                        if($transactions) {
                            $transactions_array = [];
                            foreach($transactions as $transaction) {
                                $transactions_array[] = $transaction->ID;
                            }
                            $query->andFilterWhere(['TRACT_ID' => $transactions_array]);
                        } 
                    } else {
                        throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Выданные лично задания"'); 
                    }
                } else {
                    throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Выданные лично задания"'); 
                }
            
        }


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        //проверяем существует ли фильтр и добавляем его в запрос провайдера для каждого из полей фильтра
        if(!empty($this->states)) {
            $query->joinWith('taskstates'); 
            $query->andFilterWhere(['TASK_STATES.STATE_ID' => $this->states]);
        }
        if(!empty($this->podr_list)) {
            $podr_list = array_map('trim', explode(',', $this->podr_list));
            $query->joinWith('podrtasks'); 
            $query->andFilterWhere(['PODR_TASKS.KODZIFR' => $podr_list]);
        }
        if(!empty($this->agreed_podr_list)) {
            $agreed_podr_list = array_map('trim', explode(',', $this->agreed_podr_list));
            $query->joinWith('taskconfirms'); 
            $query->andFilterWhere(['TASK_CONFIRMS.KODZIFR' => $agreed_podr_list]);
        }
        if(!empty($this->persons_list)) {
            $persons_list = array_map('trim', explode(',', $this->persons_list));
            $query->joinWith('perstasks'); 
            $query->andFilterWhere(['PERS_TASKS.TN' => $persons_list]);
        }
        if(!empty($this->documentation)) {
            $query->joinWith('taskdocs'); 
            $query->andFilterWhere(['TASK_DOCS.DOC_CODE' => $this->documentation]);
        }
        if($this->deadline_from != '' && $this->deadline_to != '') {
            $deadline_from = explode('-', $this->deadline_from);
            $deadline_from_formatted = $deadline_from[2].'-'.$deadline_from[1].'-'.$deadline_from[0];
            $deadline_to = explode('-', $this->deadline_to);
            $deadline_to_formatted = $deadline_to[2].'-'.$deadline_to[1].'-'.$deadline_to[0];
            $query->andFilterWhere(['>=', 'DEADLINE', new \yii\db\Expression("to_date('" . $deadline_from_formatted . "','{$this->dateFormat}')")])
                    ->andFilterWhere(['<=', 'DEADLINE', new \yii\db\Expression("to_date('" . $deadline_to_formatted . "','{$this->dateFormat}')")]);
        } else if($this->deadline_from != '' && $this->deadline_to == '') {
            $deadline_from = explode('-', $this->deadline_from);
            $deadline_from_formatted = $deadline_from[2].'-'.$deadline_from[1].'-'.$deadline_from[0];
            $query->andFilterWhere(['>=', 'DEADLINE', new \yii\db\Expression("to_date('" . $deadline_from_formatted . "','{$this->dateFormat}')")]);
        } else if($this->deadline_from == '' && $this->deadline_to != '') {
            $deadline_to = explode('-', $this->deadline_to);
            $deadline_to_formatted = $deadline_to[2].'-'.$deadline_to[1].'-'.$deadline_to[0];
            $query->andFilterWhere(['<=', 'DEADLINE', new \yii\db\Expression("to_date('" . $deadline_to_formatted . "','{$this->dateFormat}')")]);
        }
        if($this->task_type_date_3_from != '' && $this->task_type_date_3_to != '') {
            $query->joinWith('datetype3'); 
            $task_type_date_3_from = explode('-', $this->task_type_date_3_from);
            $task_type_date_3_from_formatted = $task_type_date_3_from[2].'-'.$task_type_date_3_from[1].'-'.$task_type_date_3_from[0];
            $task_type_date_3_to = explode('-', $this->task_type_date_3_to);
            $task_type_date_3_to_formatted = $task_type_date_3_to[2].'-'.$task_type_date_3_to[1].'-'.$task_type_date_3_to[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_3_from_formatted . "','{$this->dateFormat}')")])
                    ->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_3_to_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_3_from != '' && $this->task_type_date_3_to == '') {
            $query->joinWith('datetype3'); 
            $task_type_date_3_from = explode('-', $this->task_type_date_3_from);
            $task_type_date_3_from_formatted = $task_type_date_3_from[2].'-'.$task_type_date_3_from[1].'-'.$task_type_date_3_from[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_3_from_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_3_from == '' && $this->task_type_date_3_to != '') {
            $query->joinWith('datetype3'); 
            $task_type_date_3_to = explode('-', $this->task_type_date_3_to);
            $task_type_date_3_to_formatted = $task_type_date_3_to[2].'-'.$task_type_date_3_to[1].'-'.$task_type_date_3_to[0];
            $query->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_3_to_formatted . "','{$this->dateFormat}')")]);
        }
        if($this->task_type_date_1_from != '' && $this->task_type_date_1_to != '') {
            $query->joinWith('datetype1'); 
            $task_type_date_1_from = explode('-', $this->task_type_date_1_from);
            $task_type_date_1_from_formatted = $task_type_date_1_from[2].'-'.$task_type_date_1_from[1].'-'.$task_type_date_1_from[0];
            $task_type_date_1_to = explode('-', $this->task_type_date_1_to);
            $task_type_date_1_to_formatted = $task_type_date_1_to[2].'-'.$task_type_date_1_to[1].'-'.$task_type_date_1_to[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_1_from_formatted . "','{$this->dateFormat}')")])
                    ->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_1_to_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_1_from != '' && $this->task_type_date_1_to == '') {
            $query->joinWith('datetype1'); 
            $task_type_date_1_from = explode('-', $this->task_type_date_1_from);
            $task_type_date_1_from_formatted = $task_type_date_1_from[2].'-'.$task_type_date_1_from[1].'-'.$task_type_date_1_from[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_1_from_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_1_from == '' && $this->task_type_date_1_to != '') {
            $query->joinWith('datetype1'); 
            $task_type_date_1_to = explode('-', $this->task_type_date_1_to);
            $task_type_date_1_to_formatted = $task_type_date_1_to[2].'-'.$task_type_date_1_to[1].'-'.$task_type_date_1_to[0];
            $query->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_1_to_formatted . "','{$this->dateFormat}')")]);
        }
        if($this->task_type_date_4_from != '' && $this->task_type_date_4_to != '') {
            $query->joinWith('datetype4'); 
            $task_type_date_4_from = explode('-', $this->task_type_date_4_from);
            $task_type_date_4_from_formatted = $task_type_date_4_from[2].'-'.$task_type_date_4_from[1].'-'.$task_type_date_4_from[0];
            $task_type_date_4_to = explode('-', $this->task_type_date_4_to);
            $task_type_date_4_to_formatted = $task_type_date_4_to[2].'-'.$task_type_date_4_to[1].'-'.$task_type_date_4_to[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_4_from_formatted . "','{$this->dateFormat}')")])
                    ->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_4_to_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_4_from != '' && $this->task_type_date_4_to == '') {
            $query->joinWith('datetype4'); 
            $task_type_date_4_from = explode('-', $this->task_type_date_4_from);
            $task_type_date_4_from_formatted = $task_type_date_4_from[2].'-'.$task_type_date_4_from[1].'-'.$task_type_date_4_from[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_4_from_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_4_from == '' && $this->task_type_date_4_to != '') {
            $query->joinWith('datetype4'); 
            $task_type_date_4_to = explode('-', $this->task_type_date_4_to);
            $task_type_date_4_to_formatted = $task_type_date_4_to[2].'-'.$task_type_date_4_to[1].'-'.$task_type_date_4_to[0];
            $query->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_4_to_formatted . "','{$this->dateFormat}')")]);
        }
        if($this->task_type_date_2_from != '' && $this->task_type_date_2_to != '') {
            $query->joinWith('datetype2'); 
            $task_type_date_2_from = explode('-', $this->task_type_date_2_from);
            $task_type_date_2_from_formatted = $task_type_date_2_from[2].'-'.$task_type_date_2_from[1].'-'.$task_type_date_2_from[0];
            $task_type_date_2_to = explode('-', $this->task_type_date_2_to);
            $task_type_date_2_to_formatted = $task_type_date_2_to[2].'-'.$task_type_date_2_to[1].'-'.$task_type_date_2_to[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_2_from_formatted . "','{$this->dateFormat}')")])
                    ->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_2_to_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_2_from != '' && $this->task_type_date_2_to == '') {
            $query->joinWith('datetype2'); 
            $task_type_date_2_from = explode('-', $this->task_type_date_2_from);
            $task_type_date_2_from_formatted = $task_type_date_2_from[2].'-'.$task_type_date_2_from[1].'-'.$task_type_date_2_from[0];
            $query->andFilterWhere(['>=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_2_from_formatted . "','{$this->dateFormat}')")]);
        } else if($this->task_type_date_2_from == '' && $this->task_type_date_2_to != '') {
            $query->joinWith('datetype2'); 
            $task_type_date_2_to = explode('-', $this->task_type_date_2_to);
            $task_type_date_2_to_formatted = $task_type_date_2_to[2].'-'.$task_type_date_2_to[1].'-'.$task_type_date_2_to[0];
            $query->andFilterWhere(['<=', 'TASK_DATES.TASK_TYPE_DATE', new \yii\db\Expression("to_date('" . $task_type_date_2_to_formatted . "','{$this->dateFormat}')")]);
        }


        $query->andFilterWhere(['like', 'SOURCENUM', $this->SOURCENUM]);
        $query->andFilterWhere(['like', 'TASK_TEXT', $this->TASK_TEXT]);
        $query->andFilterWhere(['or like', 'PEOORDERNUM', $this->PEOORDERNUM]);
        $query->andFilterWhere(['or like', 'ORDERNUM', $this->ORDERNUM]);
        $query->andFilterWhere(['like', 'TASK_NUMBER', $this->TASK_NUMBER]);
        $query->andFilterWhere(['like', 'LOWER(DESIGNATION)', mb_strtolower($this->DESIGNATION, 'UTF-8')]);
        return $dataProvider;
    }
}
