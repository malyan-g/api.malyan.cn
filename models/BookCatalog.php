<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%book_catalog}}".
 *
 * @property integer $id
 * @property integer $book_id
 * @property string $title
 * @property integer $show
 * @property integer $sort
 * @property integer $created_at
 */
class BookCatalog extends \yii\db\ActiveRecord
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
        return '{{%book_catalog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_id', 'sort', 'created_at'], 'required'],
            [['book_id', 'show', 'sort', 'created_at'], 'integer'],
            [['title'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => '书籍ID',
            'title' => '目录名称',
            'show' => '是否显示',
            'sort' => '排序',
            'created_at' => '创建时间',
        ];
    }
}
