// TODO: Move host/port/path to env vars instead of hardcoding
import { ref, onBeforeUnmount } from 'vue';
import { getApiToken } from '../services/api';

const ws = ref(null);
const connected = ref(false);
const messages = ref([]);
let handlers = {};

export function useChatSocket() {

  let reconnectTimer = null;

  function connect() {
    if (ws.value && (ws.value.readyState === WebSocket.OPEN || ws.value.readyState === WebSocket.CONNECTING)) return;

    const token = getApiToken();
    if (!token) return;

    try {
      const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
      const host = window.location.host;
      const socket = new WebSocket(`${protocol}//${host}/ws/chat/${token}`);

      socket.onopen = () => {
        connected.value = true;
        if (reconnectTimer) {
          clearTimeout(reconnectTimer);
          reconnectTimer = null;
        }
      };

      socket.onmessage = async (event) => {
        let buffer;
        if (event.data instanceof Blob) {
          buffer = await event.data.arrayBuffer();
        } else {
          return; // Ignore non-binary
        }

        const bytes = new Uint8Array(buffer);
        let offset = 0;
        const data = {};
        
        function readVarint() {
          let low = 0;
          let high = 0;
          let shift = 0;
          while (offset < bytes.length) {
            const b = bytes[offset++];
            if (shift < 32) {
              low |= (b & 0x7F) << shift;
            } else {
              high |= (b & 0x7F) << (shift - 32);
            }
            if (!(b & 0x80)) break;
            shift += 7;
          }
          return high * 0x100000000 + low;
        }
        
        const decoder = new TextDecoder();
        
        while (offset < bytes.length) {
          const tag = readVarint();
          const fieldNumber = tag >> 3;
          const wireType = tag & 0x07;
          
          if (wireType === 2) {
            const length = readVarint();
            const strBytes = bytes.subarray(offset, offset + length);
            offset += length;
            const str = decoder.decode(strBytes);
            if (fieldNumber === 1) data.type = str;
            else if (fieldNumber === 2) data.from = str;
            else if (fieldNumber === 3) data.to = str;
            else if (fieldNumber === 4) data.content = str;
          } else if (wireType === 0) {
            const val = readVarint();
            if (fieldNumber === 5) data.timestamp = val;
          } else {
            break;
          }
        }

        messages.value.push(data);
        if (handlers.message) {
          for (const handler of handlers.message) {
            handler(data);
          }
        }
      };

      socket.onclose = () => {
        connected.value = false;
        ws.value = null;
        scheduleReconnect();
      };

      socket.onerror = () => {
        connected.value = false;
      };

      ws.value = socket;
    } catch {
      scheduleReconnect();
    }
  }

  function scheduleReconnect() {
    if (!reconnectTimer) {
      reconnectTimer = setTimeout(() => {
        reconnectTimer = null;
        connect();
      }, 5000);
    }
  }

  function disconnect() {
    if (reconnectTimer) {
      clearTimeout(reconnectTimer);
      reconnectTimer = null;
    }
    if (ws.value) {
      ws.value.close();
      ws.value = null;
      connected.value = false;
    }
  }

  function send(data) {
    if (ws.value && ws.value.readyState === WebSocket.OPEN) {
      ws.value.send(typeof data === 'string' ? data : JSON.stringify(data));
    }
  }

  function on(event, handler) {
    if (!handlers[event]) handlers[event] = new Set();
    handlers[event].add(handler);
  }

  function off(event, handler) {
    if (handlers[event] && handler) {
      handlers[event].delete(handler);
    }
  }


  return {
    ws,
    connected,
    messages,
    connect,
    disconnect,
    send,
    on,
    off,
  };
}
