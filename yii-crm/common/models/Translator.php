<?php

namespace common\models;

use yii\db\{ActiveRecord, ActiveQuery};
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;

class Translator extends ActiveRecord
{
    private string $password;

    public static function tableName(): string
    {
        return 'translators';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord || $this->isAttributeChanged('password')) {
                $this->password = \Yii::$app->security->generatePasswordHash($this->password);
            }
            return true;
        }
        return false;
    }

    public function validatePassword($password): bool
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    // Связь с расписанием
    public function getSchedules(): ActiveQuery
    {
        return $this->hasMany(TranslatorSchedule::class, ['translator_id' => 'id']);
    }

    // Метод для получения доступных переводчиков
    public static function getAvailableTranslators(): array
    {
        $isWeekend = (date('N') >= 6);
        $dayType = $isWeekend ? 'weekend' : 'weekday';

        return static::find()
            ->joinWith('schedules')
            ->where([
                'translator_schedules.day_type' => $dayType,
                'translator_schedules.is_available' => 1
            ])
            ->cache(300) // Кеширование на 5 минут
            ->asArray()
            ->all();
    }

}