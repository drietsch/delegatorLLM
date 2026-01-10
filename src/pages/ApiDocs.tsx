import { Typography, Card, Table, Tag } from 'antd';
import { ApiOutlined, SendOutlined, ToolOutlined, DatabaseOutlined } from '@ant-design/icons';
import XMarkdown from '@ant-design/x-markdown';
import './Pages.css';

const { Title, Text, Paragraph } = Typography;

const endpoints = [
  {
    key: '1',
    method: 'GET',
    path: '/api/agents',
    description: 'List all available AI agents with their capabilities',
    response: `{
  "agents": [
    {
      "name": "search-agent",
      "description": "Searches products and assets",
      "skills": ["product search", "asset lookup"],
      "endpoint": "/api/chat/stream"
    }
  ]
}`,
  },
  {
    key: '2',
    method: 'POST',
    path: '/api/chat/stream',
    description: 'Send a message and receive streaming SSE response',
    request: `{
  "message": "Search for laptops",
  "session_id": "optional-session-id"
}`,
    response: `SSE stream with prefixes:
0:{text}     - Text token chunk
9:{json}     - Tool call start
a:{json}     - Tool result
e:{json}     - Error
d:{json}     - Done`,
  },
  {
    key: '3',
    method: 'GET',
    path: '/api/mcp/servers',
    description: 'List all registered MCP servers',
    response: `{
  "servers": ["search", "dataobjects", "assets"]
}`,
  },
  {
    key: '4',
    method: 'GET',
    path: '/api/mcp/tools',
    description: 'List all available MCP tools from all servers',
    response: `{
  "tools": [
    {
      "name": "advanced_search",
      "description": "Search across products, assets, and documents",
      "inputSchema": {...}
    }
  ]
}`,
  },
  {
    key: '5',
    method: 'POST',
    path: '/api/mcp/tools/call',
    description: 'Call any MCP tool directly',
    request: `{
  "name": "advanced_search",
  "arguments": {
    "query": "laptops",
    "object_type": "product"
  }
}`,
    response: `{
  "content": [...],
  "isError": false
}`,
  },
  {
    key: '6',
    method: 'POST',
    path: '/api/mcp/{server}',
    description: 'JSON-RPC 2.0 endpoint for specific MCP server',
    request: `{
  "jsonrpc": "2.0",
  "id": "1",
  "method": "tools/list",
  "params": {}
}`,
    response: `{
  "jsonrpc": "2.0",
  "id": "1",
  "result": {
    "tools": [...]
  }
}`,
  },
];

const sseProtocol = `
## SSE Stream Protocol

The chat endpoint uses Server-Sent Events (SSE) with a line-based protocol. Each line starts with a single-character prefix followed by a colon and JSON data:

| Prefix | Type | Description |
|--------|------|-------------|
| \`0:\` | Text | Streamed text token from the AI |
| \`9:\` | Tool Call | Tool invocation: \`{toolCallId, toolName, args}\` |
| \`a:\` | Tool Result | Tool completion: \`{toolCallId, result}\` |
| \`e:\` | Error | Error message: \`{message}\` |
| \`d:\` | Done | Stream complete: \`{finishReason}\` |

### Example Stream

\`\`\`
0:"I'll search for "
0:"laptops for you."
9:{"toolCallId":"call_123","toolName":"advanced_search","args":{"query":"laptops"}}
a:{"toolCallId":"call_123","result":{"products":[...]}}
0:"I found 5 laptops..."
d:{"finishReason":"stop"}
\`\`\`
`;

const mcpProtocol = `
## MCP Protocol

The backend implements the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/) specification, enabling standardized AI tool integration.

### Available MCP Servers

| Server | Description | Tools |
|--------|-------------|-------|
| \`search\` | Product and content search | \`advanced_search\` |
| \`dataobjects\` | Data object CRUD operations | \`create\`, \`read\`, \`update\`, \`delete\`, \`list\` |
| \`assets\` | Digital asset management | \`upload\`, \`download\`, \`list\`, \`delete\`, \`move\` |

### JSON-RPC Methods

- \`initialize\` - Initialize connection
- \`tools/list\` - List available tools
- \`tools/call\` - Execute a tool
- \`resources/list\` - List available resources
- \`resources/read\` - Read a resource
`;

const columns = [
  {
    title: 'Method',
    dataIndex: 'method',
    key: 'method',
    width: 80,
    render: (method: string) => (
      <Tag color={method === 'GET' ? 'green' : 'blue'}>{method}</Tag>
    ),
  },
  {
    title: 'Endpoint',
    dataIndex: 'path',
    key: 'path',
    render: (path: string) => <code>{path}</code>,
  },
  {
    title: 'Description',
    dataIndex: 'description',
    key: 'description',
  },
];

export function ApiDocs() {
  return (
    <div className="page-container">
      <div className="page-header">
        <ApiOutlined className="page-icon" />
        <div>
          <Title level={2}>API Documentation</Title>
          <Text type="secondary">
            Complete reference for the Agent Delegator backend API
          </Text>
        </div>
      </div>

      <Card className="page-card">
        <Title level={4}>
          <SendOutlined /> REST Endpoints
        </Title>
        <Paragraph type="secondary">
          Base URL: <code>http://localhost:3001</code>
        </Paragraph>

        <Table
          dataSource={endpoints}
          columns={columns}
          pagination={false}
          size="small"
          expandable={{
            expandedRowRender: (record) => (
              <div className="endpoint-details">
                {record.request && (
                  <div className="endpoint-section">
                    <Text strong>Request Body:</Text>
                    <pre>{record.request}</pre>
                  </div>
                )}
                <div className="endpoint-section">
                  <Text strong>Response:</Text>
                  <pre>{record.response}</pre>
                </div>
              </div>
            ),
          }}
        />
      </Card>

      <Card className="page-card">
        <Title level={4}>
          <DatabaseOutlined /> Streaming Protocol
        </Title>
        <Typography>
          <XMarkdown>{sseProtocol}</XMarkdown>
        </Typography>
      </Card>

      <Card className="page-card">
        <Title level={4}>
          <ToolOutlined /> MCP Integration
        </Title>
        <Typography>
          <XMarkdown>{mcpProtocol}</XMarkdown>
        </Typography>
      </Card>
    </div>
  );
}
