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
	public function actionList()
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
                $bookData = $query->orderBy(['sort' => SORT_ASC,  'created_at' => SORT_ASC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $this->data = [
		            'code' => self::API_CODE_SUCCESS,
		            'msg' => self::API_CODE_SUCCESS_MSG,
		            'data' => [
		                'allPages' => $allPages,
		                'bookData' => $bookData
		            ]
		        ];
            }
        }


        return $this->data;
    }

	/**
     * 章节列表接口
     * @return array
     */
    public function actionCatalogList()
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
                $catalogData = $query->orderBy(['sort' => SORT_ASC,  'created_at' => SORT_ASC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $this->data = [
		            'code' => self::API_CODE_SUCCESS,
		            'msg' => self::API_CODE_SUCCESS_MSG,
		            'data' => [
		                'allPages' => $allPages,
		                'catalogData' => $catalogData
		            ]
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
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        if($id > 0){
        	// 查询
            $data = BookDetail::find()->select(['id', 'content'])->where(['catalog_id' => $id])->asArray()->one();

            $this->data = [
	            'code' => self::API_CODE_SUCCESS,
	            'msg' => self::API_CODE_SUCCESS_MSG,
	            'data' => $data
	        ];
        }

        return $this->data;
    }

}