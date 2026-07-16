<div class="comments-wrapper">
    <div class="comments-section">
        <h3 style="font-family: 'Barlow Condensed', sans-serif; font-size: 20px; color: var(--navy); margin-bottom: 16px;">
        Discussion (<span id="discussion-count">{{ $order->comments->count() }}</span>)
    </h3>

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
</script>
