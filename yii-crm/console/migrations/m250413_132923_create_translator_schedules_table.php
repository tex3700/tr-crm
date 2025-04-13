<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%translator_schedules}}`.
 */
class m250413_132923_create_translator_schedules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%translator_schedules}}', [
            'id' => $this->primaryKey(),
            'translator_id' => $this->integer()->notNull(),
            'day_type' => $this->string(10)->notNull(),
            'is_available' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-translator_schedules-translator_id',
            'translator_schedules',
            'translator_id',
            'translators',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-translator_schedules-translator_id', 'translator_schedules');
        $this->dropTable('{{%translator_schedules}}');
    }
}
