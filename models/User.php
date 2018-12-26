<?php

namespace app\models;

use Yii;
use PhpOffice\PhpWord\TemplateProcessor;
use app\components\helpers\QiniuApiHelper;

/**
 * This is the model class for table "cgt_user".
 *
 * @property integer $id
 * @property string $realname
 * @property integer $mobile
 * @property string $idcard
 * @property string $openid
 * @property integer $is_member
 * @property integer $member_id
 * @property integer $member_time
 * @property integer $created_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cgt_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile','is_member', 'member_id', 'member_time', 'created_at'], 'integer'],
            [['realname', 'idcard'], 'string', 'max' => 20],
            [['openid'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => '真实姓名',
            'mobile' => '手机号',
            'idcard' => '身份证',
            'openid' => '微信ID',
            'is_member' => '会员',
            'member_id' => '会员等级',
            'member_time' => '会员开通时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取用户
     * @param $openid
     * @return array|bool
     */
    public static function getUserInfo($openid)
    {
        $user = self::findOne(['openid' => $openid]);
        if(!$user){
            $user = new self();
            $user->openid = $openid;
            $user->created_at = time();
            if(!$user->save()){
                self::createCertificate($user->id);
                return false;
            }
        }
        $userInfo = [
            'id' =>   $user->id,
            'is_member' => $user->is_member,
            'member_id' => $user->member_id,
            'openid' => $openid
        ];

        return $userInfo;
    }

    /**
     * 生成证书
     * @param $id
     * @return bool
     * @throws \Exception
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public static function createCertificate($id)
    {
        // word路径
        $path = Yii::getAlias('@webroot') . '/files/';
        // 替换模板中的变量并保存
        $wordTemplate = $path . 'certificate.docx';
        $templateProcessor = new TemplateProcessor($wordTemplate);
        $templateProcessor->setValue('certificate_number', 20181225001);
        $templateProcessor->setValue('name', '马亮');
        $templateProcessor->setValue('member_name', '金卡');
        $templateProcessor->setValue('id_card', 612727199111050057);
        $templateProcessor->setValue('issue_date', 20181225);
        $templateProcessor->setValue('valid_date', 20191225);
        $wordName = $path . 'certificate-' . $id . '.docx';
        $templateProcessor->saveAs($wordName);
        // 上传七牛
        QiniuApiHelper::upload($wordName);
        // 删除本地文件
        unlink($wordName);
        return true;
    }
}
