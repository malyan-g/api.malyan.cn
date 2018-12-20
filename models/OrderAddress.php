<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgt_order_address".
 *
 * @property integer $order_id
 * @property string $userName
 * @property integer $telNumber
 * @property string $provinceName
 * @property string $cityName
 * @property string $countyName
 * @property string $detailInfo
 * @property string $postalCode
 */
class OrderAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_order_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userName', 'telNumber', 'provinceName', 'cityName', 'countyName', 'detailInfo', 'postalCode'], 'required'],
            [['telNumber'], 'integer'],
            [['telNumber'], 'match', 'pattern'=>'/^((13\d)|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18\d)|166|198|199|(147))\d{8}$/'],
            [['userName'], 'string', 'max' => 30],
            [['provinceName', 'cityName', 'countyName'], 'string', 'max' => 20],
            [['detailInfo'], 'string', 'max' => 200],
            [['userName', 'provinceName', 'cityName', 'countyName'], 'match', 'pattern'=>'/^[\x{4e00}-\x{9fa5}]+$/u'],
            [['postalCode'], 'string', 'max' => 10],
            [['postalCode'], 'match', 'pattern'=>'/^\d{6}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'userName' => '姓名',
            'telNumber' => '手机号',
            'provinceName' => '省',
            'cityName' => '市',
            'countyName' => '区县',
            'detailInfo' => '详细地址',
            'postalCode' => '邮编',
        ];
    }
}
