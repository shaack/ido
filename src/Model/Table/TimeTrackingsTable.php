<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TimeTrackings Model
 *
 * @property \App\Model\Table\TasksTable&\Cake\ORM\Association\BelongsTo $Tasks
 *
 * @method \App\Model\Entity\TimeTracking newEmptyEntity()
 * @method \App\Model\Entity\TimeTracking newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TimeTracking[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TimeTracking get($primaryKey, $options = [])
 * @method \App\Model\Entity\TimeTracking findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TimeTracking patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TimeTracking[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TimeTracking|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TimeTracking saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TimeTrackingsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('time_trackings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Tasks', [
            'foreignKey' => 'task_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('task_id')
            ->notEmptyString('task_id');

        $validator
            ->dateTime('start')
            ->allowEmptyDateTime('start');

        $validator
            ->numeric('pause')
            ->allowEmptyString('pause');

        $validator
            ->numeric('duration');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('task_id', 'Tasks'), ['errorField' => 'task_id']);

        return $rules;
    }
}
