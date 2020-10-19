<?php

namespace app\models\order;

use Yii;

/**
 * This is the model class for table "code_rule".
 *
 * @property int $id
 * @property int|null $type_id 类别
 * @property string $type_id_extra 来源码扩展字段
 * @property string $title 标题
 * @property string $code USER 或 TEACHER
 * @property string|null $no_cash_user 不返现用户 1,2,3
 * @property int $is_valid 是否停用 0：启用  1：停用
 * @property int|null $is_del 是否删除
 * @property int|null $is_withdraw 0-不可提现 1-可提现
 * @property int $is_conflict 是否冲突渠道0：否，1：是
 * @property int $cur_level 当前渠道等级
 * @property string $desc 备注
 * @property string $created_at
 * @property string $updated_at
 */
class CodeRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'code_rule';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_pub');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'is_valid', 'is_del', 'is_withdraw', 'is_conflict', 'cur_level'], 'integer'],
            [['code'], 'required'],
            [['no_cash_user'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type_id_extra', 'desc'], 'string', 'max' => 255],
            [['title', 'code'], 'string', 'max' => 40],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'type_id_extra' => 'Type Id Extra',
            'title' => 'Title',
            'code' => 'Code',
            'no_cash_user' => 'No Cash User',
            'is_valid' => 'Is Valid',
            'is_del' => 'Is Del',
            'is_withdraw' => 'Is Withdraw',
            'is_conflict' => 'Is Conflict',
            'cur_level' => 'Cur Level',
            'desc' => 'Desc',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
