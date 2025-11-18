let postState = {
    postId: null,
    post: null
};

document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    if (sessionStorage.getItem('userLoggedIn') !== 'true') {
        window.location.href = 'login.html';
        return;
    }
    
    initEventListeners();
    loadPostDetail();
});

function initEventListeners() {
    const logoutBtn = document.getElementById('btnLogout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                await fetch('logout.php', {
                    method: 'POST',
                    credentials: 'include'
                });
            } catch (error) {
                console.error('Logout error:', error);
            }
            
            sessionStorage.removeItem('userLoggedIn');
            sessionStorage.removeItem('userEmail');
            sessionStorage.removeItem('userId');
            window.location.href = 'login.html';
        });
    }
    
    document.getElementById('btnCreatePost').addEventListener('click', () => {
        window.location.href = 'create-post.html';
    });

    document.getElementById('btnCreateForum').addEventListener('click', () => {
        window.location.href = 'create-forum.html';
    });
}

async function loadPostDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('id');
    
    if (!postId) {
        showError('No post selected');
        return;
    }
    
    postState.postId = postId;
    
    // Track visited post
    trackVisitedPost(postId);
    
    try {
        const response = await fetch(`../api/forum_endpoints.php?action=get_posts&post_id=${postId}`);
        const data = await response.json();
        
        if (data.status === 200 && data.data.posts.length > 0) {
            postState.post = data.data.posts[0];
            renderPostDetail(postState.post);
            loadSidebarData();
            loadAboutCommunity();
        } else {
            showError('Failed to load post details');
        }
    } catch (error) {
        console.error('Error loading post details:', error);
        showError('Failed to load post');
    }
}

function renderPostDetail(post) {
    const container = document.getElementById('postDetailContent');
    
    // Get forum name from the post - we'll need to add this to the API response
    const forumName = post.forum_name || 'Forum';
    const forumInitials = forumName.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    
    container.innerHTML = `
        <!-- Post Card -->
        <div class="post-detail-card">
            <div class="post-vote-section">
                <button class="vote-btn like ${post.user_reacted ? 'active' : ''}" onclick="toggleReaction(${post.id})">
                    <i class="${post.user_reacted ? 'fas' : 'far'} fa-heart"></i>
                </button>
                <div class="vote-count">${post.reaction_count || 0}</div>
                <button class="vote-btn" style="display: none;">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
            <div class="post-content-section">
                <div class="post-detail-header">
                    <div class="post-detail-header-left">
                        <button class="post-back-link" onclick="window.history.back()" title="Back">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="post-community-avatar">${forumInitials}</div>
                        <div>
                            <div class="post-community-info">
                                <span class="post-community-name">r/${escapeHtml(forumName)}</span>
                                <span class="post-time">${formatTime(post.created_at)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-detail-title">
                    ${escapeHtml(post.title)}
                </div>
                ${post.tags ? `
                    <div class="post-tags">
                        ${(() => {
                            try {
                                const tags = JSON.parse(post.tags);
                                return Array.isArray(tags) ? tags : [];
                            } catch (e) {
                                return [];
                            }
                        })().map(tag => `
                            <span class="post-tag">${escapeHtml(tag)}</span>
                        `).join('')}
                    </div>
                ` : ''}
                <div class="post-detail-body">
                    ${escapeHtml(post.content)}
                </div>
                ${post.attachments ? `
                    <div style="margin-bottom: 16px; margin-top: 16px;">
                        ${(() => {
                            try {
                                const attachments = typeof post.attachments === 'string' 
                                    ? JSON.parse(post.attachments) 
                                    : post.attachments;
                                if (!Array.isArray(attachments)) return '';
                                
                                const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                const imageAttachments = [];
                                const otherAttachments = [];
                                
                                attachments.forEach(att => {
                                    const ext = att.name.split('.').pop().toLowerCase();
                                    if (imageExts.includes(ext)) {
                                        imageAttachments.push(att);
                                    } else {
                                        otherAttachments.push(att);
                                    }
                                });
                                
                                let html = '';
                                
                                // Show image previews
                                if (imageAttachments.length > 0) {
                                    html += '<div class="post-image-preview" style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 12px;">';
                                    imageAttachments.forEach(att => {
                                        html += `
                                            <div style="position: relative; max-width: 500px; max-height: 500px;">
                                                <img src="${att.url}" alt="${escapeHtml(att.name)}" 
                                                     style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" 
                                                     onclick="window.open('${att.url}', '_blank');"
                                                     onerror="this.style.display='none';">
                                            </div>
                                        `;
                                    });
                                    html += '</div>';
                                }
                                
                                // Show other file attachments as links
                                if (otherAttachments.length > 0) {
                                    html += '<div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px;">';
                                    otherAttachments.forEach(att => {
                                        html += `
                            <a href="${att.url}" target="_blank" class="attachment-item">
                                <i class="fas ${getFileIcon(att.name)}"></i>
                                <span>${escapeHtml(att.name)}</span>
                            </a>
                                        `;
                                    });
                                    html += '</div>';
                                }
                                
                                return html;
                            } catch (e) {
                                return '';
                            }
                        })()}
                    </div>
                ` : ''}
                <div class="post-detail-footer">
                    <div class="post-detail-actions">
                        <button class="btn-post-action" onclick="toggleReaction(${post.id})">
                            <i class="far fa-comment"></i>
                            <span>${post.reply_count || 0}</span>
                        </button>
                        <button class="btn-post-action">
                            <i class="far fa-bookmark"></i>
                            <span>Save</span>
                        </button>
                        <button class="btn-post-action ${post.user_reacted ? 'liked' : ''}" onclick="toggleReaction(${post.id})">
                            <i class="${post.user_reacted ? 'fas' : 'far'} fa-heart"></i>
                            <span>${post.reaction_count || 0}</span>
                        </button>
                        <button class="btn-post-action">
                            <i class="fas fa-share"></i>
                            <span>Share</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Container -->
        <div class="comments-container">
            <div class="comments-header">
                <i class="far fa-comment"></i>
                <span>${post.reply_count || 0} Comments</span>
            </div>
            <div id="commentsContainer">
            </div>
            <div class="comment-form">
                <form id="commentForm" onsubmit="submitComment(event)">
                    <div class="comment-input-container">
                        <div class="comment-input-avatar">
                            ${getCurrentUserInitials()}
                        </div>
                        <div class="comment-input-wrapper">
                            <textarea id="commentInput" class="comment-input" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="comment-submit-btn">Comment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    loadComments(post.id);
}

async function loadComments(postId) {
    try {
        const response = await fetch(`../api/forum_endpoints.php?action=get_comments&post_id=${postId}&sort=top`);
        const data = await response.json();
        
        if (data.status === 200) {
            // Debug: Log full comment structure recursively
            function logCommentStructure(comment, depth = 0, prefix = '') {
                const indent = '  '.repeat(depth);
                console.log(`${indent}${prefix}Comment ${comment.id} (depth ${depth}) - "${comment.content?.substring(0, 20)}..."`);
                if (comment.replies && Array.isArray(comment.replies)) {
                    console.log(`${indent}  └─ Has ${comment.replies.length} direct replies`);
                    comment.replies.forEach((reply, idx) => {
                        logCommentStructure(reply, depth + 1, `Reply ${idx + 1}: `);
                    });
                } else {
                    console.log(`${indent}  └─ No replies array or empty`);
                }
            }
            
            console.log('=== FULL COMMENT TREE STRUCTURE ===');
            data.data.comments.forEach((comment, idx) => {
                console.log(`\n--- Top-Level Comment ${idx + 1} (ID: ${comment.id}) ---`);
                logCommentStructure(comment, 0);
            });
            console.log('=== END COMMENT TREE ===');
            
            renderComments(data.data.comments);
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

let expandedReplies = new Set();
let collapsedComments = new Set();

function renderComments(comments) {
    const container = document.getElementById('commentsContainer');
    
    if (!comments || comments.length === 0) {
        container.innerHTML = '<p style="color: #878a8c; text-align: center; padding: 20px;">No comments yet</p>';
        return;
    }
    
    // Sort comments by reaction_count + reply_count for top comments
    const sortedComments = [...comments].sort((a, b) => {
        const scoreA = (a.reaction_count || 0) + (a.reply_count || 0);
        const scoreB = (b.reaction_count || 0) + (b.reply_count || 0);
        return scoreB - scoreA;
    });
    
    container.innerHTML = sortedComments.map((comment, index) => {
        const maxReplies = expandedReplies.has(comment.id) ? 999 : 3;
        return renderCommentItem(comment, 0, maxReplies, null, index === sortedComments.length - 1);
    }).join('');
}

function renderCommentItem(comment, depth = 0, maxRepliesToShow = 3, parentId = null, isLastChild = false) {
    const totalScore = (comment.reaction_count || 0) + (comment.reply_count || 0);
    const hasHighScore = totalScore > 0 && depth === 0;
    const isMainComment = depth === 0;
    const totalReplies = comment.replies ? comment.replies.length : 0;
    const hasMoreReplies = totalReplies > maxRepliesToShow;
    const repliesToShow = hasMoreReplies ? comment.replies.slice(0, maxRepliesToShow) : (comment.replies || []);
    const hiddenRepliesCount = totalReplies - maxRepliesToShow;
    const isCollapsed = collapsedComments.has(comment.id);
    const hasReplies = comment.replies && comment.replies.length > 0;
    const indentAmount = depth * 40;
    const buttonSize = 18;
    const isLastInThread = isLastChild && !hasReplies && depth > 0;
    
    // Debug: Log rendering at deeper levels
    if (depth >= 2) {
        console.log(`Rendering comment ${comment.id} at depth ${depth}, has ${totalReplies} replies`);
        if (comment.replies && comment.replies.length > 0) {
            console.log(`  Replies: ${comment.replies.map(r => `ID ${r.id} (has ${r.replies?.length || 0} nested)`).join(', ')}`);
        }
    }
    
    return `
        <div class="comment-thread" data-comment-id="${comment.id}" style="position: relative; padding-left: ${depth > 0 ? indentAmount + 32 : 0}px; padding-bottom: ${isLastInThread ? '0' : '8px'}; margin-bottom: ${depth === 0 ? '16px' : '0'};">
            <!-- Vertical line from parent's button to this comment's button -->
            ${depth > 0 ? `
                <div style="position: absolute; left: ${indentAmount - 40 + 16}px; top: -8px; width: 2px; background: #d1d5db; z-index: 0; height: ${buttonSize / 2 + 8}px;"></div>
            ` : ''}
            
            <!-- Collapse button container (only for nested comments) -->
            ${depth > 0 ? `
                <div style="position: absolute; left: ${indentAmount + 7}px; top: 0px; z-index: 2;">
                    <button onclick="toggleCommentCollapsePost(${comment.id})" style="width: ${buttonSize}px; height: ${buttonSize}px; border-radius: 50%; border: 1px solid #d1d5db; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        ${isCollapsed ? `
                            <i class="fas fa-plus" style="font-size: 9px; color: #6b7280;"></i>
                        ` : `
                            <i class="fas fa-minus" style="font-size: 9px; color: #6b7280;"></i>
                        `}
                    </button>
                    
                    <!-- Vertical line from button down to children -->
                    ${hasReplies && !isCollapsed ? `
                        <div style="position: absolute; left: 50%; top: ${buttonSize}px; bottom: ${isLastInThread ? 'auto' : '-8px'}; width: 2px; background: #d1d5db; transform: translateX(-50%); z-index: 0; height: ${isLastInThread ? '8px' : '100%'};"></div>
                    ` : ''}
                </div>
            ` : ''}
            
            ${isCollapsed && depth > 0 ? `
                <div style="padding: 4px 0; margin-left: ${depth > 0 ? '12px' : '0'};">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="comment-author-info" style="display: flex; align-items: center; gap: 8px;">
                            <span class="comment-author-name" style="font-size: 13px; font-weight: 600; color: #9ca3af;">
                                ${escapeHtml(comment.author_name || comment.author_username)}
                            </span>
                            <span style="font-size: 12px; color: #9ca3af;">• ${comment.reply_count || 0} ${comment.reply_count === 1 ? 'reply' : 'replies'}</span>
                        </div>
                    </div>
                </div>
            ` : `
                <div class="comment-item" data-comment-id="${comment.id}" style="padding: ${depth > 0 ? '6px 0' : '12px 0'}; margin-left: ${depth > 0 ? '12px' : '0'};">
                    <div class="comment-header" style="${isMainComment ? 'cursor: pointer;' : ''} display: flex; align-items: center; gap: 6px; margin-bottom: ${depth > 0 ? '4px' : '6px'};" ${isMainComment ? `onclick="openCommentDetail(${comment.id})"` : ''}>
                        <div class="comment-avatar" style="${depth > 0 ? 'width: 24px; height: 24px; font-size: 10px;' : 'width: 32px; height: 32px; font-size: 12px;'} flex-shrink: 0; border-radius: 50%; background: #0079d3; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    ${getUserInitials(comment.author_name || comment.author_username)}
                </div>
                        <div class="comment-author-info" style="flex: 1; display: flex; align-items: center; gap: 4px;">
                            <span class="comment-author-name" style="${depth > 0 ? 'font-size: 12px;' : 'font-size: 12px;'} font-weight: 600; color: #1c1c1c;">
                                ${escapeHtml(comment.author_name || comment.author_username)}
                            </span>
                            <span style="font-size: 12px; color: #878a8c; margin: 0 2px;">•</span>
                            <span class="comment-time" style="${depth > 0 ? 'font-size: 11px;' : 'font-size: 12px;'} color: #878a8c;">
                                ${formatTime(comment.created_at)}
                            </span>
                        </div>
                        ${hasHighScore && depth === 0 ? `
                            <div style="display: flex; gap: 12px; align-items: center; font-size: 12px; color: #878a8c;">
                                <span title="Likes" style="display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-heart" style="color: #ff4500; font-size: 11px;"></i>
                                    <span>${comment.reaction_count || 0}</span>
                                </span>
                                ${comment.reply_count > 0 ? `
                                    <span title="Replies" style="display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-comments" style="font-size: 11px;"></i>
                                        <span>${comment.reply_count}</span>
                                    </span>
                                ` : ''}
                            </div>
                        ` : comment.reaction_count > 0 ? `
                            <span style="font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px;" title="Likes">
                                <i class="fas fa-heart" style="color: #ff4500; font-size: 11px;"></i>
                                <span>${comment.reaction_count}</span>
                            </span>
                        ` : ''}
                    </div>
                    <div class="comment-body" style="${depth > 0 ? 'font-size: 13px;' : 'font-size: 14px;'} margin-left: ${depth > 0 ? '30px' : '40px'}; margin-top: 4px; color: #1c1c1c; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.5;" ${isMainComment ? `onclick="event.stopPropagation(); openCommentDetail(${comment.id})"` : ''}>
                        ${escapeHtml(comment.content)}
                        ${comment.is_edited ? '<span style="font-style: italic; color: #878a8c; margin-left: 4px; font-size: 11px;">(edited)</span>' : ''}
                    </div>
                    
                    <!-- Reply Form (hidden by default) -->
                    <div class="reply-form-container" id="replyForm_${comment.id}" style="display: none; margin-top: 12px; margin-left: ${depth > 0 ? '30px' : '40px'}; padding: 8px 0;">
                        <div class="comment-input-container">
                            <div class="comment-input-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #0079d3;">
                                ${getCurrentUserInitials()}
                            </div>
                            <div class="comment-input-wrapper" style="flex: 1;">
                                <textarea id="replyInput_${comment.id}" class="comment-input" placeholder="Write a reply..." style="min-height: 60px; border: 1px solid #edeff1; border-radius: 4px;" required></textarea>
                                <div style="display: flex; gap: 8px; margin-top: 8px;">
                                    <button type="button" class="comment-submit-btn" onclick="event.stopPropagation(); submitReply(${comment.id}, ${postState.postId})" style="padding: 6px 16px; font-size: 13px;">Reply</button>
                                    <button type="button" class="comment-cancel-btn" onclick="event.stopPropagation(); cancelReply(${comment.id})" style="padding: 6px 16px; background: transparent; color: #878a8c; border: none; cursor: pointer; font-size: 13px; font-weight: 700;">Cancel</button>
                                </div>
                            </div>
                </div>
            </div>
                    
                    ${repliesToShow.length > 0 && !isCollapsed ? `
                        <div style="margin-top: 12px;">
                            ${repliesToShow.map((reply, index) => renderCommentItem(reply, depth + 1, maxRepliesToShow, comment.id, index === repliesToShow.length - 1)).join('')}
                        </div>
                    ` : ''}
                    
                    ${hasMoreReplies ? `
                        <div style="margin-top: 8px; margin-left: ${depth > 0 ? '36px' : '48px'};">
                            <button class="show-more-replies-btn" onclick="event.stopPropagation(); showMoreReplies(${comment.id}, ${comment.replies.length})" style="padding: 6px 12px; background: transparent; color: #ff4500; border: 1px solid #ff4500; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                <i class="fas fa-chevron-down"></i> View ${hiddenRepliesCount} more ${hiddenRepliesCount === 1 ? 'reply' : 'replies'}
                            </button>
                            <button class="view-all-replies-btn" onclick="event.stopPropagation(); openCommentDetail(${comment.id})" style="margin-left: 8px; padding: 6px 12px; background: transparent; color: #1c1c1c; border: 1px solid #edeff1; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                <i class="fas fa-external-link-alt"></i> View all replies
                            </button>
            </div>
                    ` : ''}
                    
                    <div class="comment-actions" style="margin-top: 6px; margin-left: ${depth > 0 ? '30px' : '40px'}; display: flex; gap: 12px; align-items: center;" onclick="event.stopPropagation();">
                        <button class="comment-action-btn" onclick="toggleCommentLike(${comment.id})" style="background: transparent; border: none; color: #878a8c; cursor: pointer; font-size: 12px; padding: 2px 4px; display: flex; align-items: center; gap: 4px; font-weight: 700;">
                            <i class="far fa-heart" style="font-size: 14px;"></i>
                            <span>${comment.reaction_count > 0 ? comment.reaction_count : ''}</span>
                        </button>
                        <button class="comment-action-btn" onclick="toggleReplyForm(${comment.id})" style="background: transparent; border: none; color: #878a8c; cursor: pointer; font-size: 12px; padding: 2px 4px; display: flex; align-items: center; gap: 4px; font-weight: 700;">
                            <i class="far fa-comment" style="font-size: 14px;"></i>
                            <span>Reply</span>
                        </button>
                        ${depth === 0 ? `
                            <button class="comment-action-btn" onclick="shareComment(${comment.id})" style="background: transparent; border: none; color: #878a8c; cursor: pointer; font-size: 12px; padding: 2px 4px; display: flex; align-items: center; gap: 4px; font-weight: 700;">
                                <i class="fas fa-share" style="font-size: 14px;"></i>
                                <span>Share</span>
                            </button>
                        ` : ''}
                    </div>
        </div>
            `}
        </div>
    `;
}

function toggleCommentCollapsePost(commentId) {
    if (collapsedComments.has(commentId)) {
        collapsedComments.delete(commentId);
    } else {
        collapsedComments.add(commentId);
    }
    loadComments(postState.postId);
}

function showMoreReplies(commentId, totalReplies) {
    expandedReplies.add(commentId);
    loadComments(postState.postId);
}

function openCommentDetail(commentId) {
    window.location.href = `comment-detail.html?comment_id=${commentId}&post_id=${postState.postId}`;
}

async function toggleReaction(postId) {
    try {
        const response = await fetch('../api/forum_endpoints.php?action=add_reaction', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                target_type: 'post',
                target_id: postId,
                reaction_type: 'like'
            })
        });
        
        const data = await response.json();
        
        if (data.status === 200) {
            await loadPostDetail();
        }
    } catch (error) {
        console.error('Error toggling reaction:', error);
    }
}

async function toggleBookmark(postId) {
    try {
        const response = await fetch('../api/forum_endpoints.php?action=bookmark_post', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                post_id: postId,
                action: postState.post.is_bookmarked ? 'remove' : 'add'
            })
        });
        
        const data = await response.json();
        
        if (data.status === 200) {
            // Reload post to get updated bookmark status
            await loadPostDetail();
        }
    } catch (error) {
        console.error('Error toggling bookmark:', error);
    }
}

async function submitComment(event) {
    event.preventDefault();
    
    const content = document.getElementById('commentInput').value;
    
    if (!content.trim()) {
        return;
    }
    
    try {
        const response = await fetch('../api/forum_endpoints.php?action=create_comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                post_id: postState.postId,
                content: content.trim()
            })
        });
        
        const data = await response.json();
        
        if (data.status === 200) {
            document.getElementById('commentInput').value = '';
            await loadComments(postState.postId);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Error posting comment:', error);
        showError('Failed to post comment');
    }
}

async function submitReply(commentId, postId) {
    const replyInput = document.getElementById(`replyInput_${commentId}`);
    const content = replyInput.value.trim();
    
    if (!content) {
        return;
    }
    
    try {
        const response = await fetch('../api/forum_endpoints.php?action=create_comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                post_id: postId,
                parent_id: commentId,
                content: content
            })
        });
        
        const data = await response.json();
        
        if (data.status === 200) {
            replyInput.value = '';
            toggleReplyForm(commentId); // Hide the form
            await loadComments(postId);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Error posting reply:', error);
        showError('Failed to post reply');
    }
}

function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`replyForm_${commentId}`);
    if (replyForm) {
        if (replyForm.style.display === 'none') {
            replyForm.style.display = 'block';
            const textarea = document.getElementById(`replyInput_${commentId}`);
            if (textarea) {
                setTimeout(() => textarea.focus(), 100);
            }
        } else {
            replyForm.style.display = 'none';
            const textarea = document.getElementById(`replyInput_${commentId}`);
            if (textarea) {
                textarea.value = '';
            }
        }
    }
}

function cancelReply(commentId) {
    const replyForm = document.getElementById(`replyForm_${commentId}`);
    if (replyForm) {
        replyForm.style.display = 'none';
        const textarea = document.getElementById(`replyInput_${commentId}`);
        if (textarea) {
            textarea.value = '';
        }
    }
}

async function toggleCommentLike(commentId) {
    try {
        const response = await fetch('../api/forum_endpoints.php?action=add_reaction', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                target_type: 'comment',
                target_id: commentId,
                reaction_type: 'like'
            })
        });
        
        const data = await response.json();
        
        if (data.status === 200) {
            await loadComments(postState.postId);
        }
    } catch (error) {
        console.error('Error toggling comment like:', error);
    }
}

function shareComment(commentId) {
    const url = window.location.href.split('?')[0] + `?id=${postState.postId}#comment-${commentId}`;
    if (navigator.share) {
        navigator.share({
            title: 'Comment',
            url: url
        }).catch(() => {
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('Comment link copied to clipboard!');
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if (days > 7) {
        return date.toLocaleDateString();
    } else if (days > 0) {
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else if (hours > 0) {
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (minutes > 0) {
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else {
        return 'just now';
    }
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'jpg': 'fa-file-image',
        'jpeg': 'fa-file-image',
        'png': 'fa-file-image',
        'gif': 'fa-file-image'
    };
    return icons[ext] || 'fa-file';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    alert(message);
}

function getUserInitials(name) {
    if (!name) return '?';
    const words = name.split(' ');
    if (words.length >= 2) {
        return (words[0][0] + words[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function getCurrentUserInitials() {
    const userName = sessionStorage.getItem('userEmail') || 'User';
    return getUserInitials(userName);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

async function loadSidebarData() {
    await loadForumsToSidebar();
    await loadTagsForForum();
}

async function loadForumsToSidebar() {
    try {
        const response = await fetch(`../api/forum_endpoints.php?action=get_forums`);
        const data = await response.json();
        
        if (data.status === 200) {
            renderForumsToSidebar(data.data.forums);
        }
    } catch (error) {
        console.error('Error loading forums:', error);
    }
}

function renderForumsToSidebar(forums) {
    const container = document.getElementById('forumsList');
    
    if (!forums || forums.length === 0) {
        container.innerHTML = `
            <p style="padding: 12px; color: #878a8c; font-size: 14px;">
                No forums yet. Create one to get started!
            </p>
        `;
        return;
    }
    
    container.innerHTML = forums.map(forum => `
        <div class="filter-item" 
             onclick="window.location.href='forum-detail.html?id=${forum.id}'">
            <i class="fas fa-comments" style="color: #878a8c;"></i>
            <span>${escapeHtml(forum.title)}</span>
        </div>
    `).join('');
}

async function loadTagsForForum() {
    if (!postState.post) return;
    
    try {
        const response = await fetch(`../api/forum_endpoints.php?action=get_forum_tags&forum_id=${postState.post.forum_id}`);
        const data = await response.json();
        
        if (data.status === 200) {
            renderTagsToSidebar(data.data.tags || []);
        } else {
            // Fallback: try to parse tags from the post
            renderTagsFromPost();
        }
    } catch (error) {
        console.error('Error loading forum tags:', error);
        renderTagsFromPost();
    }
}

function renderTagsToSidebar(tags) {
    const container = document.getElementById('tagCloud');
    
    if (!tags || tags.length === 0) {
        container.innerHTML = `
            <p style="padding: 8px 16px; color: #878a8c; font-size: 12px;">
                No tags yet
            </p>
        `;
        return;
    }
    
    container.innerHTML = tags.slice(0, 10).map(tag => `
        <span class="tag-chip">#${escapeHtml(tag)}</span>
    `).join('');
}

function renderTagsFromPost() {
    const container = document.getElementById('tagCloud');
    
    if (!postState.post || !postState.post.tags) {
        container.innerHTML = `
            <p style="padding: 8px 16px; color: #878a8c; font-size: 12px;">
                No tags yet
            </p>
        `;
        return;
    }
    
    try {
        const tags = typeof postState.post.tags === 'string' ? JSON.parse(postState.post.tags) : postState.post.tags;
        
        if (!Array.isArray(tags) || tags.length === 0) {
            container.innerHTML = `
                <p style="padding: 8px 16px; color: #878a8c; font-size: 12px;">
                    No tags yet
                </p>
            `;
            return;
        }
        
        container.innerHTML = tags.slice(0, 10).map(tag => `
            <span class="tag-chip">#${escapeHtml(tag)}</span>
        `).join('');
    } catch (e) {
        container.innerHTML = `
            <p style="padding: 8px 16px; color: #878a8c; font-size: 12px;">
                No tags yet
            </p>
        `;
    }
}

async function loadAboutCommunity() {
    if (!postState.post) return;
    
    try {
        const response = await fetch(`../api/forum_endpoints.php?action=get_forum_details&forum_id=${postState.post.forum_id}`);
        const data = await response.json();
        
        if (data.status === 200) {
            renderAboutCommunity(data.data.forum);
        }
    } catch (error) {
        console.error('Error loading forum details:', error);
    }
}

function renderAboutCommunity(forum) {
    const container = document.getElementById('aboutCommunity');
    
    if (!forum) {
        container.innerHTML = '<p style="color: #878a8c;">Unable to load community details</p>';
        return;
    }
    
    const forumInitials = forum.title ? forum.title.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() : '??';
    
    container.innerHTML = `
        <div class="forum-description">
            ${escapeHtml(forum.description || 'No description available.')}
        </div>
        
        <div class="forum-meta">
            <div class="forum-meta-item">
                <i class="fas fa-home"></i>
                <span>Created ${formatDate(forum.created_at)}</span>
            </div>
            <div class="forum-meta-item">
                <i class="fas fa-globe"></i>
                <span>${escapeHtml(forum.visibility || 'Public')}</span>
            </div>
        </div>

        <div class="forum-stats-grid">
            <div class="forum-stat-big">
                <div class="forum-stat-value">${forum.member_count || 0}</div>
                <div class="forum-stat-label">MEMBERS</div>
            </div>
            <div class="forum-stat-big">
                <div class="forum-stat-value">${forum.post_count || 0}</div>
                <div class="forum-stat-label">POSTS</div>
            </div>
        </div>

        <button class="btn-message-mods" style="margin-top: 16px; width: 100%;" onclick="messageMods()">
            <i class="fas fa-comment"></i>
            Message Mods
        </button>
    `;
}

function messageMods() {
    // Navigate to messaging page
    window.location.href = '../Messaging/messaging.html';
}

// Make functions globally accessible
window.toggleReaction = toggleReaction;
window.toggleBookmark = toggleBookmark;
window.submitComment = submitComment;
window.submitReply = submitReply;
window.toggleReplyForm = toggleReplyForm;
window.cancelReply = cancelReply;
window.toggleCommentLike = toggleCommentLike;
window.shareComment = shareComment;
window.showMoreReplies = showMoreReplies;
window.openCommentDetail = openCommentDetail;
window.toggleCommentCollapsePost = toggleCommentCollapsePost;
window.messageMods = messageMods;

function trackVisitedPost(postId) {
    try {
        let recentPosts = JSON.parse(localStorage.getItem('recentPosts') || '[]');
        
        // Remove if already exists
        recentPosts = recentPosts.filter(p => p.id !== postId);
        
        // Add to beginning
        recentPosts.unshift({
            id: postId,
            visitedAt: new Date().toISOString()
        });
        
        // Keep only last 10
        recentPosts = recentPosts.slice(0, 10);
        
        localStorage.setItem('recentPosts', JSON.stringify(recentPosts));
    } catch (error) {
        console.error('Error tracking visited post:', error);
    }
}




