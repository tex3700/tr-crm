<?php

namespace common\models;

use yii\db\{ActiveRecord, ActiveQuery, Exception};

class TranslatorSchedule extends ActiveRecord
{
    const DAY_WEEKDAY = 'weekday';
    const DAY_WEEKEND = 'weekend';


    public static function tableName()
    {
        return 'translator_schedules';
    }

    public function rules(): array
    {
        return [
            [['translator_id', 'day_type'], 'required'],
            ['day_type', 'in', 'range' => [self::DAY_WEEKDAY, self::DAY_WEEKEND]],
            ['is_available', 'boolean'],
            ['translator_id', 'exist', 'targetClass' => Translator::class, 'targetAttribute' => 'id'],
        ];
    }

    // Связь с переводчиком
    public function getTranslator(): ActiveQuery
    {
        return $this->hasOne(Translator::class, ['id' => 'translator_id']);
    }

    // Создание/обновление расписания

    /**
     * @throws Exception
     */
    public static function updateSchedule($translatorId, $dayType, $isAvailable): bool
    {
        $schedule = static::findOne([
            'translator_id' => $translatorId,
            'day_type' => $dayType
        ]);

        if (!$schedule) {
            $schedule = new static();
            $schedule->translator_id = $translatorId;
            $schedule->day_type = $dayType;
        }

        $schedule->is_available = $isAvailable;
        return $schedule->save();
    }
}