<style>
.tabs-logs { display: flex; border-bottom: 2px solid #e0e0e0; margin-bottom: 20px; }
.tab-logs { padding: 12px 24px; cursor: pointer; font-family: 'Barlow Condensed', sans-serif; font-size: 20px; color: #777; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
.tab-logs:hover { color: var(--navy); }
.tab-logs.active { border-bottom: 3px solid var(--red, #d32f2f); color: var(--navy); font-weight: bold; }
.tab-pane-logs { display: none; }
.tab-pane-logs.active { display: block; }

/* Activity Log Styles */
.activity-timeline { display: flex; flex-direction: column; gap: 16px; margin-bottom: 30px; }
.activity-item { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 16px; }
.activity-header { margin-bottom: 8px; font-size: 14px; }
.activity-header strong { color: var(--navy); font-size: 15px; }
.activity-body { font-size: 14px; color: #444; }
.activity-body .change-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.change-badge { background: #e9ecef; padding: 2px 8px; border-radius: 4px; font-family: monospace; }
.change-arrow { color: #888; }
</style>

<div class="comments-wrapper">
    <div class="comments-section">
        
        <div class="tabs-logs">
            <div class="tab-logs active" onclick="switchLogTab(event, 'discussion')">Discussion (<span id="discussion-count">{{ $order->comments->count() }}</span>)</div>
            <div class="tab-logs" onclick="switchLogTab(event, 'activity')">Activity Log</div>
        </div>
        
        <div id="tab-discussion" class="tab-pane-logs active">
            <div class="chat-container">
                @foreach($order->comments->sortBy('created_at') as $comment)
                    <div class="chat-message {{ $comment->user_id == auth()->id() ? 'chat-mine' : 'chat-theirs' }}">
                        <div class="comment-card">
                            <div class="comment-header">
                                <div>
                                    <span class="comment-author">{{ $comment->user->name }}</span>
                                    @if($comment->user->hasRole('engineer'))
                                        <span class="comment-role" style="background:#e3f2fd; color:#1565c0;">Engineer</span>
                                    @elseif($comment->user->hasRole('manager'))
                                        <span class="comment-role" style="background:#fce4ec; color:#c2185b;">Manager</span>
                                    @elseif($comment->user->hasRole('workshop'))
                                        <span class="comment-role" style="background:#fff3e0; color:#e65100;">Workshop</span>
                                    @endif
                                </div>
                                <span class="comment-time" title="{{ $comment->created_at->diffForHumans() }}">
                                    {{ $comment->created_at->format('M d, Y h:i A') }}
                                </span>
                            </div>
                            <div class="comment-body">
                                {!! nl2br(e($comment->body)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="comment-form">
                <form id="ajax-comment-form" action="{{ route('orders.comments.store', $order) }}" method="POST">
                    @csrf
                    <textarea id="comment-body" name="body" placeholder="Write a comment..." required></textarea>
                    <div class="comment-form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-post-comment">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="tab-activity" class="tab-pane-logs">
            <div class="activity-timeline">
                @forelse($order->activities()->latest()->get() as $activity)
                    <div class="activity-item">
                        <div class="activity-header">
                            <strong>{{ $activity->causer ? $activity->causer->name : 'System' }}</strong> 
                            <span style="color: #888; font-size: 13px; margin-left: 8px;">- {{ $activity->created_at->diffForHumans() }} ({{ $activity->created_at->format('M d, Y h:i A') }})</span>
                        </div>
                        <div class="activity-body">
                            @if($activity->description == 'updated')
                                @foreach($activity->changes()['attributes'] ?? [] as $key => $value)
                                    @if(!in_array($key, ['updated_at', 'created_at']))
                                        @php
                                            $oldValue = $activity->changes()['old'][$key] ?? 'None';
                                            if (empty($oldValue)) $oldValue = 'None';
                                            if (empty($value)) $value = 'None';
                                        @endphp
                                        <div class="change-row">
                                            &bull; 
                                            <span class="change-badge">{{ ucfirst($key) }}</span>
                                            <span class="change-badge">{{ $oldValue }}</span>
                                            <span class="change-arrow">&rarr;</span>
                                            <span class="change-badge" style="background: #e3f2fd; color: #0d47a1;">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div style="font-weight: 500; color: #333;">
                                    &bull; {{ ucfirst($activity->description) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="color: #777; font-style: italic; padding: 20px 0;">No activities recorded yet.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ajax-comment-form');
    const container = document.querySelector('.chat-container');
    const btn = document.getElementById('btn-post-comment');
    const textarea = document.getElementById('comment-body');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const body = textarea.value;
            if (!body.trim()) return;

            btn.disabled = true;
            btn.innerText = 'Posting...';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ body: body })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const c = data.comment;
                    let roleHtml = '';
                    if (c.role) {
                        roleHtml = `<span class="comment-role" style="background:${c.roleBg}; color:${c.roleColor};">${c.role}</span>`;
                    }
                    
                    const html = `
                    <div class="chat-message chat-mine">
                        <div class="comment-card">
                            <div class="comment-header">
                                <div>
                                    <span class="comment-author">${c.author}</span>
                                    ${roleHtml}
                                </div>
                                <span class="comment-time" title="${c.time_diff}">
                                    ${c.time_exact}
                                </span>
                            </div>
                            <div class="comment-body">
                                ${c.body}
                            </div>
                        </div>
                    </div>`;
                    
                    container.insertAdjacentHTML('beforeend', html);
                    textarea.value = '';
                    
                    // Update count
                    const countEl = document.getElementById('discussion-count');
                    if (countEl) {
                        countEl.innerText = parseInt(countEl.innerText) + 1;
                    }
                }
            })
            .catch(error => {
                console.error('Error posting comment:', error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Post Comment';
            });
        });
    }
});

function switchLogTab(event, tabName) {
    document.querySelectorAll('.tab-logs').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-pane-logs').forEach(el => el.classList.remove('active'));
    
    event.currentTarget.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}
</script>
