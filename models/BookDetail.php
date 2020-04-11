<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%book_detail}}".
 *
 * @property integer $id
 * @property integer $catalog_id
 * @property string $content
 */
class BookDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%book_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catalog_id', 'content'], 'required'],
            [['catalog_id'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catalog_id' => '目录ID',
            'content' => '内容',
        ];
    }
}
