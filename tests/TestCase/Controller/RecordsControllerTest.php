<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\RecordsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use App\Test\Fixture\RecordsFixture;
use App\Test\Fixture\HospitalVisitsFixture;

/**
 * 記録コントローラーのテストクラス
 */
class RecordsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    // フィクスチャクラスを直接指定
    protected array $fixtures = [
        RecordsFixture::class,
        HospitalVisitsFixture::class
    ];

    /**
     * テストの前処理
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    /**
     * 記録が0件の場合のindexアクションのテスト
     * 
     * @return void
     */
    public function testIndexWithNoRecords(): void
    {
        // 1. テーブルは自動的に空の状態（truncateによる）

        // 2. /records へのGETリクエストを実行
        $this->get('/records');

        // 3. レスポンスが成功（200 OK）することを確認
        $this->assertResponseSuccess();

        // 4. ビュー変数に渡されるrecordsが空であることを確認
        $records = $this->viewVariable('records');
        $this->assertEmpty($records);
    }

    /**
     * 記録が存在する場合のindexアクションのテスト
     * 
     * @return void
     */
    public function testIndexWithRecords(): void
    {
        // 1. テストデータの作成
        $record = $this->getTableLocator()->get('Records')->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);

        // 2. テストデータをデータベースに保存
        $this->getTableLocator()->get('Records')->save($record);

        // 3. /records へのGETリクエストを実行
        $this->get('/records');

        // 4. レスポンスが成功（200 OK）することを確認
        $this->assertResponseSuccess();

        // 5. ビュー変数のテスト
        $records = $this->viewVariable('records');
        $this->assertNotEmpty($records);                                  // レコードが存在することを確認
        $this->assertEquals(1, count($records));                         // レコード数が1件であることを確認
        $this->assertEquals('Test Disease', $records->first()->disease_name); // 病名が正しいことを確認
    }

    /**
     * 記録が正しい順序（発症日の降順）で表示されることのテスト
     */
    public function testIndexOrderByOnsetDate(): void
    {
        // 1. テストデータの作成
        $records = $this->getTableLocator()->get('Records');

        // 1つ目の記録（新しい発症日）
        $record1 = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'New Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);

        // 2つ目の記録（古い発症日）
        $record2 = $records->newEntity([
            'onset_date' => '2024-01-01',
            'disease_name' => 'Old Disease',
            'severity' => 1,
            'created_at' => '2024-01-01 10:00:00',
            'modified_at' => '2024-01-01 10:00:00',
            'is_deleted' => false,
        ]);

        // 2. データを保存
        $records->saveMany([$record1, $record2]);

        // 3. /records へのGETリクエストを実行
        $this->get('/records');
        $this->assertResponseSuccess();

        // 4. ビュー変数のテスト
        $viewRecords = $this->viewVariable('records');
        $this->assertNotEmpty($viewRecords);
        $this->assertEquals(2, count($viewRecords));

        // 5. 順序のテスト
        $firstRecord = $viewRecords->first();
        $lastRecord = $viewRecords->last();

        // 新しい発症日の記録が最初に表示される
        $this->assertEquals('New Disease', $firstRecord->disease_name);
        $this->assertEquals('2024-02-14', $firstRecord->onset_date->format('Y-m-d'));

        // 古い発症日の記録が後に表示される
        $this->assertEquals('Old Disease', $lastRecord->disease_name);
        $this->assertEquals('2024-01-01', $lastRecord->onset_date->format('Y-m-d'));
    }

    /**
     * 記録の詳細表示テスト
     * 
     * @return void
     */
    public function testView(): void
    {
        // 1. テストデータの作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // 2. 関連する通院記録の作成
        $hospitalVisits = $this->getTableLocator()->get('HospitalVisits');
        $visit = $hospitalVisits->newEntity([
            'record_id' => $record->id,
            'hospital_name' => 'Test Hospital',
            'visit_datetime' => '2024-02-14 15:00:00',
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
        ]);
        $hospitalVisits->save($visit);

        // 3. /records/view/1 へのGETリクエストを実行
        $this->get('/records/view/' . $record->id);

        // 4. レスポンスのテスト
        $this->assertResponseSuccess();

        // 5. ビュー変数のテスト
        $viewRecord = $this->viewVariable('record');
        $this->assertNotEmpty($viewRecord);
        $this->assertEquals('Test Disease', $viewRecord->disease_name);

        // 6. 関連する通院記録のテスト
        $this->assertNotEmpty($viewRecord->hospital_visits);
        $this->assertEquals(1, count($viewRecord->hospital_visits));
        $this->assertEquals('Test Hospital', $viewRecord->hospital_visits[0]->hospital_name);
    }

    /**
     * 存在しない記録のIDでviewアクションを実行した場合のテスト
     */
    public function testViewNotFound(): void
    {
        $this->get('/records/view/99999');
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 削除済みの記録の詳細画面にアクセスした場合のテスト
     */
    public function testViewDeletedRecord(): void
    {
        // 削除済みの記録を作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Deleted Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => true,  // 削除済み
        ]);
        $records->save($record);

        // 削除済みの記録にアクセス
        $this->get('/records/view/' . $record->id);

        // 404エラーが返されることを確認
        $this->assertResponseCode(404);
    }

    /**
     * GETリクエストでaddアクションを実行した場合のテスト（フォーム表示）
     */
    public function testAddGet(): void
    {
        // GETリクエストを実行
        $this->get('/records/add');

        // レスポンスのテスト
        $this->assertResponseSuccess();
        $this->assertResponseCode(200);

        // ビュー変数のテスト
        $this->assertInstanceOf('App\Model\Entity\Record', $this->viewVariable('record'));
    }

    /**
     * 正常なPOSTリクエストでaddアクションを実行した場合のテスト
     */
    public function testAddPost(): void
    {
        $data = [
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'complications' => 'Test Complications',
            'medications' => 'Test Medications',
        ];

        // POSTリクエストを実行
        $this->post('/records/add', $data);

        // リダイレクトのテスト
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Records', 'action' => 'index']);

        // フラッシュメッセージのテスト
        $this->assertFlashMessage('記録を保存しました。');

        // データベースに保存されたことを確認
        $records = $this->getTableLocator()->get('Records');
        $query = $records->find()->where(['disease_name' => 'Test Disease']);
        $result = $query->first();

        $this->assertNotEmpty($result);
        $this->assertEquals('Test Disease', $result->disease_name);
        $this->assertEquals('2024-02-14', $result->onset_date->format('Y-m-d'));
    }

    /**
     * バリデーションエラーとなるPOSTリクエストでaddアクションを実行した場合のテスト
     */
    public function testAddPostValidationError(): void
    {
        $data = [
            'onset_date' => '',  // 必須項目を空に
            'disease_name' => '', // 必須項目を空に
            'severity' => 'invalid',  // 不正な値
        ];

        // POSTリクエストを実行
        $this->post('/records/add', $data);

        // レスポンスのテスト
        $this->assertResponseSuccess();
        $this->assertResponseCode(200);  // バリデーションエラー時は200 OK

        // エラーの検証
        $record = $this->viewVariable('record');
        $this->assertGreaterThan(0, $record->getErrors());
    }

    /**
     * GETリクエストでeditアクションを実行した場合のテスト（フォーム表示）
     */
    public function testEditGet(): void
    {
        // テストデータの作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // GETリクエストを実行
        $this->get('/records/edit/' . $record->id);

        // レスポンスのテスト
        $this->assertResponseSuccess();

        // ビュー変数のテスト
        $editRecord = $this->viewVariable('record');
        $this->assertNotEmpty($editRecord);
        $this->assertEquals($record->id, $editRecord->id);
        $this->assertEquals('Test Disease', $editRecord->disease_name);
    }

    /**
     * 正常なPOSTリクエストでeditアクションを実行した場合のテスト
     */
    public function testEditPost(): void
    {
        // テストデータの作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Original Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // 更新データ
        $data = [
            'disease_name' => 'Updated Disease',
            'severity' => 2,
        ];

        // POSTリクエストを実行
        $this->post('/records/edit/' . $record->id, $data);

        // リダイレクトのテスト
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Records', 'action' => 'index']);

        // フラッシュメッセージのテスト
        $this->assertFlashMessage('記録を更新しました。');

        // データベースの更新を確認
        $updated = $records->get($record->id);
        $this->assertEquals('Updated Disease', $updated->disease_name);
        $this->assertEquals(2, $updated->severity);
    }

    /**
     * 存在しない記録のIDでeditアクションを実行した場合のテスト
     */
    public function testEditGetNotFound(): void
    {
        $this->get('/records/edit/99999');
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 正常なDELETEリクエストでdeleteアクションを実行した場合のテスト
     */
    public function testDelete(): void
    {
        // テストデータの作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // DELETEリクエストを実行
        $this->delete('/records/delete/' . $record->id);

        // リダイレクトのテスト
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Records', 'action' => 'index']);

        // フラッシュメッセージのテスト
        $this->assertFlashMessage('記録を削除しました。');

        // 論理削除の確認
        $deleted = $records->get($record->id);
        $this->assertTrue($deleted->is_deleted);
    }

    /**
     * 存在しない記録のIDでdeleteアクションを実行した場合のテスト
     */
    public function testDeleteNotFound(): void
    {
        $this->delete('/records/delete/99999');
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 通院記録を含む記録を削除した場合のテスト
     */
    public function testDeleteWithHospitalVisits(): void
    {
        // テストデータの作成（症状の記録）
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // 関連する通院記録の作成
        $hospitalVisits = $this->getTableLocator()->get('HospitalVisits');
        $visit = $hospitalVisits->newEntity([
            'record_id' => $record->id,
            'hospital_name' => 'Test Hospital',
            'visit_datetime' => '2024-02-14 15:00:00',
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);

        // 保存前のデータを確認
        debug('保存前の通院記録:');
        debug($visit->toArray());

        $result = $hospitalVisits->save($visit);

        // 保存後のデータを確認
        debug('保存後の通院記録:');
        debug($result ? $result->toArray() : 'Save failed');

        // DELETEリクエストを実行
        $this->delete('/records/delete/' . $record->id);

        // 削除後のデータを確認
        $deletedVisit = $hospitalVisits->get($visit->id);
        debug('削除後の通院記録:');
        debug($deletedVisit->toArray());

        // アサーション
        $this->assertTrue($deletedVisit->is_deleted, '通院記録が論理削除されていません');
    }

    /**
     * 論理削除されたレコードが一覧に表示されないことをテストする
     */
    public function testIndexHidesDeletedRecords(): void
    {
        // テストデータの作成（通常の記録）
        $records = $this->getTableLocator()->get('Records');
        $activeRecord = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Active Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($activeRecord);

        // 論理削除済みの記録を作成
        $deletedRecord = $records->newEntity([
            'onset_date' => '2024-02-13',
            'disease_name' => 'Deleted Disease',
            'severity' => 2,
            'created_at' => '2024-02-13 10:00:00',
            'modified_at' => '2024-02-13 10:00:00',
            'is_deleted' => true,
        ]);
        $records->save($deletedRecord);

        // 一覧を取得
        $this->get('/records');

        // レスポンスの確認
        $this->assertResponseOk();

        // アクティブな記録は表示される
        $this->assertResponseContains('Active Disease');

        // 削除済みの記録は表示されない
        $this->assertResponseNotContains('Deleted Disease');
    }

    /**
     * 削除済みの通院記録が詳細画面に表示されないことをテストする
     */
    public function testViewHidesDeletedHospitalVisits(): void
    {
        // 症状の記録を作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // 通常の通院記録を作成
        $hospitalVisits = $this->getTableLocator()->get('HospitalVisits');
        $activeVisit = $hospitalVisits->newEntity([
            'record_id' => $record->id,
            'hospital_name' => 'Active Hospital',
            'visit_datetime' => '2024-02-14 15:00:00',
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $hospitalVisits->save($activeVisit);

        // 削除済みの通院記録を作成
        $deletedVisit = $hospitalVisits->newEntity([
            'record_id' => $record->id,
            'hospital_name' => 'Deleted Hospital',
            'visit_datetime' => '2024-02-13 15:00:00',
            'created_at' => '2024-02-13 10:00:00',
            'modified_at' => '2024-02-13 10:00:00',
            'is_deleted' => true,
        ]);
        $hospitalVisits->save($deletedVisit);

        // 詳細画面を表示
        $this->get('/records/view/' . $record->id);

        // レスポンスの確認
        $this->assertResponseOk();

        // 通常の通院記録は表示される
        $this->assertResponseContains('Active Hospital');

        // 削除済みの通院記録は表示されない
        $this->assertResponseNotContains('Deleted Hospital');
    }

    /**
     * カレンダー表示のテスト
     */
    public function testCalendar(): void
    {
        // カレンダーページにアクセス
        $this->get('/records/calendar');

        // レスポンスの確認
        $this->assertResponseOk();

        // カレンダーの要素が存在することを確認
        $this->assertResponseContains('id="calendar"');

        // ナビゲーションボタンが存在することを確認
        $this->assertResponseContains('前月');
        $this->assertResponseContains('翌月');
    }
}
