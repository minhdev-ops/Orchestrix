import { reactive, readonly } from 'vue';

const state = reactive({
  panelOpen: false,
  activeConversationId: null,
  activeProduct: null,
});

export function useChat() {
  function openPanel(conversationId = null, product = null) {
    if (conversationId) state.activeConversationId = conversationId;
    if (product) state.activeProduct = product;
    state.panelOpen = true;
  }

  function closePanel() {
    state.panelOpen = false;
    state.activeConversationId = null;
    state.activeProduct = null;
  }

  function togglePanel() {
    state.panelOpen = !state.panelOpen;
    if (!state.panelOpen) {
      state.activeConversationId = null;
      state.activeProduct = null;
    }
  }

  function selectConversation(id) {
    state.activeConversationId = id;
  }

  return {
    state: readonly(state),
    openPanel,
    closePanel,
    togglePanel,
    selectConversation,
  };
}
