<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\PrismFake;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_chat_page()
    {
        $response = $this->get('/chats');

        $response->assertStatus(200);
        $response->assertSee('AI Chatbot');
    }

    public function test_can_create_new_chat()
    {
        $response = $this->post('/chats');

        $this->assertDatabaseCount('chats', 1);
        $chat = Chat::first();
        $response->assertRedirect(route('chats.show', $chat));
    }

    public function test_can_send_message_and_get_reply()
    {
        $this->withoutExceptionHandling();

        Prism::fake([
           new \Prism\Prism\Text\Response(
               steps: collect([]),
               text: 'Hello from AI',
               finishReason: \Prism\Prism\Enums\FinishReason::Stop,
               toolCalls: [],
               toolResults: [],
               usage: new \Prism\Prism\ValueObjects\Usage(10, 10),
               meta: new \Prism\Prism\ValueObjects\Meta(id: '123', model: 'mistral'),
               messages: collect([]),
           ),
        ]);

        $chat = Chat::create(['title' => 'Test Chat']);

        $response = $this->postJson(route('chats.messages.store', $chat), [
            'content' => 'Hello AI',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => 'Hello AI',
        ]);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'role' => 'assistant',
            'content' => 'Hello from AI', 
        ]);
    }
}
