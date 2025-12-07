// グローバル変数
let appData = {
    teamMembers: [],
    currentPlans: [],
    previousPlans: [],
    doTasks: [],
    checkItems: [],
    checkAverages: [],
    actions: []
};

let currentView = 'home';
let selectedAssignees = [];
let currentModalType = ''; // 'plan' or 'action'
let newPlanText = '';
let newActionText = '';

// 初期化
document.addEventListener('DOMContentLoaded', async () => {
    await loadData();
    setupNavigation();
    showView('home');
});

// データ読み込み
async function loadData() {
    try {
        const response = await fetch('api/get_data.php');
        const data = await response.json();
        
        appData.teamMembers = data.team_members || [];
        appData.currentPlans = data.current_plans || [];
        appData.previousPlans = data.previous_plans || [];
        appData.doTasks = data.do_tasks || [];
        appData.checkItems = data.check_items || [];
        appData.checkAverages = data.check_averages || [];
        appData.actions = data.actions || [];
    } catch (error) {
        console.error('データ読み込みエラー:', error);
        alert('データの読み込みに失敗しました');
    }
}

// ナビゲーション設定
function setupNavigation() {
    document.querySelectorAll('.nav-item').forEach(button => {
        button.addEventListener('click', (e) => {
            const view = e.currentTarget.dataset.view;
            showView(view);
        });
    });
}

// ビュー切り替え
async function showView(view) {
    currentView = view;
    
    // ナビゲーションのアクティブ状態更新
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active', 'plan-active', 'do-active', 'check-active', 'action-active');
        if (item.dataset.view === view) {
            item.classList.add('active');
            if (view === 'plan') item.classList.add('plan-active');
            if (view === 'do') item.classList.add('do-active');
            if (view === 'check') item.classList.add('check-active');
            if (view === 'action') item.classList.add('action-active');
        }
    });
    
    // タイトル更新
    const titles = {
        home: 'ホーム',
        plan: 'Plan - 計画',
        do: 'Do - 実行',
        check: 'Check - 評価',
        action: 'Action - 改善'
    };
    document.getElementById('page-title').textContent = titles[view];
    
    // データ再読み込み
    await loadData();
    
    // ビューのレンダリング
    const mainView = document.getElementById('main-view');
    
    switch(view) {
        case 'home':
            mainView.innerHTML = renderHome();
            setupHomeEvents();
            break;
        case 'plan':
            mainView.innerHTML = renderPlan();
            setupPlanEvents();
            break;
        case 'do':
            mainView.innerHTML = renderDo();
            break;
        case 'check':
            mainView.innerHTML = renderCheck();
            setupCheckEvents();
            break;
        case 'action':
            mainView.innerHTML = renderAction();
            setupActionEvents();
            break;
    }
}

// ホーム画面レンダリング
function renderHome() {
    const currentPhase = getCurrentPhase();
    const avgRating = appData.checkAverages.length > 0
        ? (appData.checkAverages.reduce((sum, item) => sum + parseFloat(item.avg_rating || 0), 0) / appData.checkAverages.length).toFixed(1)
        : 0;
    
    return `
        <div class="home-container">
            <h1 class="home-title">ダッシュボード</h1>
            <p class="home-subtitle">現在のPDCAサイクルの進捗</p>
            
            <div class="pdca-circle-container">
                <div class="pdca-center">
                    <div class="pdca-center-title">PDCA</div>
                    <p class="pdca-center-text">サイクル進行中</p>
                    <span class="pdca-center-badge" style="background: ${getPhaseBadgeColor(currentPhase)}">
                        現在: ${getPhaseLabel(currentPhase)}
                    </span>
                </div>
                
                <svg class="pdca-svg" viewBox="0 0 400 400">
                    <defs>
                        <linearGradient id="planGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#ef4444" />
                            <stop offset="100%" stop-color="#f97316" />
                        </linearGradient>
                        <linearGradient id="doGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f97316" />
                            <stop offset="100%" stop-color="#eab308" />
                        </linearGradient>
                        <linearGradient id="checkGrad" x1="100%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#eab308" />
                            <stop offset="100%" stop-color="#22c55e" />
                        </linearGradient>
                        <linearGradient id="actionGrad" x1="100%" y1="100%" x2="0%" y2="0%">
                            <stop offset="0%" stop-color="#22c55e" />
                            <stop offset="100%" stop-color="#ef4444" />
                        </linearGradient>
                        <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                            <polygon points="0 0, 10 3, 0 6" fill="#9ca3af" />
                        </marker>
                    </defs>
                    
                    <circle cx="200" cy="200" r="130" fill="none" stroke="url(#planGrad)" stroke-width="40" 
                            stroke-dasharray="204 820" stroke-dashoffset="0" opacity="${currentPhase === 'plan' ? 1 : 0.6}" />
                    <circle cx="200" cy="200" r="130" fill="none" stroke="url(#doGrad)" stroke-width="40"
                            stroke-dasharray="204 820" stroke-dashoffset="-204" opacity="${currentPhase === 'do' ? 1 : 0.6}" />
                    <circle cx="200" cy="200" r="130" fill="none" stroke="url(#checkGrad)" stroke-width="40"
                            stroke-dasharray="204 820" stroke-dashoffset="-408" opacity="${currentPhase === 'check' ? 1 : 0.6}" />
                    <circle cx="200" cy="200" r="130" fill="none" stroke="url(#actionGrad)" stroke-width="40"
                            stroke-dasharray="204 820" stroke-dashoffset="-612" opacity="${currentPhase === 'action' ? 1 : 0.6}" />
                    
                    <path d="M 200 60 Q 280 100 310 180" stroke="#9ca3af" stroke-width="3" fill="none" marker-end="url(#arrow)" stroke-dasharray="5,5" />
                    <path d="M 340 200 Q 310 280 230 330" stroke="#9ca3af" stroke-width="3" fill="none" marker-end="url(#arrow)" stroke-dasharray="5,5" />
                    <path d="M 200 360 Q 120 320 90 240" stroke="#9ca3af" stroke-width="3" fill="none" marker-end="url(#arrow)" stroke-dasharray="5,5" />
                    <path d="M 60 200 Q 90 120 170 70" stroke="#9ca3af" stroke-width="3" fill="none" marker-end="url(#arrow)" stroke-dasharray="5,5" />
                </svg>

                <button class="pdca-button pdca-button-plan" onclick="showView('plan')">
                    <span class="pdca-button-letter">P</span>
                    <span class="pdca-button-label">Plan</span>
                    <span class="pdca-button-count">${appData.plans.length}項目</span>
                </button>
                
                <button class="pdca-button pdca-button-do" onclick="showView('do')">
                    <span class="pdca-button-letter">D</span>
                    <span class="pdca-button-label">Do</span>
                    <span class="pdca-button-count">${appData.doTasks.length}タスク</span>
                </button>
                
                <button class="pdca-button pdca-button-check" onclick="showView('check')">
                    <span class="pdca-button-letter">C</span>
                    <span class="pdca-button-label">Check</span>
                    <span class="pdca-button-count">評価: ${avgRating}</span>
                </button>
                
                <button class="pdca-button pdca-button-action" onclick="showView('action')">
                    <span class="pdca-button-letter">A</span>
                    <span class="pdca-button-label">Action</span>
                    <span class="pdca-button-count">${appData.actions.length}改善</span>
                </button>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3 class="stat-card-title">📊 進捗状況</h3>
                    <div class="stat-item">
                        <span class="stat-label">Plan</span>
                        <span class="stat-value" style="color: #f97316;">${appData.plans.length}項目</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Do</span>
                        <span class="stat-value" style="color: #eab308;">${appData.doTasks.length}タスク</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Check</span>
                        <span class="stat-value" style="color: #8b5cf6;">${appData.checkItems.length}評価</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Action</span>
                        <span class="stat-value" style="color: #22c55e;">${appData.actions.length}改善</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3 class="stat-card-title">🎯 次のアクション</h3>
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 12px;">
                        ${getNextActionText(currentPhase)}
                    </p>
                    <button class="btn-primary" onclick="showView('${currentPhase}')">
                        ${getPhaseLabel(currentPhase)}を見る
                    </button>
                </div>
            </div>
        </div>
    `;
}

                // Plan画面レンダリング
function renderPlan() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-plan">P</span>
                Plan - 計画
            </h1>
            
            ${appData.actions.length > 0 ? `
                <div class="info-box info-box-action">
                    <h3 class="info-box-title">💡 前回のActionより</h3>
                    ${appData.actions.map(action => `
                        <div class="info-box-item">
                            • ${action.text}
                            <div style="display: flex; gap: 4px; margin-left: 8px;">
                                ${action.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar" title="${member.display_name}">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            <div class="card">
                <h3 class="card-title">計画項目を追加</h3>
                
                <div class="input-group">
                    <input type="text" class="input-text" id="plan-input" placeholder="新しい計画を入力...">
                    <button class="btn-primary" onclick="openPlanAssigneeModal()">
                        <span>+</span> 担当者を選択
                    </button>
                </div>
                
                <div id="plan-selected-assignees" class="selected-assignees" style="display: none;">
                    <span>選択中:</span>
                    <div class="assignee-chips" id="plan-assignee-chips"></div>
                </div>
            </div>
        </div>
    `;
}

function setupPlanEvents() {
    // Enterキーで送信
    document.getElementById('plan-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            openPlanAssigneeModal();
        }
    });
}

function openPlanAssigneeModal() {
    newPlanText = document.getElementById('plan-input').value.trim();
    if (!newPlanText) {
        alert('計画内容を入力してください');
        return;
    }
    
    currentModalType = 'plan';
    selectedAssignees = [];
    showAssigneeModal();
}

// Do画面レンダリング
function renderDo() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-do">D</span>
                Do - 実行
            </h1>
            
            ${appData.previousPlans.length > 0 ? `
                <div class="info-box info-box-plan">
                    <h3 class="info-box-title">📋 前回のPlan</h3>
                    ${appData.previousPlans.map(plan => `
                        <div class="info-box-item">
                            • ${plan.text}
                            <div style="display: flex; gap: 4px; margin-left: 8px;">
                                ${plan.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);" title="${member.display_name}">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            <div class="card">
                <h3 class="card-title">
                    <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 8px;">今回</span>
                    今回のDo
                </h3>
                <div class="task-list">
                    ${appData.doTasks.map(task => `
                        <div class="task-item task-item-do">
                            <span class="task-text">${task.text}</span>
                            <div class="task-assignees">
                                ${task.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar" title="${member.display_name}">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                            <span class="task-status ${getStatusClass(task.status)}">${task.status}</span>
                        </div>
                    `).join('')}
                    ${appData.doTasks.length === 0 ? '<p style="color: #6b7280; text-align: center;">Planで計画を追加すると、ここに表示されます</p>' : ''}
                </div>
            </div>
        </div>
    `;
}


function getStatusClass(status) {
    if (status === '完了') return 'status-completed';
    if (status === '進行中') return 'status-in-progress';
    return 'status-pending';
}

// Check画面レンダリング
function renderCheck() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-check">C</span>
                Check - 評価
            </h1>
            
            ${appData.doTasks.length > 0 ? `
                <div class="info-box info-box-do">
                    <h3 class="info-box-title">📋 実行したDo</h3>
                    ${appData.doTasks.map(task => `
                        <div class="info-box-item">• ${task.text}</div>
                    `).join('')}
                </div>
            ` : ''}
            
            <div class="card">
                <h3 class="card-title">評価項目（1〜5で評価）</h3>
                <div id="check-items-container">
                    ${appData.checkItems.map((item, index) => `
                        <div class="check-item" data-item-id="${item.id}">
                            <p class="check-question">${item.text}</p>
                            
                            <div class="rating-buttons">
                                ${[1, 2, 3, 4, 5].map(rating => `
                                    <button class="rating-btn ${getRatingClass(rating, item.rating)}" 
                                            data-rating="${rating}"
                                            onclick="setRating(${index}, ${rating})">
                                        ${rating}
                                    </button>
                                `).join('')}
                                <span class="rating-label ${getRatingLabelClass(item.rating)}">
                                    ${getRatingLabel(item.rating)}
                                </span>
                            </div>
                            
                            <textarea class="comment-input" rows="2" 
                                      placeholder="コメントを入力..."
                                      data-item-index="${index}">${item.comment || ''}</textarea>
                        </div>
                    `).join('')}
                </div>
                
                <button class="btn-submit" onclick="submitCheckRatings()">評価を送信</button>
            </div>
        </div>
    `;
}

function setupCheckEvents() {
    // コメント入力の自動保存
    document.querySelectorAll('.comment-input').forEach(textarea => {
        textarea.addEventListener('input', (e) => {
            const index = parseInt(e.target.dataset.itemIndex);
            appData.checkItems[index].comment = e.target.value;
        });
    });
}

function setRating(index, rating) {
    appData.checkItems[index].rating = rating;
    showView('check'); // 再レンダリング
}

function getRatingClass(rating, currentRating) {
    if (rating !== currentRating) return '';
    if (rating <= 2) return 'active-low';
    if (rating === 3) return 'active-medium';
    return 'active-high';
}

function getRatingLabelClass(rating) {
    if (rating <= 2) return 'label-low';
    if (rating === 3) return 'label-medium';
    return 'label-high';
}

function getRatingLabel(rating) {
    if (rating <= 2) return '要改善';
    if (rating === 3) return '普通';
    return '良好';
}

async function submitCheckRatings() {
    const ratings = appData.checkItems.map(item => ({
        item_id: item.id,
        rating: item.rating,
        comment: item.comment || ''
    }));
    
    try {
        const response = await fetch('api/save_check.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ratings })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('評価を送信しました！');
            await loadData();
            showView('action'); // Action画面に移動
        } else {
            alert(data.error || '送信に失敗しました');
        }
    } catch (error) {
        console.error('Check submit error:', error);
        alert('送信に失敗しました');
    }
}

// Action画面レンダリング
function renderAction() {
    const lowestRatedItem = appData.checkAverages.length > 0
        ? appData.checkAverages.reduce((lowest, item) => 
            !lowest || parseFloat(item.avg_rating) < parseFloat(lowest.avg_rating) ? item : lowest
        , null)
        : null;
    
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-action">A</span>
                Action - 改善
            </h1>
            
            ${appData.checkAverages.length > 0 ? `
                <div class="average-results">
                    <div class="result-header">
                        <h3 class="result-title">📊 チーム評価結果（平均値）</h3>
                        <span class="response-count">${appData.checkAverages[0].response_count}人が回答</span>
                    </div>
                    ${appData.checkAverages.map(item => `
                        <div class="result-item">
                            <span class="result-text">${item.text}</span>
                            <div class="result-score">
                                <span class="score-value">${parseFloat(item.avg_rating).toFixed(1)}</span>
                                <span class="rating-label ${getRatingLabelClass(parseFloat(item.avg_rating))}">
                                    ${getRatingLabel(parseFloat(item.avg_rating))}
                                </span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            ${lowestRatedItem && parseFloat(lowestRatedItem.avg_rating) <= 3 ? `
                <div class="priority-alert">
                    <h3 class="alert-title">⚠️ 最優先課題</h3>
                    <p class="alert-text">
                        「${lowestRatedItem.text}」について: チーム平均${parseFloat(lowestRatedItem.avg_rating).toFixed(1)}点 - 
                        ${parseFloat(lowestRatedItem.avg_rating) <= 2 ? '根本的な改善が必要です。チーム全体で課題を共有し、具体的なアクションプランを立てましょう。' : '改善の余地があります。具体的な対策を検討しましょう。'}
                    </p>
                    ${lowestRatedItem.comments && lowestRatedItem.comments.length > 0 ? `
                        <div class="alert-comments">
                            <p class="comment-label">チームメンバーのコメント:</p>
                            ${lowestRatedItem.comments.map(c => `
                                <div class="comment-item">
                                    <span class="comment-author">${c.display_name}:</span> ${c.comment}
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
            ` : ''}
 


            <div class="card">
                <h3 class="card-title">改善アクション</h3>
                <div class="task-list">
                    ${appData.actions.map(action => `
                        <div class="task-item task-item-do" style="background: #fef3c7;">
                            <span class="task-text">${action.text}</span>
                            <div class="task-assignees">
                                ${action.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar" title="${member.display_name}">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="input-group" style="margin-top: 16px;">
                    <input type="text" class="input-text" id="action-input" placeholder="新しい改善アクションを追加...">
                    <button class="btn-primary" style="background: #eab308;" onclick="openActionAssigneeModal()">
                        <span>+</span> 担当者を選択
                    </button>
                </div>
                
                <div id="action-selected-assignees" class="selected-assignees" style="display: none;">
                    <span>選択中:</span>
                    <div class="assignee-chips" id="action-assignee-chips"></div>
                </div>
            </div>
        </div>
    `;
}

function setupActionEvents() {
    document.getElementById('action-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            openActionAssigneeModal();
        }
    });
}

function openActionAssigneeModal() {
    newActionText = document.getElementById('action-input').value.trim();
    if (!newActionText) {
        alert('改善内容を入力してください');
        return;
    }
    
    currentModalType = 'action';
    selectedAssignees = [];
    showAssigneeModal();
}


// 担当者選択モーダル
function showAssigneeModal() {
    const modal = document.getElementById('assignee-modal');
    const list = document.getElementById('assignee-list');
    
    list.innerHTML = `
        <div class="assignee-option ${selectedAssignees.length === appData.teamMembers.length ? 'selected' : ''}" 
             onclick="toggleAllAssignees()">
            <div class="assignee-option-avatar all">ALL</div>
            <span class="assignee-option-name">全員</span>
            ${selectedAssignees.length === appData.teamMembers.length ? '<span class="assignee-option-check">✓</span>' : ''}
        </div>
        ${appData.teamMembers.map(member => `
            <div class="assignee-option ${selectedAssignees.includes(member.id) ? 'selected' : ''}" 
                 onclick="toggleAssignee(${member.id})">
                <div class="assignee-option-avatar">${member.avatar}</div>
                <span class="assignee-option-name">${member.display_name}</span>
                ${selectedAssignees.includes(member.id) ? '<span class="assignee-option-check">✓</span>' : ''}
            </div>
        `).join('')}
    `;
    
    modal.style.display = 'flex';
}

function closeAssigneeModal() {
    document.getElementById('assignee-modal').style.display = 'none';
    selectedAssignees = [];
}

function toggleAllAssignees() {
    if (selectedAssignees.length === appData.teamMembers.length) {
        selectedAssignees = [];
    } else {
        selectedAssignees = appData.teamMembers.map(m => m.id);
    }
    showAssigneeModal();
}

function toggleAssignee(userId) {
    const index = selectedAssignees.indexOf(userId);
    if (index > -1) {
        selectedAssignees.splice(index, 1);
    } else {
        selectedAssignees.push(userId);
    }
    showAssigneeModal();
}

async function confirmAssignees() {
    if (selectedAssignees.length === 0) {
        alert('担当者を選択してください');
        return;
    }
    
    if (currentModalType === 'plan') {
        await savePlan();
    } else if (currentModalType === 'action') {
        await saveAction();
    }
}

async function savePlan() {
    try {
        const response = await fetch('api/save_plan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                text: newPlanText,
                assignees: selectedAssignees
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAssigneeModal();
            await loadData();
            showView('plan');
        } else {
            alert(data.error || '保存に失敗しました');
        }
    } catch (error) {
        console.error('Plan save error:', error);
        alert('保存に失敗しました');
    }
}

async function saveAction() {
    try {
        const response = await fetch('api/save_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                text: newActionText,
                assignees: selectedAssignees
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAssigneeModal();
            await loadData();
            showView('action');
        } else {
            alert(data.error || '保存に失敗しました');
        }
    } catch (error) {
        console.error('Action save error:', error);
        alert('保存に失敗しました');
    }
}