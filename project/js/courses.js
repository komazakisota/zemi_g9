/**
 * 授業管理JavaScript
 */

let courses = [];
let selectedCourseId = null;

// ページ読み込み時
document.addEventListener('DOMContentLoaded', function() {
    loadCourses();
    
    // 授業選択イベント
    document.getElementById('course-select').addEventListener('change', function() {
        selectedCourseId = this.value ? parseInt(this.value) : null;
        if (selectedCourseId) {
            loadYears(selectedCourseId);
        } else {
            document.getElementById('year-select').innerHTML = '<option value="">年度を選択してください</option>';
            document.getElementById('assignment-area').style.display = 'none';
            document.getElementById('open-chat-btn').style.display = 'none';
        }
    });
});

/**
 * 授業一覧を読み込む
 */
async function loadCourses() {
    try {
        const response = await fetch('api/courses/get_courses.php');
        const data = await response.json();
        
        if (data.success) {
            courses = data.courses;
            renderCourseList();
            renderCourseSelect();
        }
    } catch (error) {
        console.error('Error loading courses:', error);
    }
}

/**
 * 授業一覧を描画（サイドバー）
 */
function renderCourseList() {
    const courseList = document.getElementById('course-list');
    
    if (courses.length === 0) {
        courseList.innerHTML = '<p class="empty-message">授業が登録されていません<br>+ 授業 ボタンから追加してください</p>';
        return;
    }
    
    courseList.innerHTML = courses.map(course => {
        const avgRating = parseFloat(course.avg_rating) || 0;
        return `
            <div class="course-item ${course.course_id === selectedCourseId ? 'active' : ''}" 
                 data-course-id="${course.course_id}"
                 draggable="true"
                 onclick="selectCourse(${course.course_id})">
                <div class="course-name">${escapeHtml(course.course_name)}</div>
                ${avgRating > 0 ? `<div class="course-rating">★ ${avgRating.toFixed(1)}</div>` : ''}
            </div>
        `;
    }).join('');
    
    // ドラッグ&ドロップイベントを設定
    setupCourseDragDrop();
}

/**
 * 授業セレクトボックスを描画
 */
function renderCourseSelect() {
    const courseSelect = document.getElementById('course-select');
    
    courseSelect.innerHTML = '<option value="">授業を選択してください</option>' +
        courses.map(course => `
            <option value="${course.course_id}" ${course.course_id === selectedCourseId ? 'selected' : ''}>
                ${escapeHtml(course.course_name)}
            </option>
        `).join('');
}

/**
 * 授業を選択
 */
function selectCourse(courseId) {
    selectedCourseId = courseId;
    document.getElementById('course-select').value = courseId;
    
    // UI更新
    renderCourseList();
    
    // 年度を読み込む
    loadYears(courseId);
}

/**
 * 授業追加モーダルを開く
 */
function openAddCourseModal() {
    document.getElementById('add-course-modal').classList.add('active');
}

/**
 * 授業追加モーダルを閉じる
 */
function closeAddCourseModal() {
    document.getElementById('add-course-modal').classList.remove('active');
    document.getElementById('add-course-form').reset();
}

/**
 * 授業を追加
 */
async function addCourse() {
    const form = document.getElementById('add-course-form');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('api/courses/add_course.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAddCourseModal();
            loadCourses();
            alert('授業を追加しました');
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error adding course:', error);
        alert('通信エラーが発生しました');
    }
}

/**
 * HTMLエスケープ
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}