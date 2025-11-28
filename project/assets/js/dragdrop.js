/**
 * ドラッグ&ドロップ機能JavaScript
 */

let draggedElement = null;

/**
 * 授業のドラッグ&ドロップを設定
 */
function setupCourseDragDrop() {
    const courseItems = document.querySelectorAll('.course-item');
    
    courseItems.forEach(item => {
        item.addEventListener('dragstart', handleCourseDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleCourseDrop);
        item.addEventListener('dragend', handleDragEnd);
    });
}

/**
 * 課題のドラッグ&ドロップを設定
 */
function setupAssignmentDragDrop() {
    const assignmentCards = document.querySelectorAll('.assignment-card');
    
    assignmentCards.forEach(card => {
        card.addEventListener('dragstart', handleAssignmentDragStart);
        card.addEventListener('dragover', handleDragOver);
        card.addEventListener('drop', handleAssignmentDrop);
        card.addEventListener('dragend', handleDragEnd);
    });
}

/**
 * ドラッグ開始（授業）
 */
function handleCourseDragStart(e) {
    draggedElement = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

/**
 * ドラッグ開始（課題）
 */
function handleAssignmentDragStart(e) {
    draggedElement = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

/**
 * ドラッグオーバー
 */
function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    
    const parent = this.parentNode;
    const draggingItem = parent.querySelector('.dragging');
    
    if (draggingItem && draggingItem !== this) {
        const rect = this.getBoundingClientRect();
        const nextElement = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
        
        if (nextElement) {
            parent.insertBefore(draggingItem, this.nextSibling);
        } else {
            parent.insertBefore(draggingItem, this);
        }
    }
    
    return false;
}

/**
 * ドロップ（授業）
 */
function handleCourseDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    // 並び順を保存
    saveCourseOrder();
    
    return false;
}

/**
 * ドロップ（課題）
 */
function handleAssignmentDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    // 並び順を保存
    saveAssignmentOrder();
    
    return false;
}

/**
 * ドラッグ終了
 */
function handleDragEnd(e) {
    this.classList.remove('dragging');
    draggedElement = null;
}

/**
 * 授業の並び順を保存
 */
async function saveCourseOrder() {
    const courseItems = document.querySelectorAll('.course-item');
    const courseOrder = Array.from(courseItems).map((item, index) => ({
        course_id: parseInt(item.dataset.courseId),
        display_order: index + 1
    }));
    
    try {
        const response = await fetch('../api/courses/reorder_courses.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_order: courseOrder
            })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Error saving course order:', data.error);
        }
    } catch (error) {
        console.error('Error saving course order:', error);
    }
}

/**
 * 課題の並び順を保存
 */
async function saveAssignmentOrder() {
    if (!selectedYearId) return;
    
    const assignmentCards = document.querySelectorAll('.assignment-card');
    const assignmentOrder = Array.from(assignmentCards).map((card, index) => ({
        assignment_id: parseInt(card.dataset.assignmentId),
        display_order: index + 1
    }));
    
    try {
        const response = await fetch('../api/assignments/reorder_assignments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_year_id: selectedYearId,
                assignment_order: assignmentOrder
            })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Error saving assignment order:', data.error);
        }
    } catch (error) {
        console.error('Error saving assignment order:', error);
    }
}
