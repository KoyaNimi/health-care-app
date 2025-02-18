<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenTime;

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

    /**
     * 記録の新規作成
     *
     * @return \Cake\Http\Response|null リダイレクト先
     */
    public function add()
    {
        // 新しいエンティティを作成
        $record = $this->Records->newEmptyEntity();

        // POSTリクエストの場合
        if ($this->request->is('post')) {
            // リクエストデータでエンティティを作成
            $record = $this->Records->patchEntity($record, $this->request->getData());

            if ($this->Records->save($record)) {
                $this->Flash->success('記録を保存しました。');
                return $this->redirect(['action' => 'index']);
            }

            // 保存失敗時
            $this->Flash->error('記録の保存に失敗しました。入力内容を確認してください。');
        }

        // ビュー変数として設定
        $this->set(compact('record'));
    }
}
