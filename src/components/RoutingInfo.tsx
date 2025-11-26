import type { DelegationResult } from '../types';

interface RoutingInfoProps {
  delegation: DelegationResult;
}

export function RoutingInfo({ delegation }: RoutingInfoProps) {
  const { agent, confidence, reason } = delegation;
  const confidencePercent = (confidence * 100).toFixed(0);

  // Color based on confidence
  const getConfidenceColor = () => {
    if (confidence >= 0.8) return 'high';
    if (confidence >= 0.5) return 'medium';
    return 'low';
  };

  return (
    <div className="routing-info">
      <div className="routing-header">
        <span className="agent-badge">{agent}</span>
        <span className={`confidence-badge ${getConfidenceColor()}`}>
          {confidencePercent}% confidence
        </span>
      </div>
      <p className="routing-reason">{reason}</p>
    </div>
  );
}
