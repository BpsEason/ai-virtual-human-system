import api from './api';

export const getChatMessages = (characterId) => {
  return api.get(`/chat-messages?character_id=${characterId}`);
};

export const sendChatMessage = (characterId, sender, message) => {
  return api.post('/chat-messages', {
    character_id: characterId,
    sender,
    message,
  });
};
