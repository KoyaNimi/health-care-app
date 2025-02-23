<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenTime;
use Cake\Http\Exception\NotFoundException;

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
        // 削除されていないレコードのみ取得
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
        $record = $this->Records->get($id, contain: [
            'HospitalVisits' => fn($q) => $q->where(['HospitalVisits.is_deleted' => false])
        ]);

        // 削除済みの記録へのアクセスは404エラー
        if ($record->is_deleted) {
            throw new NotFoundException(__('記録が見つかりません。'));
        }

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

    /**
     * 記録の編集
     *
     * @param string $id 記録ID
     * @return \Cake\Http\Response|null リダイレクト先
     */
    public function edit(string $id)
    {
        $record = $this->Records->get($id, contain: ['HospitalVisits']);

        if ($this->request->is('post')) {
            $record = $this->Records->patchEntity($record, $this->request->getData());
            if ($this->Records->save($record)) {
                $this->Flash->success('記録を更新しました。');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('記録の更新に失敗しました。入力内容を確認してください。');
        }

        $this->set(compact('record'));
    }

    /**
     * 記録の削除（論理削除）
     *
     * @param string $id 記録ID
     * @return \Cake\Http\Response|null リダイレクト先
     */
    public function delete(string $id)
    {
        $connection = $this->Records->getConnection();
        $result = $connection->transactional(function () use ($id) {
            // 症状の記録を取得（関連データも含める）
            $record = $this->Records->get($id, contain: [
                'HospitalVisits' => function ($q) {
                    return $q->select(['id', 'record_id', 'is_deleted']);  // 必要なフィールドを明示的に指定
                }
            ]);

            // 症状の記録を論理削除
            $record->is_deleted = true;
            if (!$this->Records->save($record)) {
                return false;
            }

            // 関連する通院記録を論理削除
            if (!empty($record->hospital_visits)) {
                foreach ($record->hospital_visits as $visit) {
                    $visit->is_deleted = true;
                    if (!$this->Records->HospitalVisits->save($visit)) {
                        return false;
                    }
                }
            }

            return true;
        });

        if ($result) {
            $this->Flash->success('記録を削除しました。');
        } else {
            $this->Flash->error('記録の削除に失敗しました。');
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * カレンダー表示
     */
    public function calendar()
    {
        // 削除されていないレコードのみ取得
        $records = $this->Records->find()
            ->where(['Records.is_deleted' => false])
            ->contain(['HospitalVisits' => function ($q) {
                return $q->where(['HospitalVisits.is_deleted' => false]);
            }])
            ->order(['Records.onset_date' => 'DESC'])
            ->all();

        // 年の選択肢を準備
        $currentYear = (int)date('Y');
        $years = range($currentYear - 2, $currentYear + 2);
        $yearOptions = array_combine($years, array_map(function ($year) {
            return $year . '年';
        }, $years));

        // 月の選択肢を準備
        $months = range(1, 12);
        $monthOptions = array_combine($months, array_map(function ($month) {
            return $month . '月';
        }, $months));

        $this->set(compact('records', 'yearOptions', 'monthOptions'));
    }
}
