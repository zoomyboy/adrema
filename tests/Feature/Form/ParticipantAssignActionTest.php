<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParticipantAssignActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItAssignsAParticipantToAMenber(): void
    {
        $this->login()->loginNami();
        $participant = Participant::factory()->for(Form::factory())->create();
        $member = Member::factory()->defaults()->create();

        $this->postJson(route('participant.assign', ['participant' => $participant]), ['member_id' => $member->id])->assertOk();

        $this->assertEquals($member->id, $participant->fresh()->member_id);
    }
}
