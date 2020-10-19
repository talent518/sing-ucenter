<?php
namespace app\modules\open\controllers;

use app\constants\ErrInfo;
use app\core\CCException;
use app\core\CCOpenController;
use app\core\CCResponse;
use app\enums\BooleanEnum;
use app\models\account\PeriodsClassUsers;
use app\models\account\Teachers;
use app\models\account\Users;
use app\models\account\UsersOpen;
use app\models\classes\PeriodsClass;use app\models\order\CodeRule;
use app\models\order\UserOrder;
use yii\helpers\ArrayHelper;

class UserController extends CCOpenController {

    public function actionIndex(){
        $data = [];
        try{
            $scene = \Yii::$app->request->get('scene', '');
            switch ($scene){
                case 'get-userinfo':
                    $useridStr = \Yii::$app->request->get('userids', null);
                    if(empty($useridStr)) throw new CCException(ErrInfo::INVALID_PARAMS);
                    $userIds = explode(',', $useridStr);
                    if(count($userIds) > 200) throw new CCException(['code'=>999, 'message'=>'最多一次请求200条数据']);
                    $data = $this->getUserInfos($userIds);

                    return new CCResponse($data);
                    break;
                case 'openid-to-userid':
                    $platform = \Yii::$app->request->get('platform', null);
                    $openIds = explode(',', \Yii::$app->request->get('openids', ''));
                    if(empty($openIds) || is_null($platform)) throw new CCException(ErrInfo::INVALID_PARAMS);
                    if(count($openIds) > 200) throw new CCException(['code'=>999, 'message'=>'最多一次请求200条数据']);
                    $userOpens = UsersOpen::find()->where(["and",
                        ['=', 'platform', $platform],
                        ['in', 'open_id', $openIds]
                    ])->all();
                    foreach ($userOpens as $userOpen) {
                        $data[] = ['openid'=>$userOpen['open_id'], 'user_id'=>$userOpen['user_id']];
                    }
                    return new CCResponse($data);
                    break;
                default:
                    throw new CCException(ErrInfo::INVALID_PARAMS);

            }
        }catch (\Exception $ex){
            throw new CCException(['code'=>$ex->getCode(), 'message'=>$ex->getMessage()]);
        }
    }

    private function getUserInfos($userIds){
        $data = [];
        $users = Users::find()->where(['and',
            ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
            ['in', 'user_id', $userIds]
        ])->all();
        if(empty($users)) throw new CCException(ErrInfo::REQUEST_RESOURCE_NOT_FOUND);
        foreach ($users as $user) {
            $item = ['user_id'=>$user->user_id, 'user'=>[], 'teacher'=>[], 'order'=>[]];
            //用户信息:
            $item['user'] = ['id'=>$user->user_id, 'nickname'=>$user['nickname']];

            //老师信息：
            $latestPeriodsId = PeriodsClassUsers::find()->select('periods_id')->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'user_id', $user->user_id]])->orderBy(['periods_id'=>SORT_DESC])->limit(1)->scalar();


            $periodsClassUsers = PeriodsClassUsers::find()->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'user_id', $user->user_id],
                ['=', 'periods_id', $latestPeriodsId]])->all();


            $periodClasses = PeriodsClass::find()->where(['and',
                ['=', 'periods_id', $latestPeriodsId],
                ['in', 'id', ArrayHelper::getColumn($periodsClassUsers, 'class_id')]
            ])->all();

            $teachers = Teachers::find()->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'class_status', 0],
                ['=', 'status', 0],
                ['in', 'id', ArrayHelper::getColumn($periodClasses, 'teacher_id')]
            ])->all();
            foreach ($teachers as $teacher) {
                $item['teacher'][] = ['name'=>$teacher['name'], 'wechat'=>$teacher['alias']];
            }

            //订单信息：
            $latestOrder = UserOrder::find()->where(['and',
                ['=', 'user_id', $user->user_id]
            ])->orderBy(['id'=>SORT_DESC])->limit(1)->one();
            if(!empty($codeRule))   {
                $codeRule = CodeRule::findOne(['code'=>$latestOrder['invite_type']]);
                $item['order']['code_rule_title'] = $codeRule['title']??'';
            }
            $data[] = $item;
        }
        return $data;
    }

    public function actionView($id){
        try{
            $data = ['user'=>['nickname'=>''], 'teacher'=>[], 'order'=>['code_rule_title'=>'']];
            $user = Users::find()->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'user_id', $id]
                ])->limit(1)->one();
            if(empty($user)) throw new CCException(ErrInfo::REQUEST_RESOURCE_NOT_FOUND);

            //用户信息:
            $data['user']['nickname'] = $user['nickname'];

            //老师信息：
            $latestPeriodsId = PeriodsClassUsers::find()->select('periods_id')->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'user_id', $id]])->orderBy(['periods_id'=>SORT_DESC])->limit(1)->scalar();


            $periodsClassUsers = PeriodsClassUsers::find()->where(['and',
            ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
            ['=', 'user_id', $id],
            ['=', 'periods_id', $latestPeriodsId]])->all();


            $periodClasses = PeriodsClass::find()->where(['and',
                ['=', 'periods_id', $latestPeriodsId],
                ['in', 'id', ArrayHelper::getColumn($periodsClassUsers, 'class_id')]
            ])->all();

            $teachers = Teachers::find()->where(['and',
                ['=', 'is_del', BooleanEnum::IS_DEL_FALSE],
                ['=', 'class_status', 0],
                ['=', 'status', 0],
                ['in', 'id', ArrayHelper::getColumn($periodClasses, 'teacher_id')]
                ])->all();
            foreach ($teachers as $teacher) {
                $data['teacher'][] = ['name'=>$teacher['name'], 'wechat'=>$teacher['alias']];
            }

            //订单信息：
            $latestOrder = UserOrder::find()->where(['and',
                ['=', 'user_id', $id]
            ])->orderBy(['id'=>SORT_DESC])->limit(1)->one();
            if(!empty($codeRule))   {
                $codeRule = CodeRule::findOne(['code'=>$latestOrder['invite_type']]);
                $data['order']['code_rule_title'] = $codeRule['title']??'';
            }


            return new CCResponse($data);
        }catch (\Exception $ex){
            throw new CCException(['code'=>$ex->getCode(), 'message'=>$ex->getMessage()]);

        }
    }
}
