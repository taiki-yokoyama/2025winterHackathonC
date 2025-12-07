// PLAN画面
function renderPlan() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-plan">P</span>
                Plan - 計画
            </h1>
            
            <div class="card">
                <h3 class="card-title">チームの計画</h3>
                <div class="task-list">
                    ${appData.plans.map(plan => `
                        <div class="task-item task-item-plan">
                            <span class="task-text">${plan.text}</span>
                            <div class="task-assignees">
                                ${plan.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar" title="${member.display_name}">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>

                <div class="input-group" style="margin-top: 16px;">
                    <input type="text" class="input-text" id="plan-input" placeholder="新しい計画を追加...">
                    <button class="btn-primary" onclick="openPlanAssigneeModal()">
                        <span>+</span> 担当者を選択
                    </button>
                </div>
            </div>
        </div>
    `;
}

function setupPlanEvents() {
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
function renderDo() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-do">D</span>
                Do - 実行
            </h1>

            <div class="card">
                <h3 class="card-title">実行タスク</h3>
                <div class="task-list">
                    ${appData.doTasks.map(task => `
                        <div class="task-item ${task.status === '完了' ? 'completed' : ''}">
                            <span class="task-text">${task.text}</span>
                            <div class="task-assignees">
                                ${task.assignees.map(uid => {
                                    const member = appData.teamMembers.find(m => m.id == uid);
                                    return member ? <div class="assignee-avatar">${member.avatar}</div> : '';
                                }).join('')}
                            </div>
                            <button class="btn-status" onclick="toggleTaskStatus(${task.id})">
                                ${task.status === '完了' ? '⏪ 戻す' : '✅ 完了'}
                            </button>
                        </div>
                    `).join('')}
                </div>

                <div class="input-group" style="margin-top: 16px;">
                    <input type="text" class="input-text" id="do-input" placeholder="新しいタスクを追加...">
                    <button class="btn-primary" onclick="openDoAssigneeModal()">
                        <span>+</span> 担当者を選択
                    </button>
                </div>
            </div>
        </div>
    `;
}

function setupDoEvents() {
    document.getElementById('do-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            openDoAssigneeModal();
        }
    });
}

function openDoAssigneeModal() {
    newDoText = document.getElementById('do-input').value.trim();
    if (!newDoText) {
        alert('タスク内容を入力してください');
        return;
    }
    currentModalType = 'do';
    selectedAssignees = [];
    showAssigneeModal();
}
function renderCheck() {
    return `
        <div style="max-width: 1024px; margin: 0 auto;">
            <h1 class="page-title">
                <span class="title-icon title-icon-check">C</span>
                Check - 評価
            </h1>

            <div class="card">
                <h3 class="card-title">チーム評価アンケート</h3>
                <form id="check-form" class="check-form">
                    ${appData.checkItems.map(item => `
                        <div class="check-item">
                            <p class="check-question">${item.text}</p>
                            <div class="rating-options">
                                ${[1,2,3,4,5].map(num => `
                                    <label>
                                        <input type="radio" name="rating_${item.id}" value="${num}" required>
                                        <span>${num}</span>
                                    </label>
                                `).join('')}
                            </div>
                            <textarea class="comment" name="comment_${item.id}" placeholder="コメント（任意）"></textarea>
                        </div>
                    `).join('')}
                    <button type="submit" class="btn-primary" style="margin-top: 24px;">送信</button>
                </form>
            </div>
        </div>
    `;
}

function setupCheckEvents() {
    const form = document.getElementById('check-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const ratings = appData.checkItems.map(item => ({
            item_id: item.id,
            rating: form[`rating_${item.id}`].value,
            comment: form[`comment_${item.id}`].value
        }));

        try {
            const res = await fetch('api/save_check.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ratings })
            });
            const data = await res.json();

            if (data.success) {
                alert('評価を送信しました！');
                await loadData();
                showView('check');
            } else {
                alert(data.error || '送信に失敗しました');
            }
        } catch (err) {
            alert('通信エラーが発生しました');
        }
    });
}
async function loadData() {
    const res = await fetch('api/get_all_data.php');
    const data = await res.json();

    appData = data;

    // 平均データを取得
    const avgRes = await fetch('api/get_check_averages.php');
    const avgData = await avgRes.json();
    if (avgData.success) appData.checkAverages = avgData.data;

    // コメントを取得
    const commentRes = await fetch('api/get_comments.php');
    const commentData = await commentRes.json();
    if (commentData.success) appData.comments = commentData.data;
}