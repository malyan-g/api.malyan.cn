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
            $query = Book::find()
                ->joinWith(['catalog'])
                ->select([Book::tableName() .'.id', 'name', 'author', 'image', 'title'])
                ->where([Book::tableName() .'.show' => Book::IS_SHOW]);

            // 查询总页数
            $pageSize = 6;
            $allPages = (int) ceil($query->count()/$pageSize);

            if($page <= $allPages){
                $data = $query->orderBy([Book::tableName() .'.sort' => SORT_ASC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    //->asArray()
                    ->all();

                return $data;

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
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        $bookId = (int) ArrayHelper::getValue($requestData, 'bookId', 1);

        if($id > 0 && $bookId > 0){
        	// 查询
            $count = BookCatalog::find()
                ->select(['id', 'title'])
                ->where(['show' => BookCatalog::IS_SHOW, 'book_id' => $bookId])
                ->andFilterWhere(['<=', 'id' , $id])
                ->count();

            if($count) {
                $data = BookCatalog::find()
                    ->select(['id', 'title'])
                    ->where(['show' => BookCatalog::IS_SHOW, 'book_id' => $bookId])
                    ->orderBy(['sort' => SORT_ASC])
                    ->asArray()
                    ->all();
                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'number' => $count - 1,
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
    public function actionListBak()
    {
        $requestData = Yii::$app->request->post();
        $page = (int) ArrayHelper::getValue($requestData, 'page', 1);
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        $catalogId = (int) ArrayHelper::getValue($requestData, 'catalogId', 1);
        $sort = (int) ArrayHelper::getValue($requestData, 'sort', 0);

        if($page > 0 && $id > 0 && $catalogId > 0){
            // 查询
            $query = BookCatalog::find()
                ->select(['id', 'title'])
                ->where(['show' => BookCatalog::IS_SHOW, 'book_id' => $id])
                ->andFilterWhere([$sort ? '>=' : '<', 'id', $catalogId]);

            // 查询总页数
            $pageSize = 20;
            $allPages = (int) ceil($query->count()/$pageSize);

            if($page <= $allPages){
                $data = $query->orderBy(['sort' => $sort ? SORT_ASC : SORT_DESC])
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->asArray()
                    ->all();

                $this->data = [
                    'code' => self::API_CODE_SUCCESS,
                    'msg' => self::API_CODE_SUCCESS_MSG,
                    'allPages' => $allPages,
                    'data' => $sort ? $data : array_reverse($data)
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
        $first = (int) ArrayHelper::getValue($requestData, 'first', 0);
        $id = (int) ArrayHelper::getValue($requestData, 'id', 1);
        if($id > 0){
            $queryData = $first ? ['book_id' => $id, 'sort' => 1] : ['id' => $id];

            $catalogData = BookCatalog::find()
                ->select(['id', 'book_id', 'title'])
                ->where($queryData)
                ->andWhere(['show' => BookCatalog::IS_SHOW])
                ->asArray()
                ->one();

            if($catalogData){
                // 查询
                $data = BookDetail::find()
                    ->select(['id', 'content'])
                    ->where(['catalog_id' => $catalogData['id']])
                    ->asArray()
                    ->one();

                if($data){
                    $prevData = BookCatalog::find()
                        ->select(['id'])
                        ->where(['book_id' => $catalogData['book_id'], 'show' =>BookCatalog::IS_SHOW])
                        ->andFilterWhere([ '<', 'id', $data['id']])
                        ->orderBy(['sort' => SORT_DESC])
                        ->asArray()
                        ->one();

                    $nextData = BookCatalog::find()
                        ->select(['id'])
                        ->where(['book_id' => $catalogData['book_id'], 'show' =>BookCatalog::IS_SHOW])
                        ->andFilterWhere(['>', 'id', $data['id']])
                        ->orderBy(['sort' => SORT_ASC])
                        ->asArray()
                        ->one();

                    $data['prevPage'] = $prevData ? $prevData['id'] : null;
                    $data['nextPage'] = $nextData ? $nextData['id'] : null;
                    $data['title'] = $catalogData['title'];

                    $this->data = [
                        'code' => self::API_CODE_SUCCESS,
                        'msg' => self::API_CODE_SUCCESS_MSG,
                        'data' => $data
                    ];
                }
            }

        }

        return $this->data;
    }

}
