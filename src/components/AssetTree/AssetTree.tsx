import { useState, useCallback } from 'react';
import { Tree, Input, Flex, Typography } from 'antd';
import {
  FolderOutlined,
  FolderOpenOutlined,
  FileImageOutlined,
  VideoCameraOutlined,
  FileTextOutlined,
  SearchOutlined,
} from '@ant-design/icons';
import type { TreeDataNode, TreeProps } from 'antd';
import type { Asset } from '../../types/asset';
import { mockAssets } from '../../data/mockAssets';
import { flattenAssets } from '../../types/asset';
import './AssetTree.css';

const { Text } = Typography;

// Convert Asset to TreeDataNode
function assetToTreeNode(asset: Asset): TreeDataNode & { asset: Asset } {
  const isFolder = asset.type === 'folder';

  const getIcon = () => {
    switch (asset.type) {
      case 'folder':
        return null; // Will use switcherIcon
      case 'video':
        return <VideoCameraOutlined className="asset-type-icon video" />;
      case 'document':
        return <FileTextOutlined className="asset-type-icon document" />;
      default:
        return <FileImageOutlined className="asset-type-icon image" />;
    }
  };

  return {
    key: asset.id,
    title: (
      <div className="asset-node">
        {asset.thumbnail && asset.type !== 'folder' && (
          <img src={asset.thumbnail} alt={asset.name} className="asset-thumbnail" />
        )}
        {!asset.thumbnail && asset.type !== 'folder' && getIcon()}
        <span className="asset-name">{asset.name}</span>
      </div>
    ),
    icon: isFolder ? undefined : getIcon(),
    children: asset.children?.map(assetToTreeNode),
    isLeaf: !isFolder,
    asset,
  };
}

// Custom drag image
function createDragImage(assets: Asset[]): HTMLElement {
  const div = document.createElement('div');
  div.className = 'drag-ghost';

  if (assets.length === 1) {
    const asset = assets[0];
    if (asset.thumbnail) {
      div.innerHTML = `<img src="${asset.thumbnail}" /><span>${asset.name}</span>`;
    } else {
      div.innerHTML = `<span>${asset.name}</span>`;
    }
  } else {
    div.innerHTML = `<span>${assets.length} items</span>`;
  }

  document.body.appendChild(div);
  return div;
}

export function AssetTree() {
  const [expandedKeys, setExpandedKeys] = useState<React.Key[]>(['folder-marketing', 'folder-products']);
  const [selectedKeys, setSelectedKeys] = useState<React.Key[]>([]);
  const [searchValue, setSearchValue] = useState('');
  const [assetMap] = useState(() => {
    const map = new Map<string, Asset>();
    function traverse(assets: Asset[]) {
      for (const asset of assets) {
        map.set(asset.id, asset);
        if (asset.children) traverse(asset.children);
      }
    }
    traverse(mockAssets);
    return map;
  });

  const treeData = mockAssets.map(assetToTreeNode);

  const onExpand: TreeProps['onExpand'] = (keys) => {
    setExpandedKeys(keys);
  };

  const onSelect: TreeProps['onSelect'] = (keys) => {
    setSelectedKeys(keys);
  };

  const handleDragStart = useCallback((e: React.DragEvent, info: { node: TreeDataNode & { asset?: Asset } }) => {
    const draggedAsset = info.node.asset;
    if (!draggedAsset) return;

    // Get all selected assets, or just the dragged one
    let assetsToTransfer: Asset[] = [];

    if (selectedKeys.includes(draggedAsset.id)) {
      // Dragging from selection - include all selected
      assetsToTransfer = selectedKeys
        .map(key => assetMap.get(key as string))
        .filter((a): a is Asset => a !== undefined);
    } else {
      // Dragging unselected item - just that one
      assetsToTransfer = [draggedAsset];
    }

    // If dragging a folder, include all children
    const expandedAssets: Asset[] = [];
    for (const asset of assetsToTransfer) {
      if (asset.type === 'folder' && asset.children) {
        expandedAssets.push(...flattenAssets([asset]));
      } else {
        expandedAssets.push(asset);
      }
    }

    // Set drag data
    e.dataTransfer.setData('application/json', JSON.stringify(expandedAssets));
    e.dataTransfer.effectAllowed = 'copy';

    // Custom drag image
    const ghost = createDragImage(assetsToTransfer);
    e.dataTransfer.setDragImage(ghost, 0, 0);
    setTimeout(() => ghost.remove(), 0);
  }, [selectedKeys, assetMap]);

  // Filter tree based on search
  const filterTree = (nodes: (TreeDataNode & { asset: Asset })[], search: string): (TreeDataNode & { asset: Asset })[] => {
    if (!search) return nodes;

    const lowerSearch = search.toLowerCase();

    return nodes.reduce<(TreeDataNode & { asset: Asset })[]>((acc, node) => {
      const asset = node.asset;
      const matches = asset.name.toLowerCase().includes(lowerSearch);

      if (asset.type === 'folder' && node.children) {
        const filteredChildren = filterTree(node.children as (TreeDataNode & { asset: Asset })[], search);
        if (filteredChildren.length > 0 || matches) {
          acc.push({
            ...node,
            children: filteredChildren.length > 0 ? filteredChildren : node.children,
          });
        }
      } else if (matches) {
        acc.push(node);
      }

      return acc;
    }, []);
  };

  const filteredTreeData = filterTree(treeData as (TreeDataNode & { asset: Asset })[], searchValue);

  return (
    <div className="asset-tree-container">
      <div className="asset-tree-header">
        <Flex align="center" justify="space-between" style={{ marginBottom: 12 }}>
          <Text strong style={{ fontSize: 14, color: '#1e293b' }}>Digital Assets</Text>
          <Text type="secondary" style={{ fontSize: 11 }}>Drag to chat</Text>
        </Flex>
        <Input
          placeholder="Search assets..."
          prefix={<SearchOutlined style={{ color: '#94a3b8' }} />}
          value={searchValue}
          onChange={(e) => setSearchValue(e.target.value)}
          allowClear
          size="small"
          style={{ borderRadius: 8 }}
        />
      </div>
      <div className="asset-tree-content">
        <Tree
          showLine={{ showLeafIcon: false }}
          showIcon
          switcherIcon={({ expanded }) =>
            expanded ? <FolderOpenOutlined className="folder-icon" /> : <FolderOutlined className="folder-icon" />
          }
          expandedKeys={expandedKeys}
          selectedKeys={selectedKeys}
          onExpand={onExpand}
          onSelect={onSelect}
          treeData={filteredTreeData}
          multiple
          draggable={{
            icon: false,
            nodeDraggable: () => true,
          }}
          onDragStart={({ event, node }) => handleDragStart(event as unknown as React.DragEvent, { node: node as TreeDataNode & { asset?: Asset } })}
        />
      </div>
    </div>
  );
}
