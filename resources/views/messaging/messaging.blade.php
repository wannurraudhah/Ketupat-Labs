<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging - Material Learning Platform</title>
    <link rel="stylesheet" href="{{ asset('assets/css/messaging.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css') }}">
</head>
<body>
    <div class="messaging-container">
        <aside class="conversations-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-comments"></i> Messages</h2>
                <button class="btn-create-group" id="btnCreateGroup" title="Create Group Chat">
                    <i class="fas fa-plus-circle"></i>
                </button>
            </div>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchConversations" placeholder="Search conversations...">
            </div>
            
            <div class="sort-filter">
                <select id="sortConversations">
                    <option value="recent">Most Recent</option>
                    <option value="oldest">Oldest</option>
                    <option value="unread">Unread</option>
                </select>
            </div>
            
            <div class="conversations-list" id="conversationsList">
            </div>
        </aside>

        <main class="chat-main">
            <div class="chat-header" id="chatHeader">
                <div class="chat-header-placeholder">
                    <i class="fas fa-comment-dots"></i>
                    <p>Select a conversation to start chatting</p>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
            </div>
            
            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                <div class="typing-indicator" id="typingIndicator" style="display: none;">
                    <span id="typingUser"></span> is typing...
                </div>
                <div class="chat-input-wrapper">
                    <button class="btn-attach" id="btnAttach" title="Attach File">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="file" id="fileInput" style="display: none;" multiple>
                    <textarea 
                        id="messageInput" 
                        placeholder="Type your message..."
                        rows="1"
                    ></textarea>
                    <button class="btn-send" id="btnSend" title="Send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </main>

        <aside class="info-sidebar" id="infoSidebar" style="display: none;">
            <div class="info-header">
                <h3 id="infoTitle">Group Info</h3>
                <button class="btn-close-info" id="btnCloseInfo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="info-content" id="infoContent">
            </div>
        </aside>
    </div>

    <div class="modal" id="createGroupModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create Group Chat</h2>
                <button class="modal-close" id="closeGroupModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="createGroupForm">
                    <div class="form-group">
                        <label for="groupName">Group Name</label>
                        <input type="text" id="groupName" required>
                    </div>
                    <div class="form-group">
                        <label for="groupMembers">Add Members</label>
                        <div class="members-selector" id="membersSelector">
                        </div>
                        <div class="selected-members" id="selectedMembers"></div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" id="cancelGroupModal">Cancel</button>
                        <button type="submit" class="btn-primary">Create Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/messaging.js') }}"></script>
</body>
</html>
