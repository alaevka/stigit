<?php
/*
    Класс прав доступа и настройки состояний
*/
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class PermissionsController extends Controller
{

    /*
        Метод формирования доступа к страницам
    */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['logout', 'index'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }



    /*
        Метод формирования страницы "Права доступа"
    */
    public function actionIndex()
    {
        $permissions_for_change_permissions = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action) or 
            (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'dolg_id' =>  \Yii::$app->session->get('user.user_iddolg'), 'action' => \app\controllers\PermissionsController::_getPermissionsArray('permission_management'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2])->one();
        /*
            Проверка на доступ пользователя к странице
        */
        if($permissions_for_change_permissions) {
            /*
                Формирование списка должностей
            */
            $query = new \yii\db\Query;
            $query->select('NAIMDOLG AS name, IDDOLG AS id')
                    ->from('STIGIT.V_F_SHRAS')
                    //->limit(20)
                    ->orderBy('NAIMDOLG asc');
            $command = $query->createCommand();
            $v_f_shras = $command->queryAll();
            
            /*
                Формирование списка пользователей
            */
            $query = new \yii\db\Query;
            $query->select('TN AS tn, FIO AS fio')
                    ->from('STIGIT.V_F_PERS')
                    //->limit(20)
                    ->orderBy('FIO asc');
            $command = $query->createCommand();
            $v_f_pers = $command->queryAll(); 

            /*
                Формирование списка доступных действий
            */
            $actions = \app\models\Actions::find()->orderBy('ACTION_DESC asc')->all();

            /*
                Формирование списка состояний
            */
            $states_list = \app\models\States::find()->all();

            /*
                Рендер страницы и передача сформированных переменных в нее
            */
            return $this->render('index', [
                'v_f_shras' => $v_f_shras,
                'v_f_pers' => $v_f_pers,
                'actions' => $actions,
                'states_list' => $states_list,
            ]);
        } else {
            /*
                Вызываем эксепшн в случае, если доступ к странце запрещен
            */
            throw new \yii\web\ForbiddenHttpException('У Вас нет доступа на "Права доступа".'); 
        }
    }

    /*
        Метод формирования страницы смены состояний
    */
    public function actionStates()
    {
        $permissions_for_states_change = \app\models\Permissions::find()->where('SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action', ['action' => \app\controllers\PermissionsController::_getPermissionsArray('states_sequence'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
        /*
            Проверка на доступ пользователя к странице
        */
        if($permissions_for_states_change) {
            /*
                Формирование списка состояний
            */
            $states_list = \app\models\States::find()->all();
            /*
                Рендер страницы и передача сформированных переменных в нее
            */
            return $this->render('states', [
                'states_list' => $states_list,
                'perm_level' => $permissions_for_states_change->PERM_LEVEL
            ]);
        } else {
            /*
                Вызываем эксепшн в случае, если доступ к странце запрещен
            */
            throw new \yii\web\ForbiddenHttpException('У Вас нет доступа на "Последовательность смены состояний".'); 
        }
    }

    public function _checkPermissions($action, $task_id) {

        switch($action) {
            case 'open_issue_modal':
            //проверка прав на просмотр задания (в модальном окне)
            $permissions_for_open_issue_modal = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and PERM_TYPE = :perm_type)  or 
                                                                                        (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and PERM_TYPE = :perm_type)', 
                                                                                        ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'perm_type' => 1, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0, 'action' => \app\controllers\PermissionsController::_getPermissionsArray('task_form')])->one();
            if($permissions_for_open_issue_modal) {
                //в случае если есть доступ на просмотр данных о задании
                $user_in_persons_or_podr_list = false;

                //получаем массив подразделений задания
                $podr_tasks = \app\models\PodrTasks::find()->where(['TASK_ID' => $task_id, 'DEL_TRACT_ID' => 0])->all();
                if($podr_tasks) { // если существуют подразделения, то формируем их список
                    $podr_list_kodzifr_array = [];
                    foreach($podr_tasks as $task) { //обходим список подразделений
                        $query = new \yii\db\Query;
                        $query->select('*')
                            ->from('STIGIT.V_F_PODR')
                            ->where('KODZIFR = \'' . trim($task->KODZIFR) .'\'');
                        $command = $query->createCommand();
                        $data = $command->queryOne();
                        if(isset($data['KODZIFR'])) { // проверяем на существование названия подразделения (на момент разработки не для всех были названия)
                            $podr_list_kodzifr_array[] = $data['KODZIFR'];
                        }
                    }
                    
                }

                //проверяем входит ли данный пользователь в список исполнителей задания
                //получаем список пользователей, кому назначено задание
                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' => $task_id, 'DEL_TRACT_ID' => 0])->all();
                if($pers_tasks) {  // если существуют исполнители, то формируем их список
                    $persons_array = [];
                    foreach($pers_tasks as $task) {
                        $query = new \yii\db\Query;
                        $query->select('*')
                            ->from('STIGIT.V_F_PERS')
                            ->where('TN = \'' . $task->TN .'\'');
                        $command = $query->createCommand();
                        $data = $command->queryOne();
                        $persons_array[] = $data['TN'];
                    }
                    //проверяем, входит ли пользователь в этот список
                    if(in_array(\Yii::$app->user->id, $persons_array)) {
                        //пользователь входит в список исполнителей
                        $user_in_persons_or_podr_list = true;
                    }
                } else {
                    //в этом случае, задания назначено всем исполнителям из списка подразделений задания
                    $persons_array = [];
                    //обходим массив подразделений для проверки, входит ли в него текущий пользователь
                    $ids = join(',', $podr_list_kodzifr_array); 
                    $query = new \yii\db\Query;
                    $query->select('*')
                        ->from('STIGIT.V_F_PERS')
                        ->where('TN = \'' . \Yii::$app->user->id .'\' and KODZIFR in ('.$ids.')');
                    $command = $query->createCommand();
                    $data = $command->queryAll();
                    if(!empty($data)) { // проверяем вхождение пользователя в список исполнителей подразделений задания
                        $user_in_persons_or_podr_list = true; // текущий пользователь входит в подразделения, указанные в задании
                    }
                }

                //проверяем, является ли пользователь начальником подразделений, указанных в задании
                //получаем список подчиненных подразделений
                $user_boss_of = \Yii::$app->session->get('user.user_boss_of');
                if(!empty($user_boss_of) && !$user_in_persons_or_podr_list) {


                    //проверяем, является ли пользователь начальником подразделений, указанных в задании
                    if(in_array($user_boss_of, $podr_list_kodzifr_array)) {

                        //пользователь является руководителем, но не входит в спиок исполнителей или указанных подразделений
                        //осуществляем проверку прав на 'podr_tasks_my_edit'
                        $permissions_for_open_issue_modal_for_boss = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and PERM_TYPE = :perm_type)  or 
                                                                                            (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and PERM_TYPE = :perm_type)', 
                                                                                            ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'perm_type' => 1, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0, 'action' => \app\controllers\PermissionsController::_getPermissionsArray('podr_tasks_my_edit')])->one();
                        if($permissions_for_open_issue_modal_for_boss) {
                            //пользователю разрешен просмотр и редактирование заданий своего подразделения
                            $user_in_persons_or_podr_list = true;
                        }
                    }
                }


                if($user_in_persons_or_podr_list) {
                    //пользователь имеет доступ к заданию, так как входит в список исполнителей выбранного задания, либо находится в составе подразделей задания,
                    //либо является руководителем подразделений, указанных в задании
                    return true;
                }
            } else {
                return false;
            }

            break;
            case 'open_issue_modal_in_current_status':
                //проверка прав на просмотр задания (в модальном окне) в текущем статусе задания
                //получаем текущий статус задания для данного пользователя
                $current_status = self::_getCurrentTaskStatusForCurrentUser($task_id);
                if($current_status != 'empty_status' && $current_status != 'user_not_in_persons_list') {
                    //проверяем есть ли доступ у пользователя к заданию в текущем статусе
                    $permissions_for_open_issue_modal_in_current_status = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action and PERM_TYPE = :perm_type) or
                                                                                            (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :id_dolg and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action and PERM_TYPE = :perm_type)', 
                                                                                            ['subject_type_dolg' => 1, 'id_dolg' =>  \Yii::$app->session->get('user.user_iddolg'), 'perm_type' => 2, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0, 'action' => $current_status])->one();
                    
                    if($permissions_for_open_issue_modal_in_current_status) {
                        //досутп на просмотр в текущем статусе разрешен
                        return 'true';
                    } else {
                        $state = \app\models\States::findOne($current_status);
                        return 'У Вас нет прав на "Форма свойств задания" в статусе "'.$state->STATE_NAME.'"';
                    }
                } elseif ($current_status == 'empty_status') {
                    //устанавливаем статус "Принято при первом открытии задания"
                    $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$task_id, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                    $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
                    //пишем первый статус в БД
                    $task_states = new \app\models\TaskStates;
                    $task_states->TASK_ID = $task_id;
                    $task_states->STATE_ID = 1;
                    $task_states->TRACT_ID = $transactions->ID;
                    $task_states->IS_CURRENT = 1;
                    if($pers_tasks->ID) { // устанавливаем pers_tasks id
                        $task_states->PERS_TASKS_ID = $pers_tasks->ID;
                    }
                    $task_states->save();
                    //разрешаем пользователю смотреть информацию по заданию
                    return 'true';
                } elseif($current_status == 'user_not_in_persons_list') {
                    //пользователь не стоит в списке исполнителей задания
                    //проверяем, если он руководитель показываем информацию по заданию (остальные пользователи сбда не попадут из-за проверки выше)

                    return 'true_for_boss';
                }
            break;
            case 'update_issue':
                //проверяем, есть ли доступ на редактирование задания для исполнителей
                $permissions_for_update_issue = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and PERM_TYPE = :perm_type)  or 
                                                                                        (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and PERM_TYPE = :perm_type)', 
                                                                                        ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'perm_type' => 1, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2, 'action' => \app\controllers\PermissionsController::_getPermissionsArray('task_form')])->one();    
                
                //проверка доступа на редактирование для руководителей
                $permissions_for_update_issue_boss = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and PERM_TYPE = :perm_type)  or 
                                                                                        (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and ACTION_ID = :action and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and PERM_TYPE = :perm_type)', 
                                                                                        ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'perm_type' => 1, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2, 'action' => \app\controllers\PermissionsController::_getPermissionsArray('podr_tasks_my_edit')])->one();    

                //проверяем кто редактирует задание
                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' => $task_id, 'DEL_TRACT_ID' => 0])->all();
                if($pers_tasks) {  // если существуют исполнители, то формируем их список
                    $persons_array = [];
                    foreach($pers_tasks as $task) {
                        $query = new \yii\db\Query;
                        $query->select('*')
                            ->from('STIGIT.V_F_PERS')
                            ->where('TN = \'' . $task->TN .'\'');
                        $command = $query->createCommand();
                        $data = $command->queryOne();
                        $persons_array[] = $data['TN'];
                    }
                    //проверяем, входит ли пользователь в этот список
                    if(in_array(\Yii::$app->user->id, $persons_array)) {
                        //пользователь входит в список исполнителей
                        $user_is_person = 1;
                    } else {
                        $user_is_person = 0;
                    }
                }

                if($permissions_for_update_issue && $user_is_person == 1) {
                    return 'update_issue_for_person';
                } elseif($permissions_for_update_issue_boss && $user_is_person == 0) {
                    return 'update_issue_for_boss';
                } else {
                    return false;
                }


            break;
        }
    }

    public function _getCurrentTaskStatusForCurrentUser($task_id) {
        //получаем список исполнителей данного задания
        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $task_id, 'DEL_TRACT_ID' => 0, 'TN' => \Yii::$app->user->id])->one();
        if($persons) {
            //получаем статус для данного исполнителя
            $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $persons->ID, 'TASK_ID' => $task_id])->one();
            if($task_state) {
                return $task_state->STATE_ID;
            } else {
                //нет ни одного статуса для данного пользователя
                return 'empty_status';
            }
        } else {
            //пользователь не стоит в списке исполнителей задания
            return 'user_not_in_persons_list';
        }

    }


    public function _getPermissionsArray($rule) {
        $permissions_array = \Yii::$app->session->get('user.permissions_array');
        return $permissions_array[$rule];
    }
}