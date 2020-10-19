<?php

namespace app\models\account;

use Yii;

/**
 * This is the model class for table "users_open".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $open_id
 * @property int $platform 平台ID：0、公众号（唱唱启蒙）
 * @property string $created_at
 * @property string $updated_at
 */
class UsersOpen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_open';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_user');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'platform'], 'integer'],
            [['open_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['open_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'open_id' => 'Open ID',
            'platform' => 'Platform',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
