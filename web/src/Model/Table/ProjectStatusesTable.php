<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProjectStatuses Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $Projects
 *
 * @method \App\Model\Entity\ProjectStatus newEmptyEntity()
 * @method \App\Model\Entity\ProjectStatus newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ProjectStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProjectStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProjectStatus findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ProjectStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProjectStatus[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProjectStatus|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProjectStatus saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProjectStatus[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProjectStatus[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProjectStatus[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ProjectStatus[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProjectStatusesTable extends Table
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

        $this->setTable('project_statuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Projects', [
            'foreignKey' => 'project_status_id',
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
            ->scalar('name')
            ->maxLength('name', 32)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['name']), ['errorField' => 'name']);

        return $rules;
    }
}
