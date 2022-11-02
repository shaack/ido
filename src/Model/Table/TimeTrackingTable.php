<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TimeTracking Model
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
class TimeTrackingTable extends Table
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

        $this->setTable('time_tracking');
        $this->setDisplayField('Id');
        $this->setPrimaryKey('Id');
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
            ->integer('task')
            ->allowEmptyString('task');

        $validator
            ->dateTime('start')
            ->allowEmptyDateTime('start');

        $validator
            ->numeric('pause')
            ->allowEmptyString('pause');

        $validator
            ->numeric('duration')
            ->allowEmptyString('duration');

        return $validator;
    }
}
