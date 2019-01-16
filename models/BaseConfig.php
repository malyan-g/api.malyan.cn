<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%base_config}}".
 *
 * @property integer $id
 * @property string $index_banner
 * @property string $index_content
 * @property string $index_footer
 * @property string $agent_image
 */
class BaseConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%base_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['index_banner'], 'required'],
            [['index_banner'], 'string'],
            [['index_content', 'index_footer'], 'string', 'max' => 500],
            [['agent_image'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'index_banner' => '首页轮播图',
            'index_content' => '首页产品',
            'index_footer' => '首页合作伙伴',
            'agent_image' => '邀请代理商背景图',
        ];
    }
}
