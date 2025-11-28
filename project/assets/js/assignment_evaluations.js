/**
 * 課題評価JavaScript
 */

// 星評価の選択
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('#rating-input span');
    
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('rating-value').value = rating;
            
            // 星を更新
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.textContent = '★';
                    s.classList.add('selected');
                } else {
                    s.textContent = '☆';
                    s.classList.remove('selected');
                }
            });
        });
        
        // ホバーエフェクト
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.textContent = '★';
                } else {
                    s.textContent = '☆';
                }
            });
        });
    });
    
    // マウスアウト時は選択された星に戻す
    document.getElementById('rating-input').addEventListener('mouseleave', function() {
        const selectedRating = parseInt(document.getElementById('rating-value').value) || 0;
        ratingStars.forEach((s, index) => {
            if (index < selectedRating) {
                s.textContent = '★';
            } else {
                s.textContent = '☆';
            }
        });
    });
});

/**
 * 課題評価フォームを閉じる
 */
function closeEvaluationForm() {
    if (!confirm('評価を中止しますか？（課題は未完了のままになります）')) {
        return;
    }
    
    document.getElementById('evaluation-form-modal').classList.remove('active');
    document.getElementById('evaluation-form').reset();
}

/**
 * 課題評価を投稿
 */
async function submitEvaluation() {
    const assignmentId = document.getElementById('eval-assignment-id').value;
    const rating = document.getElementById('rating-value').value;
    const comment = document.getElementById('eval-comment').value;
    
    if (!rating) {
        alert('星評価を選択してください');
        return;
    }
    
    const formData = new FormData();
    formData.append('assignment_id', assignmentId);
    formData.append('rating', rating);
    formData.append('comment', comment);
    
    try {
        // 評価を投稿
        const evalResponse = await fetch('../api/assignment_evaluations/add_evaluation.php', {
            method: 'POST',
            body: formData
        });
        
        const evalData = await evalResponse.json();
        
        if (!evalData.success) {
            alert('エラー: ' + evalData.error);
            return;
        }
        
        // 課題を完了にする
        const completeFormData = new FormData();
        completeFormData.append('assignment_id', assignmentId);
        
        const completeResponse = await fetch('../api/assignments/complete_assignment.php', {
            method: 'POST',
            body: completeFormData
        });
        
        const completeData = await completeResponse.json();
        
        if (completeData.success) {
            document.getElementById('evaluation-form-modal').classList.remove('active');
            document.getElementById('evaluation-form').reset();
            
            // 課題一覧を再読み込み
            if (selectedYearId) {
                loadAssignments(selectedYearId);
            }
            
            alert('評価を投稿しました！');
        } else {
            alert('エラー: ' + completeData.error);
        }
    } catch (error) {
        console.error('Error submitting evaluation:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * 課題評価詳細を表示
 */
async function showEvaluationDetail(assignmentId) {
    document.getElementById('evaluation-detail-modal').classList.add('active');
    document.getElementById('evaluation-detail-body').innerHTML = '<p class="loading-text">読み込み中...</p>';
    
    try {
        // 課題情報と評価一覧を取得
        const response = await fetch(`../api/assignment_evaluations/get_evaluations.php?assignment_id=${assignmentId}`);
        const data = await response.json();
        
        if (!data.success) {
            alert('エラー: ' + data.error);
            return;
        }
        
        const assignment = data.assignment;
        const evaluations = data.evaluations;
        const avgRating = data.avg_rating || 0;
        const myEvaluation = evaluations.find(e => e.is_mine);
        const otherEvaluations = evaluations.filter(e => !e.is_mine);
        
        // モーダルタイトルを更新
        document.getElementById('detail-assignment-name').textContent = assignment.assignment_name;
        
        // コンテンツを描画
        let html = `
            <div class="evaluation-summary">
                <h3>平均評価</h3>
                <div class="rating-large">${'★'.repeat(Math.round(avgRating))}${'☆'.repeat(5 - Math.round(avgRating))}</div>
                <p>${avgRating.toFixed(1)} / 5.0 (${evaluations.length}件の評価)</p>
            </div>
        `;
        
        // 自分の評価
        if (myEvaluation) {
            html += `
                <div class="evaluation-section">
                    <h4>あなたの評価</h4>
                    <div class="evaluation-item my-evaluation">
                        <div class="evaluation-header">
                            <span class="evaluation-user">${escapeHtml(myEvaluation.username)}</span>
                            <span class="evaluation-date">${formatDate(myEvaluation.created_at)}</span>
                        </div>
                        <div class="evaluation-rating">${'★'.repeat(myEvaluation.rating)}${'☆'.repeat(5 - myEvaluation.rating)}</div>
                        ${myEvaluation.comment ? `<div class="evaluation-comment">${escapeHtml(myEvaluation.comment)}</div>` : ''}
                    </div>
                </div>
            `;
        }
        
        // 他の人の評価
        if (otherEvaluations.length > 0) {
            html += `
                <div class="evaluation-section">
                    <h4>他の人の評価</h4>
            `;
            
            otherEvaluations.forEach(evaluation => {
                html += `
                    <div class="evaluation-item">
                        <div class="evaluation-header">
                            <span class="evaluation-user">${escapeHtml(evaluation.username)}</span>
                            <span class="evaluation-date">${formatDate(evaluation.created_at)}</span>
                        </div>
                        <div class="evaluation-rating">${'★'.repeat(evaluation.rating)}${'☆'.repeat(5 - evaluation.rating)}</div>
                        ${evaluation.comment ? `<div class="evaluation-comment">${escapeHtml(evaluation.comment)}</div>` : ''}
                    </div>
                `;
            });
            
            html += `</div>`;
        }
        
        if (evaluations.length === 0) {
            html += '<p class="empty-message">まだ評価がありません</p>';
        }
        
        document.getElementById('evaluation-detail-body').innerHTML = html;
        
    } catch (error) {
        console.error('Error loading evaluation detail:', error);
        document.getElementById('evaluation-detail-body').innerHTML = '<p class="error-message">読み込みに失敗しました</p>';
    }
}

/**
 * 課題評価詳細モーダルを閉じる
 */
function closeEvaluationDetail() {
    document.getElementById('evaluation-detail-modal').classList.remove('active');
}

/**
 * 日付フォーマット
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}/${month}/${day}`;
}
