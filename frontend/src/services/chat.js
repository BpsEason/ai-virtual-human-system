import api from './api';

export const getConversations = () => api.get('/conversations');
export const startConversation = (characterId) => api.post('/conversations/start', { character_id: characterId });
export const getMessages = (conversationId) => api.get(`/conversations/${conversationId}/messages`);
export const sendMessage = (conversationId, content) => api.post(`/conversations/${conversationId}/messages`, { message: content });
