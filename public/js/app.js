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
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            ${lowestRatedItem ? `
                <div class="priority-alert">
                    <h3 class="alert-title">⚠️ 最優先課題</h3>
                    <p class="alert-text">
                        「${lowestRatedItem.text}」について、平均${lowestRatedItem.avg_rating}点。
                        改善アクションを設定しましょう！
                    </p>
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
                                    return member ? `<div class="assignee-avatar" title="${member.display_name}">${member.avatar}</div>` : '';
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

async function saveAction() {
    try {
        const res = await fetch('api/save_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                text: newActionText,
                assignees: selectedAssignees
            })
        });

        const data = await res.json();
        if (data.success) {
            closeAssigneeModal();
            await loadData();
            showView('action');
        } else {
            alert(data.error || '保存に失敗しました');
        }
    } catch (error) {
        console.error('Action save error:', error);
        alert('通信エラーが発生しました');
    }
}

let appData = {};
let currentModalType = null;
let selectedAssignees = [];
let newPlanText = '';
let newActionText = '';

async function initApp() {
    await loadData();
    showView('plan');
}

async function showView(view) {
    const app = document.getElementById('app');
    if (!appData) return;

    if (view === 'plan') app.innerHTML = renderPlan();
    if (view === 'do') app.innerHTML = renderDo();
    if (view === 'check') app.innerHTML = renderCheck();
    if (view === 'action') app.innerHTML = renderAction();

    if (view === 'plan') setupPlanEvents();
    if (view === 'do') setupDoEvents();
    if (view === 'check') setupCheckEvents();
    if (view === 'action') setupActionEvents();
}

document.addEventListener('DOMContentLoaded', initApp);
async function loadData() {
    const res = await fetch('api/get_data.php');
    const data = await res.json();

    if (data.success) {
        appData = data;
    } else {
        alert('データの取得に失敗しました');
    }
}