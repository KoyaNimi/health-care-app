<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Records Controller
 *
 * @property \App\Model\Table\RecordsTable $Records
 */
class RecordsController extends AppController
{
    /**
     * 記録一覧を表示するアクション
     * 
     * @return void
     */
    public function index()
    {
        // RecordsTableから全ての記録を取得
        $records = $this->Records->find()
            ->where(['is_deleted' => false])
            ->orderBy(['onset_date' => 'DESC'])
            ->all();

        // ビューに記録データをセット
        $this->set(compact('records'));
    }

    /**
     * 記録の詳細表示
     *
     * @param string|null $id 記録ID
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException 指定されたIDの記録が存在しない場合、Cakeは404エラーを返す
     */
    public function view($id = null)
    {
        // 記録を取得（関連する通院記録も含める）
        $record = $this->Records->get($id, contain: 'HospitalVisits');

        $this->set(compact('record'));
    }
}
