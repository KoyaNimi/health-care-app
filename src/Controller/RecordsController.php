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
}
