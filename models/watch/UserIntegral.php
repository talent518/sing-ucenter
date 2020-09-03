<?php

namespace app\models\watch;

use Yii;

/**
 * This is the model class for table "user_integral".
 *
 * @property int $user_id 用户ID
 * @property int $stars 星星数
 * @property int $iphone_stars iPhone星星数
 * @property int $ipad_stars iPad星星数
 * @property int $android_stars Android星星数
 * @property int $h5_stars H5星星数(微信公众号)
 * @property int $mini_stars 微信小程序星星数
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserIntegral extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_integral';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'stars', 'iphone_stars', 'ipad_stars', 'android_stars', 'h5_stars', 'mini_stars'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'stars' => '星星数',
            'iphone_stars' => 'iPhone星星数',
            'ipad_stars' => 'iPad星星数',
            'android_stars' => 'Android星星数',
            'h5_stars' => 'H5星星数(微信公众号)',
            'mini_stars' => '微信小程序星星数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
