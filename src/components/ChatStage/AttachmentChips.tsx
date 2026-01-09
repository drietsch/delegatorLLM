import { CloseOutlined, FileImageOutlined, VideoCameraOutlined, FileTextOutlined } from '@ant-design/icons';
import type { Attachment } from '../../types/asset';
import './AttachmentChips.css';

interface AttachmentChipsProps {
  attachments: Attachment[];
  onRemove: (id: string) => void;
}

export function AttachmentChips({ attachments, onRemove }: AttachmentChipsProps) {
  if (attachments.length === 0) return null;

  const getTypeIcon = (type: Attachment['type']) => {
    switch (type) {
      case 'video':
        return <VideoCameraOutlined className="chip-type-icon video" />;
      case 'document':
        return <FileTextOutlined className="chip-type-icon document" />;
      default:
        return <FileImageOutlined className="chip-type-icon image" />;
    }
  };

  return (
    <div className="attachment-chips">
      <div className="chips-scroll">
        {attachments.map((attachment) => (
          <div key={attachment.id} className="attachment-chip">
            {attachment.thumbnail ? (
              <img
                src={attachment.thumbnail}
                alt={attachment.name}
                className="chip-thumbnail"
              />
            ) : (
              getTypeIcon(attachment.type)
            )}
            <span className="chip-name" title={attachment.name}>
              {attachment.name}
            </span>
            <button
              className="chip-remove"
              onClick={() => onRemove(attachment.id)}
              aria-label={`Remove ${attachment.name}`}
            >
              <CloseOutlined />
            </button>
          </div>
        ))}
      </div>
      {attachments.length > 0 && (
        <div className="chips-count">
          {attachments.length} file{attachments.length !== 1 ? 's' : ''} attached
        </div>
      )}
    </div>
  );
}
