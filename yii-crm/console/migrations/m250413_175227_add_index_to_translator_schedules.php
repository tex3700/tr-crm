<?php

use yii\db\Migration;

class m250413_175227_add_index_to_translator_schedules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-translator_schedules-translator_id-day_type',
            'translator_schedules',
            ['translator_id', 'day_type']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-translator_schedules-translator_id-day_type',
            'translator_schedules'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250413_175227_add_index_to_translator_schedules cannot be reverted.\n";

        return false;
    }
    */
}
