import type { DelegationResult, Agent } from '../types';
import agentManifest from '../config/agents.json';

/**
 * Mock response generators for each agent type
 */
const mockResponses: Record<string, (query: string) => string> = {
  ai_text_generator: (query: string) =>
    `[AI Text Generator]\n\nGenerating content for: "${query}"\n\n**Generated Text:**\nLorem ipsum dolor sit amet, consectetur adipiscing elit. This is AI-generated placeholder content that would normally be produced by GPT-4, Claude, or Hugging Face models.\n\n**Options:**\n- Regenerate with different tone\n- Expand this content\n- Make it more concise`,

  ai_image_generator: (query: string) =>
    `[AI Image Generator]\n\nCreating image for: "${query}"\n\n**Status:** Image generation queued\n**Model:** Stable Diffusion XL\n**Resolution:** 1024x1024\n\n*Image would appear here in production*\n\n**Actions:**\n- Generate variations\n- Upscale image\n- Edit with inpainting`,

  ai_translator: (query: string) =>
    `[AI Translator]\n\nTranslating: "${query}"\n\n**Detected Language:** English\n**Target Languages Available:**\n- German (DE)\n- French (FR)\n- Spanish (ES)\n- Italian (IT)\n\n**Sample Translation (German):**\n"Dies ist eine Beispielübersetzung..."`,

  ai_classifier: (query: string) =>
    `[AI Classifier]\n\nClassifying: "${query}"\n\n**Detected Categories:**\n- Primary: Technology (87%)\n- Secondary: Business (65%)\n- Tertiary: Innovation (45%)\n\n**Suggested Tags:**\n\`ai\`, \`machine-learning\`, \`automation\``,

  data_object_manager: (query: string) =>
    `[Data Object Manager]\n\nProcessing: "${query}"\n\n**Available Actions:**\n- Create new data object\n- Update existing object\n- Delete object\n- Query objects\n\n**Recent Objects:**\n| ID | Class | Name | Modified |\n|---|---|---|---|\n| 1234 | Product | Widget Pro | 2 hours ago |\n| 1235 | Category | Electronics | 1 day ago |`,

  asset_manager: (query: string) =>
    `[Asset Manager]\n\nManaging assets for: "${query}"\n\n**Asset Library Stats:**\n- Total Assets: 15,234\n- Images: 12,456\n- Documents: 2,134\n- Videos: 644\n\n**Actions:**\n- Upload new asset\n- Generate thumbnails\n- Edit metadata\n- Organize folders`,

  document_manager: (query: string) =>
    `[Document Manager]\n\nDocument operation: "${query}"\n\n**Document Types:**\n- Pages (CMS content)\n- Email templates\n- Snippets (reusable blocks)\n- Links (redirects)\n\n**Recent Documents:**\n- /en/homepage (Page)\n- /en/about-us (Page)\n- /emails/welcome (Email)`,

  advanced_search: (query: string) =>
    `[Advanced Search]\n\nSearching for: "${query}"\n\n**Search Results (5 of 127):**\n\n1. **Product: Widget Pro** (Score: 0.95)\n   ID: 1234, Class: Product\n\n2. **Category: Electronics** (Score: 0.87)\n   ID: 5678, Class: Category\n\n3. **Asset: widget-banner.jpg** (Score: 0.82)\n   Type: Image, Size: 2.4MB\n\n**Filters Applied:** None\n**Sort:** Relevance`,

  data_importer: (query: string) =>
    `[Data Importer]\n\nImport request: "${query}"\n\n**Supported Sources:**\n- CSV files\n- XML files\n- JSON files\n- REST APIs\n- SFTP locations\n\n**Import Status:**\n- Last import: 2 hours ago\n- Records processed: 1,543\n- Success rate: 99.2%`,

  data_exporter: (query: string) =>
    `[Data Exporter]\n\nExport request: "${query}"\n\n**Export Formats:**\n- CSV (Comma-separated)\n- XML (Structured)\n- JSON (API-ready)\n\n**Export Configuration:**\n- Include: All fields\n- Filter: Active products only\n- Destination: /exports/`,

  graphql_api: (query: string) =>
    `[GraphQL API]\n\nAPI request: "${query}"\n\n**Endpoint:** /pimcore-graphql-webservices/\n\n**Sample Query:**\n\`\`\`graphql\nquery {\n  getProductListing(first: 10) {\n    edges {\n      node {\n        id\n        name\n        price\n      }\n    }\n  }\n}\n\`\`\``,

  ecommerce_cart: (query: string) =>
    `[E-Commerce Cart]\n\nCart operation: "${query}"\n\n**Current Cart:**\n| Product | Qty | Price |\n|---------|-----|-------|\n| Widget Pro | 2 | $49.98 |\n| Gadget X | 1 | $29.99 |\n\n**Subtotal:** $79.97\n**Actions:** Update, Remove, Checkout`,

  ecommerce_checkout: (query: string) =>
    `[E-Commerce Checkout]\n\nCheckout: "${query}"\n\n**Checkout Steps:**\n1. ✅ Cart Review\n2. ⏳ Shipping Address\n3. ⬜ Payment Method\n4. ⬜ Order Confirmation\n\n**Available Payment Methods:**\n- Credit Card\n- PayPal\n- Invoice`,

  ecommerce_pricing: (query: string) =>
    `[E-Commerce Pricing]\n\nPricing calculation: "${query}"\n\n**Price Breakdown:**\n- Base Price: $99.00\n- Discount (10%): -$9.90\n- Tax (19% VAT): +$16.93\n- **Final Price: $106.03**\n\n**Active Promotions:**\n- Summer Sale: 10% off`,

  customer_manager: (query: string) =>
    `[Customer Manager]\n\nCustomer operation: "${query}"\n\n**Customer Profile:**\n- Name: John Doe\n- Email: john@example.com\n- Segment: Premium\n- Lifetime Value: $2,450\n\n**Recent Activity:**\n- Last login: Today\n- Last purchase: 3 days ago`,

  customer_segments: (query: string) =>
    `[Customer Segments]\n\nSegmentation: "${query}"\n\n**Active Segments:**\n| Segment | Customers | Criteria |\n|---------|-----------|----------|\n| Premium | 1,234 | LTV > $1000 |\n| Newsletter | 5,678 | Subscribed |\n| Inactive | 890 | No login 90d |`,

  data_quality: (query: string) =>
    `[Data Quality]\n\nQuality check: "${query}"\n\n**Data Quality Score: 87%**\n\n**Issues Found:**\n- Missing descriptions: 23 products\n- No images: 12 products\n- Incomplete categories: 5 items\n\n**Recommendations:**\n- Add missing product images\n- Complete category assignments`,

  workflow_manager: (query: string) =>
    `[Workflow Manager]\n\nWorkflow: "${query}"\n\n**Current State:** Draft\n**Available Transitions:**\n- Submit for Review → Pending\n- Publish → Published\n\n**Workflow History:**\n- Created: 2 days ago\n- Last modified: 1 hour ago`,

  automation_runner: (query: string) =>
    `[Automation Runner]\n\nAutomation: "${query}"\n\n**Available Automations:**\n- Product Import (Daily)\n- Image Processing (On Upload)\n- Translation Sync (Hourly)\n\n**Recent Runs:**\n| Job | Status | Duration |\n|-----|--------|----------|\n| Import | ✅ Success | 2m 34s |`,

  targeting_engine: (query: string) =>
    `[Targeting Engine]\n\nTargeting: "${query}"\n\n**Active Rules:**\n1. VIP Customers → Show premium content\n2. New Visitors → Show welcome banner\n3. Cart Abandoners → Show discount popup\n\n**Current Visitor:**\n- Segment: Returning Customer\n- Location: Germany`,

  statistics_reporter: (query: string) =>
    `[Statistics Reporter]\n\nReport: "${query}"\n\n**Dashboard Overview:**\n- Total Products: 5,432\n- Active Users: 1,234\n- Orders Today: 56\n- Revenue: $12,450\n\n**Trends:**\n📈 Sales up 15% vs last week\n📊 Traffic stable`,

  webhook_sender: (query: string) =>
    `[Webhook Sender]\n\nWebhook: "${query}"\n\n**Configured Webhooks:**\n| Event | Endpoint | Status |\n|-------|----------|--------|\n| Product Update | api.erp.com | Active |\n| Order Created | shop.notify | Active |\n\n**Recent Deliveries:** 45 successful, 2 failed`,

  newsletter_manager: (query: string) =>
    `[Newsletter Manager]\n\nNewsletter: "${query}"\n\n**Subscribers:** 12,456\n**Lists:**\n- Weekly Updates (8,234)\n- Product News (5,123)\n- Promotions (9,876)\n\n**Last Campaign:**\n- Sent: Yesterday\n- Open Rate: 24.5%\n- Click Rate: 3.2%`,

  portal_content: (query: string) =>
    `[Portal Content]\n\nPortal: "${query}"\n\n**Portal Sections:**\n- Homepage Hero\n- Product Showcase\n- Featured Categories\n- Customer Reviews\n\n**Templates Available:**\n- Landing Page\n- Product Grid\n- Contact Form`,

  variant_generator: (query: string) =>
    `[Variant Generator]\n\nVariants: "${query}"\n\n**Variant Attributes:**\n- Size: S, M, L, XL\n- Color: Red, Blue, Black\n\n**Generated Variants:** 12\n**SKU Pattern:** PROD-{SIZE}-{COLOR}\n\n**Example:** PROD-M-RED`,
};

/**
 * Get agent details by name
 */
export function getAgent(name: string): Agent | undefined {
  return (agentManifest.agents as unknown as Agent[]).find((a) => a.name === name);
}

/**
 * Execute a delegated request with a mock agent
 */
export async function executeAgent(
  delegation: DelegationResult
): Promise<string> {
  const { agent, arguments: args } = delegation;
  const query = (args.query as string) || 'No query provided';

  // Simulate network delay
  await new Promise((resolve) => setTimeout(resolve, 300 + Math.random() * 400));

  // Get mock response generator
  const responseGenerator = mockResponses[agent];

  if (responseGenerator) {
    return responseGenerator(query);
  }

  // Fallback for unknown agents
  return `[${agent}]\n\nProcessing your request: "${query}"\n\n(Mock response - agent implementation pending)`;
}

/**
 * Get all available agents
 */
export function getAllAgents(): Agent[] {
  return agentManifest.agents as unknown as Agent[];
}
