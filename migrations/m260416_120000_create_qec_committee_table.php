<?php

use yii\db\Migration;

/**
 * Class m260416_120000_create_qec_committee_table
 */
class m260416_120000_create_qec_committee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%qec_committee}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'is_teacher' => $this->boolean()->notNull()->defaultValue(false),
            'position' => $this->string(100)->null(),
            'appointed_date' => $this->date()->null(),
            'status' => $this->string(20)->notNull()->defaultValue('active'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-qec_committee-user_id',
            '{{%qec_committee}}',
            'user_id',
            '{{%users}}',
            'user_id',
            'CASCADE',
            'CASCADE'
        );

        // Create index for faster queries
        $this->createIndex(
            'idx-qec_committee-status',
            '{{%qec_committee}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-qec_committee-user_id', '{{%qec_committee}}');
        $this->dropIndex('idx-qec_committee-status', '{{%qec_committee}}');
        $this->dropTable('{{%qec_committee}}');
    }
}