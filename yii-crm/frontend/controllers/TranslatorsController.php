<?php

namespace frontend\controllers;

use common\models\Translator;
use yii\web\Controller;

class TranslatorsController extends Controller
{
    public function actionIndex()
    {
        $translators = \Yii::$app->cache->getOrSet('availableTranslators', function() {
            return Translator::getAvailableTranslators();
        }, 300);

        return $this->render('index', [
            'translators' => $translators
        ]);
//        $this->layout = 'vue';
//        return $this->render('index');
    }
}