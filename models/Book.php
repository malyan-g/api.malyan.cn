<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%book}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $author
 * @property string $image
 * @property integer $show
 * @property integer $sort
 * @property integer $created_at
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * 不展示
     */
    const NOT_SHOW = 0;

    /**
     * 展示
     */
    const IS_SHOW = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%book}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show', 'sort', 'created_at'], 'integer'],
            [['sort', 'created_at'], 'required'],
            [['name', 'image'], 'string', 'max' => 64],
            [[ 'author'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '书籍名称',
            'author' => '作者',
            'image' => '图片',
            'show' => '是否显示',
            'sort' => '排序',
            'created_at' => '创建时间',
        ];
    }
}
