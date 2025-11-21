/**
 * 課題管理JavaScript
 */

let assignments = [];
let currentFilter = 'incomplete'; // incomplete or completed

// フィルターボタンイベント
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // アクティブ状態を切り替え
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // フィルターを適用
            currentFilter = this.dataset.filter;
            renderAssignmentList();
        });
    });
    
    // 課題追加モーダルの期限設定トグル
    document.getElementById('has-time').addEventListener('change', function() {
        document.getElementById('time-field').style.display = this.checked ? 'block' : 'none';
    });
});

/**
 * 期限フィールドの表示/非表示
 */
function toggleDeadlineFields() {
    const hasDeadline = document.querySelector('input[name="has_deadline"]:checked').value;
    const deadlineFields = document.getElementById('deadline-fields');
    
    if (hasDeadline === 'yes') {
        deadlineFields.style.display = 'block';
    } else {
        deadlineFields.style.display = 'none';
    }
}

/**
 * 課題一覧を読み込む
 */
async function loadAssignments(courseYearId) {
    try {
        const response = await fetch(`api/assignments/get_assignments.php?course_year_id=${courseYearId}`);
        const data = await response.json();
        
        if (data.success) {
            assignments = data.assignments;
            renderAssignmentList();
        }
    } catch (error) {
        console.error('Error loading assignments:', error);
    }
}

/**
 * 課題一覧を描画
 */
function renderAssignmentList() {
    const assignmentList = document.getElementById('assignment-list');
    
    // フィルタリング
    const filteredAssignments = assignments.filter(a => {
        if (currentFilter === 'incomplete') {
            return !a.is_completed;
        } else {
            return a.is_completed;
        }
    });
    
    if (filteredAssignments.length === 0) {
        const message = currentFilter === 'incomplete' ? '未提出の課題はありません' : '完了した課題はありません';
        assignmentList.innerHTML = `<p class="empty-message">${message}</p>`;
        return;
    }
    
    assignmentList.innerHTML = filteredAssignments.map(assignment => {
        const deadlineText = formatDeadline(assignment.deadline, assignment.has_time);
        const isOverdue = isDeadlineOverdue(assignment.deadline);
        const avgRating = parseFloat(assignment.avg_rating) || 0;
        const ratingStars = avgRating > 0 ? '★'.repeat(Math.round(avgRating)) + '☆'.repeat(5 - Math.round(avgRating)) : '☆☆☆☆☆';
        
        return `
            <div class="assignment-card ${assignment.is_completed ? 'completed' : ''}" 
                 data-assignment-id="${assignment.assignment_id}"
                 draggable="true">
                <div class="assignment-info">
                    <div class="assignment-header">
                        <span class="assignment-icon">📝</span>
                        <span class="assignment-name">${escapeHtml(assignment.assignment_name)}</span>
                    </div>
                    <div class="assignment-meta">
                        <div class="assignment-rating" onclick="showEvaluationDetail(${assignment.assignment_id})">
                            ${ratingStars} ${avgRating > 0 ? '(' + avgRating.toFixed(1) + ')' : '(未評価)'}
                        </div>
                        <div class="assignment-deadline ${isOverdue ? 'overdue' : ''}">
                            期限: ${deadlineText}
                        </div>
                    </div>
                </div>
                <div class="assignment-actions">
                    ${!assignment.is_completed ? `
                        <button class="btn btn-success btn-sm" onclick="completeAssignment(${assignment.assignment_id}, '${escapeHtml(assignment.assignment_name)}')">
                            完了
                        </button>
                    ` : `
                        <span style="color: #50C878;">✓ 完了済み</span>
                    `}
                </div>
            </div>
        `;
    }).join('');
    
    // ドラッグ&ドロップイベントを設定
    setupAssignmentDragDrop();
}

/**
 * 期限をフォーマット
 */
function formatDeadline(deadline, hasTime) {
    if (!deadline) {
        return '期限なし';
    }
    
    const date = new Date(deadline);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    
    if (hasTime) {
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        return `${year}/${month}/${day} ${hour}:${minute}`;
    } else {
        return `${year}/${month}/${day}`;
    }
}

/**
 * 期限切れかチェック
 */
function isDeadlineOverdue(deadline) {
    if (!deadline) return false;
    return new Date(deadline) < new Date();
}

/**
 * 課題追加モーダルを開く
 */
async function openAddAssignmentModal() {
    if (!selectedYearId) {
        alert('先に年度を選択してください');
        return;
    }
    document.getElementById('add-assignment-modal').classList.add('active');
    
    // 課題名の候補を取得
    try {
        const selectedYear = years.find(y => y.course_year_id === selectedYearId);
        if (!selectedYear) return;
        
        const response = await fetch(
            `api/assignments/get_assignment_names.php?course_id=${selectedCourseId}&year=${selectedYear.year}`
        );
        const data = await response.json();
        
        if (data.success && data.assignment_names.length > 0) {
            const assignmentInput = document.getElementById('assignment-name');
            
            if (assignmentAutocomplete) {
                assignmentAutocomplete.updateSuggestions(data.assignment_names);
            } else {
                assignmentAutocomplete = new Autocomplete(assignmentInput, data.assignment_names);
            }
        }
    } catch (error) {
        console.error('Error loading assignment names:', error);
    }
}

/**
 * 課題追加モーダルを閉じる
 */
function closeAddAssignmentModal() {
    document.getElementById('add-assignment-modal').classList.remove('active');
    document.getElementById('add-assignment-form').reset();
    document.getElementById('deadline-fields').style.display = 'none';
    document.getElementById('time-field').style.display = 'none';
    
    if (assignmentAutocomplete) {
        assignmentAutocomplete.closeList();
    }
}

/**
 * 課題を追加
 */
async function addAssignment() {
    if (!selectedYearId) {
        alert('先に年度を選択してください');
        return;
    }
    
    const form = document.getElementById('add-assignment-form');
    const hasDeadline = document.querySelector('input[name="has_deadline"]:checked').value;
    
    const formData = new FormData();
    formData.append('course_year_id', selectedYearId);
    formData.append('assignment_name', document.getElementById('assignment-name').value);
    
    if (hasDeadline === 'yes') {
        const deadlineDate = document.getElementById('deadline-date').value;
        const hasTime = document.getElementById('has-time').checked;
        
        if (!deadlineDate) {
            alert('日付を入力してください');
            return;
        }
        
        let deadline = deadlineDate;
        if (hasTime) {
            const deadlineTime = document.getElementById('deadline-time').value || '23:59';
            deadline += ' ' + deadlineTime + ':00';
            formData.append('has_time', '1');
        } else {
            deadline += ' 00:00:00';
            formData.append('has_time', '0');
        }
        
        formData.append('deadline', deadline);
    } else {
        formData.append('deadline', '');
        formData.append('has_time', '0');
    }
    
    try {
        const response = await fetch('api/assignments/add_assignment.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAddAssignmentModal();
            loadAssignments(selectedYearId);
            alert('課題を追加しました');
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error adding assignment:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * 課題を完了
 */
async function completeAssignment(assignmentId, assignmentName) {
    // 課題評価フォームモーダルを開く
    document.getElementById('eval-assignment-id').value = assignmentId;
    document.getElementById('eval-assignment-name').textContent = assignmentName;
    document.getElementById('evaluation-form-modal').classList.add('active');
    
    // 星評価をリセット
    document.querySelectorAll('#rating-input span').forEach(star => {
        star.textContent = '☆';
        star.classList.remove('selected');
    });
    document.getElementById('rating-value').value = '';
    document.getElementById('eval-comment').value = '';
}

/**
 * 課題並び順をリセット
 */
async function resetAssignmentOrder() {
    if (!selectedYearId) return;
    
    if (!confirm('課題の並び順を期限順にリセットしますか？')) {
        return;
    }
    
    try {
        const response = await fetch('api/assignments/reset_assignment_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_year_id: selectedYearId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadAssignments(selectedYearId);
            alert('課題の並び順をリセットしました');
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error resetting assignment order:', error);
        alert('通信エラーが発生しました');
    }
}