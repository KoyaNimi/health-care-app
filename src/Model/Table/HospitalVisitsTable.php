<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableSchemaInterface;

/**
 * HospitalVisits Model
 *
 * @property \App\Model\Table\RecordsTable&\Cake\ORM\Association\BelongsTo $Records
 *
 * @method \App\Model\Entity\HospitalVisit newEmptyEntity()
 * @method \App\Model\Entity\HospitalVisit newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\HospitalVisit> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HospitalVisit get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\HospitalVisit findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\HospitalVisit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\HospitalVisit> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HospitalVisit|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\HospitalVisit saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\HospitalVisit>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HospitalVisit>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HospitalVisit>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HospitalVisit> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HospitalVisit>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HospitalVisit>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HospitalVisit>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HospitalVisit> deleteManyOrFail(iterable $entities, array $options = [])
 */
class HospitalVisitsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('hospital_visits');
        $this->setDisplayField('hospital_name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Records', [
            'foreignKey' => 'record_id',
            'joinType' => 'INNER',
        ]);

        // アクセス可能なフィールドの設定
        $this->setEntityClass('HospitalVisit');
    }

    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('is_deleted', 'boolean');
        return $schema;
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
            ->integer('record_id')
            ->notEmptyString('record_id');

        $validator
            ->scalar('hospital_name')
            ->maxLength('hospital_name', 255)
            ->requirePresence('hospital_name', 'create')
            ->notEmptyString('hospital_name');

        $validator
            ->dateTime('visit_datetime')
            ->requirePresence('visit_datetime', 'create')
            ->notEmptyDateTime('visit_datetime');

        $validator
            ->scalar('treatment_details')
            ->allowEmptyString('treatment_details');

        $validator
            ->scalar('impressions')
            ->allowEmptyString('impressions');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('modified_at')
            ->notEmptyDateTime('modified_at');

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
        $rules->add($rules->existsIn(['record_id'], 'Records'), ['errorField' => 'record_id']);

        return $rules;
    }
}
