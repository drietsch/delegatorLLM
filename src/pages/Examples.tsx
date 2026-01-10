import { Typography, Card, List, Tag, Button, Space, Tooltip } from 'antd';
import {
  BulbOutlined,
  SearchOutlined,
  EditOutlined,
  TranslationOutlined,
  FileImageOutlined,
  CopyOutlined,
  PlayCircleOutlined,
} from '@ant-design/icons';
import './Pages.css';

const { Title, Text, Paragraph } = Typography;

interface ExampleQuery {
  query: string;
  description: string;
  category: string;
  expectedAgent: string;
  tags: string[];
}

const exampleQueries: ExampleQuery[] = [
  // Search queries - these work with the advanced_search tool
  {
    query: 'Search for laptops',
    description: 'Find all laptop products in the system',
    category: 'Search',
    expectedAgent: 'copilot',
    tags: ['products', 'electronics'],
  },
  {
    query: 'Search for products under $500',
    description: 'Find affordable products with price filtering',
    category: 'Search',
    expectedAgent: 'copilot',
    tags: ['products', 'price'],
  },
  {
    query: 'Find all headphones',
    description: 'Search for audio equipment',
    category: 'Search',
    expectedAgent: 'copilot',
    tags: ['products', 'audio'],
  },
  {
    query: 'Search for TechPro brand products',
    description: 'Find products by brand name',
    category: 'Search',
    expectedAgent: 'copilot',
    tags: ['products', 'brand'],
  },
  {
    query: 'What smartphones do you have?',
    description: 'Natural language product search',
    category: 'Search',
    expectedAgent: 'copilot',
    tags: ['products', 'phones'],
  },

  // Content Generation - uses ai_text_generator
  {
    query: 'Write a product description for a wireless mouse',
    description: 'Generate marketing copy for products',
    category: 'Content',
    expectedAgent: 'copilot',
    tags: ['generation', 'marketing'],
  },
  {
    query: 'Create a catchy headline for a summer tech sale',
    description: 'Generate promotional headlines',
    category: 'Content',
    expectedAgent: 'copilot',
    tags: ['generation', 'headlines'],
  },
  {
    query: 'Write a short blog intro about smart home devices',
    description: 'Generate blog content',
    category: 'Content',
    expectedAgent: 'copilot',
    tags: ['generation', 'blog'],
  },

  // Translation - uses ai_translator
  {
    query: 'Translate "Welcome to our store" to German',
    description: 'Simple text translation',
    category: 'Translation',
    expectedAgent: 'copilot',
    tags: ['translate', 'german'],
  },
  {
    query: 'Translate "High-quality wireless headphones with noise cancellation" to French',
    description: 'Product text translation',
    category: 'Translation',
    expectedAgent: 'copilot',
    tags: ['translate', 'french'],
  },
  {
    query: 'How do you say "Free shipping on orders over $50" in Spanish?',
    description: 'Marketing text translation',
    category: 'Translation',
    expectedAgent: 'copilot',
    tags: ['translate', 'spanish'],
  },

  // Asset queries - uses asset_manager
  {
    query: 'List all images in the system',
    description: 'Browse image assets',
    category: 'Assets',
    expectedAgent: 'copilot',
    tags: ['assets', 'images'],
  },
  {
    query: 'Show me the available PDF documents',
    description: 'Find document assets',
    category: 'Assets',
    expectedAgent: 'copilot',
    tags: ['assets', 'documents'],
  },
  {
    query: 'What videos are available?',
    description: 'List video assets',
    category: 'Assets',
    expectedAgent: 'copilot',
    tags: ['assets', 'videos'],
  },

  // Conversational / Help
  {
    query: 'What can you help me with?',
    description: 'Learn about available capabilities',
    category: 'Help',
    expectedAgent: 'copilot',
    tags: ['help', 'capabilities'],
  },
  {
    query: 'How do I search for products?',
    description: 'Get guidance on using search',
    category: 'Help',
    expectedAgent: 'copilot',
    tags: ['help', 'guide'],
  },
];

const categoryIcons: Record<string, React.ReactNode> = {
  Search: <SearchOutlined />,
  Content: <EditOutlined />,
  Translation: <TranslationOutlined />,
  Assets: <FileImageOutlined />,
  Help: <BulbOutlined />,
};

const categoryColors: Record<string, string> = {
  Search: 'blue',
  Content: 'purple',
  Translation: 'green',
  Assets: 'orange',
  Help: 'cyan',
};

interface ExamplesProps {
  onTryQuery?: (query: string) => void;
}

export function Examples({ onTryQuery }: ExamplesProps) {
  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
  };

  const categories = [...new Set(exampleQueries.map((q) => q.category))];

  return (
    <div className="page-container">
      <div className="page-header">
        <BulbOutlined className="page-icon" />
        <div>
          <Title level={2}>Example Queries</Title>
          <Text type="secondary">
            Sample queries to help you get started with the Agent Delegator
          </Text>
        </div>
      </div>

      <Card className="page-card intro-card">
        <Paragraph>
          The Copilot assistant has access to several tools for searching products, generating content,
          translating text, and managing assets. Below are example queries that demonstrate these capabilities.
          Click <Tag color="blue">Try</Tag> to use a query in the chat, or <Tag>Copy</Tag> to copy it.
        </Paragraph>
        <Space wrap>
          {categories.map((cat) => (
            <Tag key={cat} color={categoryColors[cat]} icon={categoryIcons[cat]}>
              {cat}
            </Tag>
          ))}
        </Space>
      </Card>

      {categories.map((category) => (
        <Card
          key={category}
          className="page-card"
          title={
            <Space>
              {categoryIcons[category]}
              <span>{category} Queries</span>
              <Tag>{exampleQueries.filter((q) => q.category === category).length}</Tag>
            </Space>
          }
        >
          <List
            dataSource={exampleQueries.filter((q) => q.category === category)}
            renderItem={(item) => (
              <List.Item
                className="example-item"
                actions={[
                  <Tooltip title="Copy query" key="copy">
                    <Button
                      type="text"
                      icon={<CopyOutlined />}
                      onClick={() => copyToClipboard(item.query)}
                    />
                  </Tooltip>,
                  onTryQuery && (
                    <Tooltip title="Try this query" key="try">
                      <Button
                        type="primary"
                        size="small"
                        icon={<PlayCircleOutlined />}
                        onClick={() => onTryQuery(item.query)}
                      >
                        Try
                      </Button>
                    </Tooltip>
                  ),
                ].filter(Boolean)}
              >
                <List.Item.Meta
                  title={
                    <Text className="example-query" copyable={{ text: item.query }}>
                      "{item.query}"
                    </Text>
                  }
                  description={
                    <Space direction="vertical" size={4}>
                      <Text type="secondary">{item.description}</Text>
                      <Space size={4} wrap>
                        {item.tags.map((tag) => (
                          <Tag key={tag} style={{ margin: 0 }}>
                            {tag}
                          </Tag>
                        ))}
                      </Space>
                    </Space>
                  }
                />
              </List.Item>
            )}
          />
        </Card>
      ))}

      <Card className="page-card tips-card">
        <Title level={4}>
          <BulbOutlined /> Tips for Effective Queries
        </Title>
        <List
          size="small"
          dataSource={[
            'Use natural language - the AI understands context and intent',
            'Be specific about what you want to find or create',
            'For searches, mention product types, brands, or price ranges',
            'For translations, specify the target language clearly',
            'For content, describe the tone and purpose (marketing, technical, casual)',
            'Ask "What can you help me with?" to see available capabilities',
          ]}
          renderItem={(item) => (
            <List.Item>
              <Text>{item}</Text>
            </List.Item>
          )}
        />
      </Card>
    </div>
  );
}
