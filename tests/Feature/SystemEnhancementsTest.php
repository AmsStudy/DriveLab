<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Soal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $otherAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $this->otherAdmin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_duplicate_quiz(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Jaringan Dasar',
            'slug'               => 'kuis-jaringan-dasar',
            'access_code'        => 'JAR001',
            'unique_token'       => 'tok12345',
            'time_limit_minutes' => 20,
            'shuffle_questions'  => true,
            'shuffle_options'    => true,
            'is_active'          => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Apa itu IP Address?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => 'Alamat identifikasi perangkat pada jaringan',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => 'Kabel konektor LAN',
            'is_correct'       => false,
            'order_num'        => 2,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.quizzes.duplicate', $quiz));
        $response->assertSessionHas('success');
        $response->assertRedirect(route('admin.quizzes.index'));

        // Verify duplicate quiz
        $duplicate = Quiz::where('id', '!=', $quiz->id)->first();
        $this->assertNotNull($duplicate);
        $this->assertEquals('Kuis Jaringan Dasar (Salinan)', $duplicate->title);
        $this->assertNotEquals('JAR001', $duplicate->access_code);
        $this->assertNotEquals('tok12345', $duplicate->unique_token);
        $this->assertFalse($duplicate->is_active); // Must start inactive for review
        $this->assertEquals(20, $duplicate->time_limit_minutes);

        // Verify questions and options were cloned
        $this->assertEquals(1, $duplicate->questions()->count());
        $clonedQuestion = $duplicate->questions()->first();
        $this->assertEquals('Apa itu IP Address?', $clonedQuestion->question_text);
        $this->assertEquals(2, $clonedQuestion->options()->count());
        $this->assertTrue($clonedQuestion->options()->where('is_correct', true)->exists());
    }

    public function test_admin_can_preview_and_delete_submission(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'user_id'      => $this->admin->id,
            'title'        => 'Laporan Praktikum Sistem Operasi',
            'kode_unik'    => 'SO-001',
            'unique_token' => 'so123456',
            'password'     => Hash::make('rahasia123'),
            'access_type'  => 'lifetime',
        ]);

        $file = UploadedFile::fake()->create('laporan.pdf', 500, 'application/pdf');
        $filePath = $file->storeAs('submissions/' . $soal->id, '13020250001_Ahmad_laporan.pdf', 'private');

        $submission = Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020250001',
            'nama'               => 'Ahmad Fulan',
            'kelas'              => 'A1',
            'submission_type'    => 'file',
            'file_original_name' => 'laporan.pdf',
            'file_path'          => $filePath,
            'file_extension'     => 'pdf',
            'file_size'          => 500 * 1024,
        ]);

        // 1. Preview Submission
        $previewResponse = $this->actingAs($this->admin)->get(route('admin.soal.submission.preview', $submission->id));
        $previewResponse->assertStatus(200);

        // 2. Other admin cannot delete submission (Authorization check)
        $unauthDelete = $this->actingAs($this->otherAdmin)->delete(route('admin.soal.submission.destroy', $submission->id));
        $unauthDelete->assertStatus(403);
        $this->assertDatabaseHas('submissions', ['id' => $submission->id]);

        // 3. Authorized Admin deletes submission
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.soal.submission.destroy', $submission->id));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
        Storage::disk('private')->assertMissing($filePath);
    }
}
