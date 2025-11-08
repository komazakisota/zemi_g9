/**
 * 授業評価JavaScript
 */

// HTMLエスケープ関数
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

let courseEvaluations = [];

// ページ読み込み時
document.addEventListener('DOMContentLoaded', function() {
    loadCourseEvaluations();
});

/**
 * 授業評価一覧を読み込む
 */
async function loadCourseEvaluations() {
    try {
        const response = await fetch('api/course_evaluations/get_course_evaluations.php');
        const data = await response.json();
        
        if (data.success) {
            courseEvaluations = data.courses;
            renderCourseEvaluationList();
        }
    } catch (error) {
        console.error('Error loading course evaluations:', error);
    }
}

/**
 * 授業評価一覧を描画
 */
function renderCourseEvaluationList() {
    const list = document.getElementById('course-evaluation-list');
    
    if (courseEvaluations.length === 0) {
        list.innerHTML = '<p class="empty-message">授業が登録されていません</p>';
        return;
    }
    
    list.innerHTML = courseEvaluations.map(course => {
        const avgRating = parseFloat(course.avg_rating) || 0;
        const ratingStars = avgRating > 0 ? '★'.repeat(Math.round(avgRating)) + '☆'.repeat(5 - Math.round(avgRating)) : '☆☆☆☆☆';
        const hasEvaluated = course.has_my_evaluation;
        
        return `
            <div class="course-evaluation-card" onclick="openCourseEvaluation(${course.course_id}, '${escapeHtml(course.course_name)}')">
                <h3>${escapeHtml(course.course_name)}</h3>
                <div class="course-rating-display">
                    <span class="course-stars">${ratingStars}</span>
                    <span class="course-rating-text">${avgRating.toFixed(1)} (${course.evaluation_count}件)</span>
                </div>
                <p class="evaluation-status ${hasEvaluated ? 'evaluated' : ''}">
                    ${hasEvaluated ? '✓ 評価済み' : '未評価'}
                </p>
            </div>
        `;
    }).join('');
}

/**
 * 授業評価モーダルを開く
 */
async function openCourseEvaluation(courseId, courseName) {
    document.getElementById('course-evaluation-modal').classList.add('active');
    document.getElementById('modal-course-name').textContent = courseName;
    document.getElementById('modal-body').innerHTML = '<p class="loading-text">読み込み中...</p>';
    
    try {
        const response = await fetch(`api/course_evaluations/get_course_detail.php?course_id=${courseId}`);
        const data = await response.json();
        
        if (data.success) {
            renderCourseEvaluationModal(data, courseId, courseName);
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error loading course evaluation:', error);
        document.getElementById('modal-body').innerHTML = '<p class="error-message">読み込みに失敗しました</p>';
    }
}

/**
 * 授業評価モーダルを描画
 */
function renderCourseEvaluationModal(data, courseId, courseName) {
    const myEvaluation = data.my_evaluation;
    const otherEvaluations = data.other_evaluations;
    const avgRating = data.avg_rating || 0;
    
    let html = '';
    
    // 自分の評価
    if (myEvaluation) {
        html += `
            <div class="my-course-evaluation">
                <h4>あなたの評価</h4>
                <div class="course-evaluation-rating">${'★'.repeat(myEvaluation.rating)}${'☆'.repeat(5 - myEvaluation.rating)}</div>
                ${myEvaluation.comment ? `<div class="course-evaluation-comment">${escapeHtml(myEvaluation.comment)}</div>` : ''}
                <div class="evaluation-actions">
                    <button class="btn btn-danger btn-sm" onclick="deleteCourseEvaluation(${courseId})">削除</button>
                </div>
            </div>
        `;
    } else {
        // 評価フォーム
        html += `
            <div class="evaluation-form-section">
                <h4>この授業を評価する</h4>
                <div class="rating-input-large" id="course-rating-input">
                    <span data-rating="1">☆</span>
                    <span data-rating="2">☆</span>
                    <span data-rating="3">☆</span>
                    <span data-rating="4">☆</span>
                    <span data-rating="5">☆</span>
                </div>
                <input type="hidden" id="course-rating-value">
                <div class="form-group">
                    <label for="course-comment">コメント（任意）</label>
                    <textarea id="course-comment" rows="4" placeholder="授業の感想を入力"></textarea>
                </div>
                <button class="btn btn-primary" onclick="submitCourseEvaluation(${courseId})">評価を投稿</button>
            </div>
        `;
        
        // 星評価イベントを設定
        setTimeout(() => setupCourseRatingInput(), 100);
    }
    
    // 他の人の評価
    if (otherEvaluations && otherEvaluations.length > 0) {
        html += `
            <div class="other-evaluations">
                <h4>他の人の評価 (平均: ${avgRating.toFixed(1)} / 5.0)</h4>
        `;
        
        otherEvaluations.forEach(evaluation => {
            html += `
                <div class="course-evaluation-item">
                    <div class="course-evaluation-header">
                        <span class="course-evaluation-user">${escapeHtml(evaluation.username)}</span>
                        <span class="course-evaluation-date">${formatDate(evaluation.created_at)}</span>
                    </div>
                    <div class="course-evaluation-rating">${'★'.repeat(evaluation.rating)}${'☆'.repeat(5 - evaluation.rating)}</div>
                    ${evaluation.comment ? `<div class="course-evaluation-comment">${escapeHtml(evaluation.comment)}</div>` : ''}
                </div>
            `;
        });
        
        html += `</div>`;
    }
    
    document.getElementById('modal-body').innerHTML = html;
}

/**
 * 星評価入力を設定
 */
function setupCourseRatingInput() {
    const ratingStars = document.querySelectorAll('#course-rating-input span');
    
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('course-rating-value').value = rating;
            
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
    });
}

/**
 * 授業評価を投稿
 */
async function submitCourseEvaluation(courseId) {
    const rating = document.getElementById('course-rating-value').value;
    const comment = document.getElementById('course-comment').value;
    
    if (!rating) {
        alert('星評価を選択してください');
        return;
    }
    
    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('rating', rating);
    formData.append('comment', comment);
    
    try {
        const response = await fetch('api/course_evaluations/add_course_evaluation.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('評価を投稿しました！');
            closeCourseEvaluationModal();
            loadCourseEvaluations();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error submitting evaluation:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * 授業評価を削除
 */
async function deleteCourseEvaluation(courseId) {
    if (!confirm('評価を削除しますか？')) {
        return;
    }
    
    try {
        const response = await fetch('api/course_evaluations/delete_course_evaluation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ course_id: courseId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('評価を削除しました');
            closeCourseEvaluationModal();
            loadCourseEvaluations();
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error deleting evaluation:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * 授業評価モーダルを閉じる
 */
function closeCourseEvaluationModal() {
    document.getElementById('course-evaluation-modal').classList.remove('active');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.getFullYear() + '/' + 
           String(date.getMonth() + 1).padStart(2, '0') + '/' + 
           String(date.getDate()).padStart(2, '0');
}