<?php

namespace app\controllers;

use YII;
use app\models\Book;
use app\models\BookCatalog;
use app\models\BookDetail;
use yii\helpers\ArrayHelper;

/**
 * 书籍接口
 * Class IndexController
 * @package app\controllers
 */
class BookController extends Controller
{
	 /**
     * 书籍列表接口
     * @return array
     */
	public function actionIndex()
    {
    	$requestData = Yii::$app->request->post();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        if($page > 0){
        	// 查询
            $query = Book::find()->select(['id', 'name'])->where(['show' => Book::IS_SHOW]);

            // 查询总页数
            $pageSize = 6;
            $allPages = (int) ceil($query->count()/6);

            if($page <= $allPages){
                $data = $query->orderBy(['sort' => SORT_ASC,  'created_at' => SORT_ASC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $this->data = [
		            'code' => self::API_CODE_SUCCESS,
		            'msg' => self::API_CODE_SUCCESS_MSG,
                    'allPages' => $allPages,
                    'data' => $data
		        ];
            }
        }


        return $this->data;
    }

	/**
     * 章节列表接口
     * @return array
     */
    public function actionList()
    {
        $requestData = Yii::$app->request->post();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        if($page > 0 && $id > 0){
        	// 查询
            $query = BookCatalog::find()->select(['id', 'title'])->where(['show' => BookCatalog::IS_SHOW, 'book_id' => $id]);

            // 查询总页数
            $pageSize = 20;
            $allPages = (int) ceil($query->count()/6);

            if($page <= $allPages){
                $data = $query->orderBy(['sort' => SORT_ASC,  'created_at' => SORT_ASC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $this->data = [
		            'code' => self::API_CODE_SUCCESS,
		            'msg' => self::API_CODE_SUCCESS_MSG,
                    'allPages' => $allPages,
                    'data' => $data
		        ];
            }
        }


        return $this->data;
    }

    /**
     * 章节详情接口
     * @return array
     */
    public function actionDetail()
    {
        $requestData = Yii::$app->request->post();
        $fisrt = ArrayHelper::getValue($requestData, 'fisrt', false);
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        if($id > 0){
            if($fisrt === true){
                $id = BookCatalog::find()->select('id')->where(['id' => $id])->column();

                return $id;
            }

            // 查询
            $data = BookDetail::find()->select(['id', 'content'])->where(['catalog_id' => $id])->asArray()->one();
            $nextData = BookCatalog::find()->select(['id'])->where(['catalog_id', '>=', $id])->asArray()->one();
            $lastData = BookCatalog::find()->select(['id'])->where(['catalog_id', '<=', $id])->asArray()->one();

            $data['nextPage'] = $nextData['id'];
            $data['lastPage'] = $lastData['id'];

            $this->data = [
	            'code' => self::API_CODE_SUCCESS,
	            'msg' => self::API_CODE_SUCCESS_MSG,
	            'data' => $data
	        ];
        }

        return $this->data;
    }

}
