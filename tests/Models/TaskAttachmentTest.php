<?php

namespace Tests\Models;

use App\Models\TaskAttachment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskAttachmentTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_return_attachment_path()
    {
        $taskAttachment = factory(TaskAttachment::class)->create();

        $taskAttachment = TaskAttachment::first();

        $this->assertNotEmpty($taskAttachment->file_path);
        $this->assertStringContainsString('attachments', $taskAttachment->file_path);
    }

    /** @test */
    public function test_return_attachment_url()
    {
        $taskAttachment = factory(TaskAttachment::class)->create();

        $taskAttachment = TaskAttachment::first();

        $this->assertNotEmpty($taskAttachment->file_url);
    }
}
