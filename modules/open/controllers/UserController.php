<?php
namespace app\modules\open\controllers;

use app\constants\ErrInfo;
use app\core\CCException;
use app\core\CCOpenController;
use app\core\CCResponse;

class UserController extends CCOpenController {

    public function actionIndex(){
        $data = [];
        try{
            $scene = \Yii::$app->request->get('scene', '');
            switch ($scene){
                case 'openid-to-userid':

                    $openIds = explode(',', \Yii::$app->request->post('openids', ''));
                    if(empty($openIds)) throw new CCException(ErrInfo::INVALID_PARAMS);

                    return new CCResponse($data);
                    break;
                default:
                    throw new CCException(ErrInfo::INVALID_PARAMS);

            }
        }catch (\Exception $ex){
            return new CCException(['code'=>$ex->getCode(), 'message'=>$ex->getMessage()]);
        }


    }
}
