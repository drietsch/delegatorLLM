import { Divider } from 'antd';
import { SessionToken } from './SessionToken';
import { NewSessionButton } from './NewSessionButton';
import { useMultiSession } from '../../context/MultiSessionContext';
import './PowerStrip.css';

export function PowerStrip() {
  const {
    sessionList,
    activeSessionId,
    createSession,
    switchSession,
    isRouterReady,
  } = useMultiSession();

  const handleNewSession = () => {
    createSession();
  };

  const handleSessionClick = (id: string) => {
    switchSession(id);
  };

  // Separate active/working sessions from completed ones
  const activeSessions = sessionList.filter(
    (s) => s.status !== 'completed' && s.status !== 'error'
  );
  const completedSessions = sessionList.filter(
    (s) => s.status === 'completed' || s.status === 'error'
  );

  return (
    <div className="power-strip">
      <div className="power-strip-content">
        {/* New Session Button */}
        <NewSessionButton onClick={handleNewSession} disabled={!isRouterReady} />

        {/* Divider */}
        <Divider style={{ margin: '12px 0', borderColor: 'rgba(255,255,255,0.2)' }} />

        {/* Active Sessions */}
        <div className="session-list">
          {activeSessions.map((session) => (
            <SessionToken
              key={session.id}
              session={session}
              isActive={session.id === activeSessionId}
              onClick={() => handleSessionClick(session.id)}
            />
          ))}
        </div>

        {/* Completed Sessions (if any) */}
        {completedSessions.length > 0 && (
          <>
            <Divider style={{ margin: '12px 0', borderColor: 'rgba(255,255,255,0.1)' }} />
            <div className="session-list">
              {completedSessions.map((session) => (
                <SessionToken
                  key={session.id}
                  session={session}
                  isActive={session.id === activeSessionId}
                  onClick={() => handleSessionClick(session.id)}
                />
              ))}
            </div>
          </>
        )}
      </div>
    </div>
  );
}
