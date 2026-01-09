// API configuration - uses relative URLs in production, localhost in development
export const API_BASE = import.meta.env.PROD
  ? ''
  : 'http://localhost:3001';
