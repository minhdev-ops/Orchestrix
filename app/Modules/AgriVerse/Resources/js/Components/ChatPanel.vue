<template>
  <Transition name="chat-panel">
    <div v-if="state.panelOpen" class="chat-overlay" @click.self="closePanel">
      <div class="chat-panel" @click.stop>
        <!-- ===== LEFT SIDEBAR ===== -->
        <div class="chat-sidebar" :class="{ 'chat-sidebar--hidden': activeChatId }">
          <div class="chat-sidebar-header">
            <span class="chat-sidebar-title">Chat</span>
            <button class="chat-header-btn" @click="closePanel">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <!-- Tabs -->
          <div class="chat-tabs">
            <button
              class="chat-tab"
              :class="{ 'chat-tab--active': activeTab === 'messages' }"
              @click="activeTab = 'messages'"
            >Tin nhắn</button>
            <button
              class="chat-tab"
              :class="{ 'chat-tab--active': activeTab === 'groups' }"
              @click="activeTab = 'groups'"
            >Cộng đồng</button>
          </div>

          <!-- Search (messages tab) -->
          <div v-if="activeTab === 'messages'" class="chat-search-wrap">
            <span class="material-symbols-outlined chat-search-icon">search</span>
            <input v-model="searchQuery" class="chat-search" placeholder="Tìm kiếm trên Chat" />
          </div>

          <!-- Groups tab header -->
          <div v-if="activeTab === 'groups'" class="chat-create-group-wrap">
            <button class="chat-create-group-btn" @click="fetchGroups">
              <span class="material-symbols-outlined">refresh</span>
              Làm mới
            </button>
          </div>

          <div class="chat-sidebar-body">
            <!-- Conversation List -->
            <template v-if="activeTab === 'messages'">
              <div v-if="loading" class="chat-loading">Đang tải...</div>
              <div v-else-if="filteredConversations.length === 0" class="chat-empty">
                <span class="material-symbols-outlined" style="font-size:48px;opacity:0.3">chat</span>
                <p>Chưa có tin nhắn nào</p>
              </div>
              <div v-else class="chat-conv-list">
                <div
                  v-for="conv in filteredConversations"
                  :key="'c' + conv.id"
                  class="chat-conv-item"
                  :class="{ 'chat-conv-item--active': activeChatId === 'c' + conv.id }"
                  @click="openConversation(conv)"
                >
                  <div class="chat-avatar">
                    <span class="chat-avatar-text">{{ conv.other_user?.name?.charAt(0)?.toUpperCase() || '?' }}</span>
                  </div>
                  <div class="chat-conv-info">
                    <div class="chat-conv-top">
                      <span class="chat-conv-name">{{ conv.other_user?.name || 'Người dùng' }}</span>
                      <span class="chat-conv-time">{{ conv.last_message_at }}</span>
                    </div>
                    <div class="chat-conv-bottom">
                      <span class="chat-conv-preview">{{ conv.last_message || 'Chưa có tin nhắn' }}</span>
                      <span v-if="conv.unread > 0" class="chat-badge">{{ conv.unread }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- Group List -->
            <template v-if="activeTab === 'groups'">
              <div v-if="loadingGroups" class="chat-loading">Đang tải...</div>
              <div v-else-if="groups.length === 0" class="chat-empty">
                <span class="material-symbols-outlined" style="font-size:48px;opacity:0.3">groups</span>
                <p>Chưa tham gia nhóm nào</p>
              </div>
                  <div v-else class="chat-conv-list">
                <div
                  v-for="g in groups"
                  :key="'g' + g.id"
                  class="chat-conv-item"
                  :class="{
                    'chat-conv-item--active': activeChatId === 'g' + g.id,
                    'chat-conv-item--pending': g.is_pending,
                  }"
                  @click="openGroup(g)"
                >
                  <div class="chat-avatar chat-avatar--group">
                    <span class="material-symbols-outlined" style="font-size:22px">groups</span>
                  </div>
                  <div class="chat-conv-info">
                    <div class="chat-conv-top">
                      <span class="chat-conv-name">{{ g.name }}</span>
                      <span class="chat-conv-time">{{ g.member_count }} thành viên</span>
                    </div>
                    <div class="chat-conv-bottom">
                      <span class="chat-conv-preview">{{ g.created_by }}</span>
                      <span v-if="g.is_pending" class="chat-badge-pending">Chờ duyệt</span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- ===== RIGHT: Private Chat ===== -->
        <div v-if="activeChatId?.startsWith('c')" class="chat-main">
          <div class="chat-main-header">
            <button class="chat-back-btn" @click="activeChatId = null">
              <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <div class="chat-main-user">
              <div class="chat-avatar chat-avatar--sm">
                <span class="chat-avatar-text">{{ activeConv?.other_user?.name?.charAt(0)?.toUpperCase() || '?' }}</span>
              </div>
              <div class="chat-main-user-info">
                <span class="chat-main-user-name">{{ activeConv?.other_user?.name || 'Người dùng' }}</span>
              </div>
            </div>
            <div class="chat-main-actions">
              <button class="chat-header-btn" @click="closePanel">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>
          </div>
          <MessageArea
            ref="messageAreaRef"
            :loading="messagesLoading"
            :has-more="hasMore"
            :loading-older="isLoadingMore"
            @load-older="loadOlderMessages"
          >
            <div
              v-for="msg in chatMessages"
              :key="msg.id"
              class="chat-msg-row"
              :class="msg.is_mine ? 'chat-msg-row--mine' : 'chat-msg-row--theirs'"
            >
              <div class="chat-bubble" :class="{ 'chat-bubble--mine': msg.is_mine }">
                <span class="chat-bubble-text">{{ msg.message }}</span>
                <div class="chat-bubble-meta">
                  <span class="chat-bubble-time">{{ formatTime(msg.created_at) }}</span>
                  <span v-if="msg.is_mine && !msg.is_sending" class="material-symbols-outlined chat-seen-icon">done_all</span>
                  <span v-if="msg.is_sending" class="material-symbols-outlined chat-seen-icon chat-seen-icon--sending">schedule</span>
                </div>
              </div>
            </div>
          </MessageArea>
          <div class="chat-input-wrap">
            <div class="chat-input-target">
              <span class="material-symbols-outlined" style="font-size:14px;">chat</span>
              <span class="chat-input-target-text">{{ activeConv?.other_user?.name || 'Người dùng' }}</span>
            </div>
            <div class="chat-input-box">
              <input
                ref="inputRef"
                v-model="newMessage"
                class="chat-input"
                placeholder="Nhập tin nhắn..."
                @keyup.enter="sendMessage"
                :disabled="sending"
              />
              <button class="chat-send-btn" @click="sendMessage" :disabled="sending || !newMessage.trim()">
                <span class="material-symbols-outlined">send</span>
              </button>
            </div>
          </div>
        </div>

        <!-- ===== RIGHT: Group Chat ===== -->
        <div v-if="activeChatId?.startsWith('g')" class="chat-main">
          <div class="chat-main-header">
            <button class="chat-back-btn" @click="activeChatId = null">
              <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <div class="chat-main-user">
              <div class="chat-avatar chat-avatar--sm chat-avatar--group">
                <span class="material-symbols-outlined" style="font-size:18px">groups</span>
              </div>
              <div class="chat-main-user-info">
                <span class="chat-main-user-name">{{ activeGroup?.name }}</span>
                <span class="chat-main-user-status">{{ activeGroup?.member_count }} thành viên</span>
              </div>
            </div>
            <div class="chat-main-actions">
              <button v-if="activeGroup?.is_creator" class="chat-header-btn" title="Thêm thành viên" @click="showAddMember = true">
                <span class="material-symbols-outlined">person_add</span>
              </button>
              <button class="chat-header-btn" @click="closePanel">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>
          </div>

          <!-- Pending state -->
          <div v-if="activeGroup?.is_pending" class="chat-pending-state">
            <span class="material-symbols-outlined" style="font-size:48px;opacity:0.3">hourglass_empty</span>
            <h3>Yêu cầu đang chờ duyệt</h3>
            <p>Vui lòng chờ admin duyệt yêu cầu tham gia nhóm của bạn</p>
          </div>

          <template v-else>
            <MessageArea
              ref="messageAreaRef"
              :loading="groupMessagesLoading"
              :has-more="groupHasMore"
              :loading-older="groupLoadingOlder"
              @load-older="loadOlderGroupMessages"
            >
              <div
                v-for="msg in groupMessages"
                :key="msg.id"
                class="chat-msg-row"
                :class="msg.is_mine ? 'chat-msg-row--mine' : 'chat-msg-row--theirs'"
              >
                <div class="chat-bubble" :class="{ 'chat-bubble--mine': msg.is_mine }">
                  <span v-if="!msg.is_mine" class="chat-bubble-sender">{{ msg.sender_name }}</span>
                  <span class="chat-bubble-text">{{ msg.message }}</span>
                  <div class="chat-bubble-meta">
                    <span class="chat-bubble-time">{{ formatTime(msg.created_at) }}</span>
                  </div>
                </div>
              </div>
            </MessageArea>
            <div class="chat-input-wrap">
              <div class="chat-input-target">
                <span class="material-symbols-outlined" style="font-size:14px;">groups</span>
                <span class="chat-input-target-text">{{ activeGroup?.name || 'Nhóm' }}</span>
              </div>
              <div class="chat-input-box">
                <input
                  ref="inputRef"
                  v-model="newMessage"
                  class="chat-input"
                  placeholder="Nhập tin nhắn..."
                  @keyup.enter="sendGroupMessage"
                  :disabled="groupSending"
                />
                <button class="chat-send-btn" @click="sendGroupMessage" :disabled="groupSending || !newMessage.trim()">
                  <span class="material-symbols-outlined">send</span>
                </button>
              </div>
            </div>
          </template>
        </div>

        <!-- ===== EMPTY STATE ===== -->
        <div v-if="!activeChatId" class="chat-main chat-main--empty">
          <div class="chat-empty-state">
            <span class="material-symbols-outlined" style="font-size:72px;opacity:0.15">chat</span>
            <h3>Tin nhắn của bạn</h3>
            <p>Chọn một cuộc trò chuyện để bắt đầu</p>
          </div>
        </div>

        <!-- Create Group Modal -->
        <Teleport to="body">
          <div v-if="showCreateGroup" class="chat-overlay" @click.self="showCreateGroup = false">
            <div class="chat-modal">
              <div class="chat-modal-header">
                <span class="chat-modal-title">Tạo nhóm mới</span>
                <button class="chat-header-btn" @click="showCreateGroup = false">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>
              <div class="chat-modal-body">
                <input
                  v-model="newGroupName"
                  class="chat-modal-input"
                  placeholder="Nhập tên nhóm..."
                  @keyup.enter="createGroup"
                  maxlength="100"
                />
              </div>
              <div class="chat-modal-footer">
                <button class="chat-modal-cancel" @click="showCreateGroup = false">Hủy</button>
                <button class="chat-modal-confirm" @click="createGroup" :disabled="!newGroupName.trim()">Tạo</button>
              </div>
            </div>
          </div>
        </Teleport>

        <!-- Add Member Modal -->
        <Teleport to="body">
          <div v-if="showAddMember" class="chat-overlay" @click.self="showAddMember = false">
            <div class="chat-modal">
              <div class="chat-modal-header">
                <span class="chat-modal-title">Thêm thành viên</span>
                <button class="chat-header-btn" @click="showAddMember = false">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>
              <div class="chat-modal-body">
                <input
                  v-model="searchUserQuery"
                  class="chat-modal-input"
                  placeholder="Tìm kiếm người dùng..."
                  @input="searchUsers"
                />
                <div v-if="searchingUsers" class="chat-loading" style="padding:12px">Đang tìm...</div>
                <div v-else-if="searchUserResults.length > 0" class="chat-user-list">
                  <div
                    v-for="u in searchUserResults"
                    :key="u.id"
                    class="chat-user-item"
                    @click="addMember(u)"
                  >
                    <div class="chat-avatar chat-avatar--xs">
                      <span class="chat-avatar-text">{{ u.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <span class="chat-user-name">{{ u.name }}</span>
                  </div>
                </div>
                <div v-else-if="searchUserQuery.length >= 2" class="chat-loading" style="padding:12px">Không tìm thấy người dùng</div>
              </div>
            </div>
          </div>
        </Teleport>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { useChat } from '@agriverse/Composables/useChat'
import { useChatSocket } from '@agriverse/Composables/useChatSocket'
import MessageArea from './MessageArea.vue'

// CSRF token cho các POST request dạng web
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token
}

const { state, closePanel, selectConversation } = useChat()
const { connect, on, off, connected } = useChatSocket()
const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content')

const activeTab = ref('messages')

// Private chat
const conversations = ref([])
const chatMessages = ref([])
const loading = ref(false)
const messagesLoading = ref(false)
const isLoadingMore = ref(false)
const hasMore = ref(true)
const page = ref(1)

// Group chat
const groups = ref([])
const groupMessages = ref([])
const loadingGroups = ref(false)
const groupMessagesLoading = ref(false)
const groupLoadingOlder = ref(false)
const groupHasMore = ref(true)
const groupPage = ref(1)

const sending = ref(false)
const groupSending = ref(false)
const newMessage = ref('')
const messagesRef = ref(null)
const inputRef = ref(null)
const searchQuery = ref('')
const messageAreaRef = ref(null)

const showCreateGroup = ref(false)
const newGroupName = ref('')

const showAddMember = ref(false)
const searchUserQuery = ref('')
const searchUserResults = ref([])
const searchingUsers = ref(false)

// activeChatId = 'c{id}' for conversation, 'g{id}' for group
const activeChatId = ref(null)

const activeConv = computed(() =>
  conversations.value.find(c => 'c' + c.id === activeChatId.value) || null
)

const activeGroup = computed(() =>
  groups.value.find(g => 'g' + g.id === activeChatId.value) || null
)

const filteredConversations = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return conversations.value
  return conversations.value.filter(c =>
    c.other_user?.name?.toLowerCase().includes(q) ||
    c.last_message?.toLowerCase().includes(q)
  )
})

watch(() => state.activeConversationId, (id) => {
  if (id) {
    activeTab.value = 'messages'
    resetChat()
    activeChatId.value = 'c' + id
    fetchMessages()
  }
})

watch(() => state.panelOpen, (open) => {
  if (open) {
    fetchConversations()
    fetchGroups()
    if (state.activeConversationId) {
      activeTab.value = 'messages'
      resetChat()
      activeChatId.value = 'c' + state.activeConversationId
      fetchMessages()
    }
  } else {
    activeChatId.value = null
    stopGroupPolling()
  }
})

watch(activeChatId, (id) => {
  if (!id?.startsWith('g')) {
    stopGroupPolling()
  }
})

const handleIncomingMessage = async (data) => {
  if (!data) return
  if (data.type === 'private' && state.panelOpen) {
    const conv = conversations.value.find(c =>
      String(data.from) === String(c.buyer_id) ||
      String(data.from) === String(c.seller_id)
    )
    if (conv && activeChatId.value === 'c' + conv.id) {
      const isOwn = String(data.from) === userId
      if (!isOwn) {
        chatMessages.value.push({
          id: 'ws-' + Date.now(),
          message: data.content,
          is_mine: false,
          created_at: new Date(data.timestamp || Date.now()).toISOString()
        })
        await nextTick()
        scrollToBottom()
      }
    }
  }
  if (data.type === 'group' && state.panelOpen) {
    const isOwn = String(data.from) === userId
    if (activeChatId.value === 'g' + data.to && !isOwn) {
      const senderName = groupMembersMap.value[data.from] || 'Thành viên'
      groupMessages.value.push({
        id: 'ws-' + Date.now(),
        message: data.content,
        sender_name: senderName,
        is_mine: false,
        created_at: new Date(data.timestamp || Date.now()).toISOString()
      })
      await nextTick()
      scrollToBottom()
    }
  }
}

onMounted(() => {
  connect()
  on('message', handleIncomingMessage)
})

const groupMembersMap = ref({})
let pollTimer = null

function startGroupPolling() {
  stopGroupPolling()
  if (!connected.value) {
    pollTimer = setInterval(fetchGroupMessages, 5000)
  }
}

function stopGroupPolling() {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
}

onBeforeUnmount(() => {
  off('message', handleIncomingMessage)
  stopGroupPolling()
})

function resetChat() {
  chatMessages.value = []
  groupMessages.value = []
  page.value = 1
  hasMore.value = true
  isLoadingMore.value = false
  groupPage.value = 1
  groupHasMore.value = true
  groupLoadingOlder.value = false
}

async function fetchConversations() {
  loading.value = true
  try {
    const { data } = await axios.get('/agriverse/api/chat/conversations')
    conversations.value = data.conversations || []
  } catch {
    conversations.value = []
  } finally {
    loading.value = false
  }
}

async function fetchGroups() {
  loadingGroups.value = true
  try {
    const { data } = await axios.get('/agriverse/api/chat/groups')
    groups.value = data.groups || []
  } catch {
    groups.value = []
  } finally {
    loadingGroups.value = false
  }
}

// ===== Private Conversation =====

async function openConversation(conv) {
  resetChat()
  activeChatId.value = 'c' + conv.id
  selectConversation(conv.id)
  activeTab.value = 'messages'
  await fetchMessages()
  inputRef.value?.focus()
}

async function fetchMessages() {
  if (!activeChatId.value?.startsWith('c')) return
  messagesLoading.value = true
  try {
    const convId = activeChatId.value.slice(1)
    const { data } = await axios.get(`/agriverse/api/chat/${convId}/messages`, {
      params: { page: page.value }
    })
    chatMessages.value = data.messages || []
    hasMore.value = data.has_more ?? false
    await nextTick()
    scrollToBottom()
  } catch {
    chatMessages.value = []
    hasMore.value = false
  } finally {
    messagesLoading.value = false
  }
}

async function loadOlderMessages() {
  if (!hasMore.value || isLoadingMore.value || messagesLoading.value) return
  isLoadingMore.value = true
  page.value++
  const prevScrollHeight = messageAreaRef.value?.$el?.scrollHeight || 0
  try {
    const convId = activeChatId.value.slice(1)
    const { data } = await axios.get(`/agriverse/api/chat/${convId}/messages`, {
      params: { page: page.value }
    })
    const msgs = data.messages || []
    hasMore.value = data.has_more ?? false
    if (msgs.length > 0) {
      chatMessages.value = [...msgs, ...chatMessages.value]
      await nextTick()
      if (messageAreaRef.value?.$el) {
        messageAreaRef.value.$el.scrollTop = messageAreaRef.value.$el.scrollHeight - prevScrollHeight
      }
    }
  } catch {
    page.value--
  } finally {
    isLoadingMore.value = false
  }
}

async function sendMessage() {
  const text = newMessage.value.trim()
  if (!text || sending.value || !activeChatId.value?.startsWith('c')) return

  const tempId = 'temp-' + Date.now()
  chatMessages.value.push({
    id: tempId,
    message: text,
    is_mine: true,
    created_at: new Date().toISOString(),
    is_sending: true
  })
  newMessage.value = ''
  await nextTick()
  scrollToBottom()
  inputRef.value?.focus()

  const convId = activeChatId.value.slice(1)
  sending.value = true
  try {
    const { data } = await axios.post(`/agriverse/api/chat/${convId}/send`, { message: text })
    const idx = chatMessages.value.findIndex(m => m.id === tempId)
    if (idx !== -1) chatMessages.value[idx] = data.message
    await fetchConversations()
  } catch {
    const idx = chatMessages.value.findIndex(m => m.id === tempId)
    if (idx !== -1) chatMessages.value.splice(idx, 1)
  } finally {
    sending.value = false
    await nextTick()
    inputRef.value?.focus()
  }
}

// ===== Group Chat =====

async function openGroup(g) {
  resetChat()
  activeChatId.value = 'g' + g.id
  activeTab.value = 'groups'
  if (g.is_pending) return
  await Promise.all([
    fetchGroupMessages(),
    loadGroupMembers(g.id),
  ])
  inputRef.value?.focus()
  startGroupPolling()
}

async function loadGroupMembers(groupId) {
  try {
    const { data } = await axios.get(`/agriverse/api/chat/groups/${groupId}/members`)
    if (data.members) {
      const map = {}
      for (const m of data.members) {
        map[m.id] = m.name
      }
      groupMembersMap.value = map
    }
  } catch {
    // ignore
  }
}

async function fetchGroupMessages() {
  if (!activeChatId.value?.startsWith('g')) return
  groupMessagesLoading.value = true
  try {
    const groupId = activeChatId.value.slice(1)
    const { data } = await axios.get(`/agriverse/api/chat/groups/${groupId}/messages`, {
      params: { page: groupPage.value }
    })
    groupMessages.value = data.messages || []
    groupHasMore.value = data.has_more ?? false
    await nextTick()
    scrollToBottom()
  } catch {
    groupMessages.value = []
    groupHasMore.value = false
  } finally {
    groupMessagesLoading.value = false
  }
}

async function loadOlderGroupMessages() {
  if (!groupHasMore.value || groupLoadingOlder.value || groupMessagesLoading.value) return
  groupLoadingOlder.value = true
  groupPage.value++
  const prevScrollHeight = messageAreaRef.value?.$el?.scrollHeight || 0
  try {
    const groupId = activeChatId.value.slice(1)
    const { data } = await axios.get(`/agriverse/api/chat/groups/${groupId}/messages`, {
      params: { page: groupPage.value }
    })
    const msgs = data.messages || []
    groupHasMore.value = data.has_more ?? false
    if (msgs.length > 0) {
      groupMessages.value = [...msgs, ...groupMessages.value]
      await nextTick()
      if (messageAreaRef.value?.$el) {
        messageAreaRef.value.$el.scrollTop = messageAreaRef.value.$el.scrollHeight - prevScrollHeight
      }
    }
  } catch {
    groupPage.value--
  } finally {
    groupLoadingOlder.value = false
  }
}

async function sendGroupMessage() {
  const text = newMessage.value.trim()
  if (!text || groupSending.value || !activeChatId.value?.startsWith('g')) return

  const tempId = 'temp-' + Date.now()
  groupMessages.value.push({
    id: tempId,
    message: text,
    sender_name: 'Tôi',
    is_mine: true,
    created_at: new Date().toISOString(),
    is_sending: true
  })
  newMessage.value = ''
  await nextTick()
  scrollToBottom()
  inputRef.value?.focus()

  const groupId = activeChatId.value.slice(1)
  groupSending.value = true
  try {
    const { data } = await axios.post(`/agriverse/api/chat/groups/${groupId}/send`, { message: text })
    const idx = groupMessages.value.findIndex(m => m.id === tempId)
    if (idx !== -1) groupMessages.value[idx] = data.message
    await fetchGroups()
  } catch (e) {
    console.error('[GroupChat] Send failed:', e.response?.status, e.response?.data)
    const idx = groupMessages.value.findIndex(m => m.id === tempId)
    if (idx !== -1) groupMessages.value.splice(idx, 1)
  } finally {
    groupSending.value = false
    await nextTick()
    inputRef.value?.focus()
  }
}

async function createGroup() {
  const name = newGroupName.value.trim()
  if (!name) return
  try {
    await axios.post('/agriverse/api/chat/groups', { name })
    showCreateGroup.value = false
    newGroupName.value = ''
    activeTab.value = 'groups'
    await fetchGroups()
  } catch {
    // ignore
  }
}

let searchTimeout = null
function searchUsers() {
  clearTimeout(searchTimeout)
  const q = searchUserQuery.value.trim()
  if (q.length < 2) {
    searchUserResults.value = []
    return
  }
  searchingUsers.value = true
  searchTimeout = setTimeout(async () => {
    try {
      const { data } = await axios.get('/agriverse/api/chat/search-users', { params: { q } })
      searchUserResults.value = data.users || []
    } catch {
      searchUserResults.value = []
    } finally {
      searchingUsers.value = false
    }
  }, 300)
}

async function addMember(user) {
  if (!activeChatId.value?.startsWith('g')) return
  const groupId = activeChatId.value.slice(1)
  try {
    await axios.post(`/agriverse/api/chat/groups/${groupId}/add-member`, { user_id: user.id })
    showAddMember.value = false
    searchUserQuery.value = ''
    searchUserResults.value = []
    await fetchGroups()
  } catch (e) {
    const msg = e.response?.data?.error || 'Không thể thêm thành viên'
    alert(msg)
  }
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messageAreaRef.value?.$el) {
      messageAreaRef.value.$el.scrollTop = messageAreaRef.value.$el.scrollHeight
    }
  })
}

function formatTime(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  const now = new Date()
  const isToday = d.toDateString() === now.toDateString()
  if (isToday) {
    return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
  }
  const yesterday = new Date(now)
  yesterday.setDate(yesterday.getDate() - 1)
  if (d.toDateString() === yesterday.toDateString()) {
    return 'Hôm qua ' + d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
  }
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}
</script>

<style scoped>
/* ===== Overlay & Panel ===== */
.chat-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.4);
}

.chat-panel {
  width: 840px;
  max-width: 95vw;
  height: 640px;
  max-height: 90vh;
  background: #fff;
  border-radius: 16px;
  display: flex;
  overflow: hidden;
  box-shadow: 0 8px 48px rgba(0, 0, 0, 0.2);
}

/* ===== Left Sidebar ===== */
.chat-sidebar {
  width: 300px;
  min-width: 300px;
  border-right: 1px solid #e5e5e5;
  display: flex;
  flex-direction: column;
  background: #fff;
}

.chat-sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 16px 0;
}

.chat-sidebar-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
}

.chat-header-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
}

.chat-header-btn:hover {
  background: #f0f0f0;
}

/* Tabs */
.chat-tabs {
  display: flex;
  padding: 12px 16px 0;
  gap: 4px;
}

.chat-tab {
  flex: 1;
  padding: 8px 0;
  border: none;
  background: transparent;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.chat-tab:hover {
  background: #f5f5f5;
}

.chat-tab--active {
  background: #e8f0fe;
  color: #0084ff;
  font-weight: 600;
}

/* Search */
.chat-search-wrap {
  position: relative;
  padding: 12px 16px;
}

.chat-search-icon {
  position: absolute;
  left: 24px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #999;
  pointer-events: none;
}

.chat-search {
  width: 100%;
  padding: 8px 12px 8px 36px;
  border: 1px solid #e5e5e5;
  border-radius: 20px;
  font-size: 13px;
  outline: none;
  background: #f5f5f5;
  box-sizing: border-box;
  transition: background 0.15s, border-color 0.15s;
}

.chat-search:focus {
  background: #fff;
  border-color: #0084ff;
}

/* Create group button */
.chat-create-group-wrap {
  padding: 12px 16px;
}

.chat-create-group-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  border: 1px dashed #ccc;
  border-radius: 8px;
  background: transparent;
  font-size: 13px;
  color: #666;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
}

.chat-create-group-btn:hover {
  background: #f5f5f5;
  border-color: #0084ff;
  color: #0084ff;
}

/* Sidebar body */
.chat-sidebar-body {
  flex: 1;
  overflow-y: auto;
}

.chat-loading {
  padding: 24px;
  text-align: center;
  color: #999;
  font-size: 13px;
}

.chat-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  gap: 8px;
  color: #999;
  font-size: 13px;
  padding: 24px;
}

/* Conversation/Group items */
.chat-conv-list {
  padding: 4px 0;
}

.chat-conv-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  cursor: pointer;
  transition: background 0.1s;
}

.chat-conv-item:hover {
  background: #f5f5f5;
}

.chat-conv-item--active {
  background: #e8f0fe;
}

.chat-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #0084ff;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  flex-shrink: 0;
}

.chat-avatar--sm {
  width: 36px;
  height: 36px;
  font-size: 14px;
}

.chat-avatar--group {
  background: #42b72a;
}

.chat-avatar-text {
  line-height: 1;
}

.chat-conv-info {
  flex: 1;
  min-width: 0;
}

.chat-conv-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 4px;
}

.chat-conv-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.chat-conv-time {
  font-size: 11px;
  color: #999;
  white-space: nowrap;
}

.chat-conv-bottom {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}

.chat-conv-preview {
  font-size: 13px;
  color: #666;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  flex: 1;
}

.chat-badge {
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 9px;
  background: #e41e3f;
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.chat-badge-pending {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 9999px;
  background: #fef3c7;
  color: #d97706;
  white-space: nowrap;
}
.chat-conv-item--pending {
  opacity: 0.6;
  pointer-events: none;
}

/* ===== Right Main Area ===== */
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #fff;
  min-width: 0;
}

.chat-main--empty {
  align-items: center;
  justify-content: center;
}

.chat-empty-state {
  text-align: center;
  color: #999;
}

.chat-empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 16px 0 4px;
}

.chat-empty-state p {
  font-size: 14px;
  color: #666;
}

.chat-pending-state {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #999;
  padding: 40px 24px;
}
.chat-pending-state h3 {
  font-size: 16px;
  font-weight: 600;
  color: #d97706;
  margin: 12px 0 4px;
}
.chat-pending-state p {
  font-size: 13px;
  color: #999;
  max-width: 240px;
}

/* Main Header */
.chat-main-header {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e5e5;
  gap: 12px;
  flex-shrink: 0;
}

.chat-back-btn {
  display: none;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  border-radius: 50%;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  color: #666;
}

.chat-back-btn:hover {
  background: #f0f0f0;
}

.chat-main-user {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
  min-width: 0;
}

.chat-main-user-info {
  min-width: 0;
}

.chat-main-user-name {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.chat-main-user-status {
  font-size: 12px;
  color: #999;
}

.chat-main-actions {
  display: flex;
  gap: 4px;
}

/* Bubbles */
.chat-msg-row {
  display: flex;
  margin-bottom: 4px;
}

.chat-msg-row--mine {
  justify-content: flex-end;
}

.chat-msg-row--theirs {
  justify-content: flex-start;
}

.chat-bubble {
  max-width: 70%;
  padding: 8px 14px;
  border-radius: 18px;
  background: #fff;
  color: #1a1a1a;
  font-size: 14px;
  line-height: 1.4;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
  word-wrap: break-word;
}

.chat-bubble--mine {
  background: #0084ff;
  color: #fff;
  border-bottom-right-radius: 4px;
}

.chat-msg-row--theirs .chat-bubble {
  border-bottom-left-radius: 4px;
}

.chat-bubble-sender {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #42b72a;
  margin-bottom: 2px;
}

.chat-bubble-text {
  display: block;
  white-space: pre-wrap;
}

.chat-bubble-meta {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 3px;
  margin-top: 2px;
}

.chat-bubble-time {
  font-size: 11px;
  opacity: 0.7;
}

.chat-bubble--mine .chat-bubble-time {
  color: rgba(255, 255, 255, 0.8);
}

.chat-msg-row--theirs .chat-bubble-time {
  color: #999;
}

.chat-seen-icon {
  font-size: 14px;
  opacity: 0.7;
}

.chat-bubble--mine .chat-seen-icon {
  color: rgba(255, 255, 255, 0.8);
}

.chat-seen-icon--sending {
  animation: pulse 1s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 1; }
}

/* Input */
.chat-input-wrap {
  padding: 8px 16px 12px;
  border-top: 1px solid #e5e5e5;
  flex-shrink: 0;
  background: #fff;
}
.chat-input-target {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 4px 6px;
  color: var(--ag-text-muted, #999);
  font-size: 12px;
}
.chat-input-target-text {
  font-weight: 600;
  color: var(--ag-primary-500, #486730);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chat-input-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f0f2f5;
  border-radius: 24px;
  padding: 4px 4px 4px 16px;
}

.chat-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  outline: none;
  color: #1a1a1a;
  min-height: 24px;
}

.chat-send-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 50%;
  background: #0084ff;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background 0.15s;
}

.chat-send-btn:hover {
  background: #006edb;
}

.chat-send-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Modal (create group) */
.chat-modal {
  background: #fff;
  border-radius: 16px;
  width: 400px;
  max-width: 90vw;
  box-shadow: 0 8px 48px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.chat-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #e5e5e5;
}

.chat-modal-title {
  font-size: 16px;
  font-weight: 600;
}

.chat-modal-body {
  padding: 20px;
}

.chat-modal-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s;
}

.chat-modal-input:focus {
  border-color: #0084ff;
}

.chat-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  padding: 12px 20px;
  border-top: 1px solid #e5e5e5;
}

.chat-modal-cancel,
.chat-modal-confirm {
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: background 0.15s;
}

.chat-modal-cancel {
  background: #f0f0f0;
  color: #666;
}

.chat-modal-cancel:hover {
  background: #e5e5e5;
}

.chat-modal-confirm {
  background: #0084ff;
  color: #fff;
}

.chat-modal-confirm:hover {
  background: #006edb;
}

.chat-modal-confirm:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* User list in add-member modal */
.chat-user-list {
  margin-top: 12px;
  max-height: 240px;
  overflow-y: auto;
}

.chat-user-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.1s;
}

.chat-user-item:hover {
  background: #f0f2f5;
}

.chat-avatar--xs {
  width: 32px;
  height: 32px;
  font-size: 13px;
}

.chat-user-name {
  font-size: 14px;
  color: #1a1a1a;
}

/* Transitions */
.chat-panel-enter-active,
.chat-panel-leave-active {
  transition: opacity 0.2s ease;
}

.chat-panel-enter-active .chat-panel,
.chat-panel-leave-active .chat-panel {
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.chat-panel-enter-from,
.chat-panel-leave-to {
  opacity: 0;
}

.chat-panel-enter-from .chat-panel {
  transform: scale(0.95);
  opacity: 0;
}

.chat-panel-leave-to .chat-panel {
  transform: scale(0.95);
  opacity: 0;
}

/* Mobile */
@media (max-width: 768px) {
  .chat-overlay {
    align-items: stretch;
  }

  .chat-panel {
    width: 100vw;
    max-width: 100vw;
    height: 100vh;
    max-height: 100vh;
    border-radius: 0;
  }

  .chat-sidebar {
    width: 100%;
    min-width: 100%;
  }

  .chat-sidebar--hidden {
    display: none;
  }

  .chat-main {
    width: 100%;
  }

  .chat-back-btn {
    display: flex;
  }

  .chat-input-box {
    margin: 0 8px;
  }

  .chat-modal {
    width: 90vw;
    max-width: 90vw;
  }
}
</style>
