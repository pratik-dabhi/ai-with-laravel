<?php

use Laravel\Mcp\Facades\Mcp;

use App\Mcp\Servers\ChatServer;

Mcp::web('/mcp/chats', ChatServer::class);


