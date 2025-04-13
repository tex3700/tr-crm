<?php

namespace backend\controllers;

use common\models\Translator;
use yii\web\{Controller, Response};

class TranslatorsController extends Controller
{
    public function actionApi(): Response
    {
        $isWeekend = (date('N') >= 6);
        $dayType = $isWeekend ? 'weekend' : 'weekday';

        $translators = Translator::find()
            ->joinWith('schedules')
            ->where([
                'translator_schedules.day_type' => $dayType,
                'translator_schedules.is_available' => 1
            ])
            ->asArray()
            ->all();

        return $this->asJson($translators);
    }
}