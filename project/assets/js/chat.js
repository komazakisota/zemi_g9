/**
 * チャット機能JavaScript
 */

let currentChatRoomId = null;
let chatPollingInterval = null;

/**
 * トークルームモーダルを開く
 */
async function openChatModal() {
    if (!selectedYearId) {
        alert('先に年度を選択してください');
        return;
    }
    
    document.getElementById('chat-modal').classList.add('active');
    document.getElementById('chat-messages').innerHTML = '<p class="loading-text">読み込み中...</p>';
    
    try {
        // トークルームIDを取得
        const response = await fetch(`../api/chat/get_chat_room.php?course_year_id=${selectedYearId}`);
        const data = await response.json();
        
        if (data.success) {
            currentChatRoomId = data.chat_room_id;
            document.getElementById('chat-room-title').textContent = data.room_name;
            
            // メッセージを読み込む
            await loadChatMessages();
            
            // 定期的にメッセージを更新（3秒ごと）
            chatPollingInterval = setInterval(loadChatMessages, 3000);
        } else {
            alert('エラー: ' + data.error);
            closeChatModal();
        }
    } catch (error) {
        console.error('Error opening chat room:', error);
        alert('通信エラーが発生しました');
        closeChatModal();
    }
}

/**
 * トークルームモーダルを閉じる
 */
function closeChatModal() {
    document.getElementById('chat-modal').classList.remove('active');
    document.getElementById('chat-message-input').value = '';
    
    // ポーリングを停止
    if (chatPollingInterval) {
        clearInterval(chatPollingInterval);
        chatPollingInterval = null;
    }
    
    currentChatRoomId = null;
}

/**
 * チャットメッセージを読み込む
 */
async function loadChatMessages() {
    if (!currentChatRoomId) return;
    
    try {
        const response = await fetch(`../api/chat/get_messages.php?chat_room_id=${currentChatRoomId}`);
        const data = await response.json();
        
        if (data.success) {
            renderChatMessages(data.messages);
        }
    } catch (error) {
        console.error('Error loading chat messages:', error);
    }
}

/**
 * チャットメッセージを描画
 */
function renderChatMessages(messages) {
    const chatMessages = document.getElementById('chat-messages');
    
    if (messages.length === 0) {
        chatMessages.innerHTML = '<p class="empty-message">まだメッセージがありません<br>最初のメッセージを送信しましょう！</p>';
        return;
    }
    
    // 現在のスクロール位置を保存
    const isScrolledToBottom = chatMessages.scrollHeight - chatMessages.scrollTop <= chatMessages.clientHeight + 50;
    
    chatMessages.innerHTML = messages.map(msg => `
        <div class="chat-message">
            <div class="chat-message-header">
                <span class="chat-username">${escapeHtml(msg.username)}</span>
                <span class="chat-time">${formatChatTime(msg.created_at)}</span>
            </div>
            <div class="chat-text">${escapeHtml(msg.message)}</div>
        </div>
    `).join('');
    
    // 新しいメッセージがある場合は自動スクロール
    if (isScrolledToBottom) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

/**
 * メッセージを送信
 */
async function sendMessage() {
    if (!currentChatRoomId) return;
    
    const input = document.getElementById('chat-message-input');
    const message = input.value.trim();
    
    if (!message) {
        alert('メッセージを入力してください');
        return;
    }
    
    const formData = new FormData();
    formData.append('chat_room_id', currentChatRoomId);
    formData.append('message', message);
    
    try {
        const response = await fetch('../api/chat/send_message.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            await loadChatMessages();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * Enterキーでメッセージを送信
 */
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('chat-message-input');
    
    input.addEventListener('keydown', function(e) {
        // Enterキー（Shift+Enterは除く）
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});

/**
 * チャット時間フォーマット
 */
function formatChatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) {
        return 'たった今';
    } else if (diffMins < 60) {
        return `${diffMins}分前`;
    } else if (diffMins < 1440) {
        const diffHours = Math.floor(diffMins / 60);
        return `${diffHours}時間前`;
    } else {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        return `${month}/${day} ${hour}:${minute}`;
    }
}
