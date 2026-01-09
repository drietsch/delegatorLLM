// Digital Asset Management types

export type AssetType = 'image' | 'video' | 'document' | 'folder';

export interface Asset {
  id: string;
  name: string;
  type: AssetType;
  thumbnail?: string;      // URL for preview
  path: string;            // Full path in tree
  size?: number;           // File size in bytes
  mimeType?: string;
  children?: Asset[];      // For folders
  metadata?: {
    width?: number;
    height?: number;
    duration?: number;     // For videos (seconds)
    createdAt?: string;
  };
}

export interface Attachment {
  id: string;
  assetId: string;
  name: string;
  thumbnail?: string;
  type: AssetType;
  path: string;
}

// Helper to flatten assets from a folder
export function flattenAssets(assets: Asset[]): Asset[] {
  const result: Asset[] = [];

  function traverse(items: Asset[]) {
    for (const item of items) {
      if (item.type === 'folder' && item.children) {
        traverse(item.children);
      } else if (item.type !== 'folder') {
        result.push(item);
      }
    }
  }

  traverse(assets);
  return result;
}

// Convert Asset to Attachment
export function assetToAttachment(asset: Asset): Attachment {
  return {
    id: `attachment-${asset.id}-${Date.now()}`,
    assetId: asset.id,
    name: asset.name,
    thumbnail: asset.thumbnail,
    type: asset.type,
    path: asset.path,
  };
}
