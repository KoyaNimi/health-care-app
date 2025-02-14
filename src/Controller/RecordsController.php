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
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Records->find();
        $records = $this->paginate($query);

        $this->set(compact('records'));
    }
}
