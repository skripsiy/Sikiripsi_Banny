<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Classroom;
use App\Models\Guru;
use App\Models\GuruMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class KurikulumKelasTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $guruUser;
    protected $guru;
    protected $tahunAkademik;
    protected $classroom;
    protected $mataPelajaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        Admin::create(['user_id' => $this->adminUser->id]);

        $this->guruUser = User::factory()->create(['role' => 'guru']);
        $this->guru = Guru::create(['user_id' => $this->guruUser->id, 'nip' => '199001012020011001']);

        $this->tahunAkademik = TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'nama_kelas' => 'X RPL 1',
            'jurusan_id' => $jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);

        $this->mataPelajaran = MataPelajaran::create([
            'kode_pelajaran' => 'WEB10',
            'nama_pelajaran' => 'Pemrograman Web',
            'is_active' => true,
        ]);

        // Penugasan Guru (Guru -> Mapel & Kelas)
        GuruMataPelajaran::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'classroom_id' => $this->classroom->id,
        ]);
    }

    public function test_admin_can_view_kurikulum_kelas_index()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.manage.kurikulum-kelas.index'));

        $response->assertStatus(200);
        $response->assertSee('Plotting Kurikulum Mata Pelajaran Per Kelas');
        $response->assertSee('X RPL 1');
    }

    public function test_admin_can_assign_subjects_to_classroom()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.manage.kurikulum-kelas.store'), [
            'classroom_id' => $this->classroom->id,
            'mata_pelajaran_ids' => [$this->mataPelajaran->id],
        ]);

        $response->assertRedirect(route('admin.manage.kurikulum-kelas.index'));
        $this->assertDatabaseHas('classroom_mata_pelajaran', [
            'classroom_id' => $this->classroom->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
        ]);
    }

    public function test_guru_cannot_create_module_for_unassigned_classroom_in_kurikulum_kelas()
    {
        // Classroom does NOT have $this->mataPelajaran assigned yet in Kurikulum Kelas
        $response = $this->actingAs($this->guruUser)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasErrors(['classroom_id']);
        $this->assertDatabaseMissing('learning_modules', [
            'guru_id' => $this->guru->id,
            'classroom_id' => $this->classroom->id,
        ]);
    }

    public function test_guru_can_create_module_when_classroom_is_assigned_in_kurikulum_kelas()
    {
        // Admin assigns subject to classroom in Kurikulum Kelas
        $this->classroom->mataPelajarans()->sync([$this->mataPelajaran->id]);

        $response = $this->actingAs($this->guruUser)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertRedirect(route('guru.learning-modules.index'));
        $this->assertDatabaseHas('learning_modules', [
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'classroom_id' => $this->classroom->id,
        ]);
    }

    public function test_admin_can_download_kurikulum_kelas_template()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.manage.kurikulum-kelas.template'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_import_kurikulum_kelas_excel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'nama_kelas');
        $sheet->setCellValue('B1', 'kode_pelajaran');
        $sheet->setCellValue('A2', 'X RPL 1');
        $sheet->setCellValue('B2', 'WEB10');

        $filePath = tempnam(sys_get_temp_dir(), 'test_import') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $file = new UploadedFile($filePath, 'kurikulum_kelas.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->adminUser)->post(route('admin.manage.kurikulum-kelas.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.manage.kurikulum-kelas.index'));
        $this->assertDatabaseHas('classroom_mata_pelajaran', [
            'classroom_id' => $this->classroom->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
        ]);

        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
