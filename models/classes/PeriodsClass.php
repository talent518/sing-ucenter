<?php

namespace app\models\classes;

use Yii;

/**
 * This is the model class for table "periods_class".
 *
 * @property int $id
 * @property string $class_name 班级名字
 * @property int $periods_id 期数管理
 * @property int $teacher_id 老师ID
 * @property int $join_num 班级人数
 * @property int $invalid_num 无效人数
 * @property int $test_num 测试量人数
 * @property int $max_join_num 最大班级人数
 * @property int $type 班级类型：1、带班班级，2、观摩班级
 * @property int $is_system 是否系统自动创建(0：否，1：是）
 * @property int $source 带班方案（旧：活动基础表ID（activity_base_id.id），新：渠道活动关系表ID（code_activity_relation.id））
 * @property string|null $qr 群二维码
 * @property string $media_id 素材ID
 * @property int $is_del 是否删除
 * @property string $created_at
 * @property string $updated_at
 */
class PeriodsClass extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'periods_class';
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
            [['periods_id', 'teacher_id', 'join_num', 'invalid_num', 'test_num', 'max_join_num', 'type', 'is_system', 'source', 'is_del'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['class_name'], 'string', 'max' => 60],
            [['qr'], 'string', 'max' => 255],
            [['media_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_name' => 'Class Name',
            'periods_id' => 'Periods ID',
            'teacher_id' => 'Teacher ID',
            'join_num' => 'Join Num',
            'invalid_num' => 'Invalid Num',
            'test_num' => 'Test Num',
            'max_join_num' => 'Max Join Num',
            'type' => 'Type',
            'is_system' => 'Is System',
            'source' => 'Source',
            'qr' => 'Qr',
            'media_id' => 'Media ID',
            'is_del' => 'Is Del',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
