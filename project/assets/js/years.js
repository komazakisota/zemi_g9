/**
 * 年度管理JavaScript
 */

let years = [];
let selectedYearId = null;
let selectedYearNumber = null; // 年度の数字を保存（例: 2024, 2025）

// 年度選択イベント
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('year-select').addEventListener('change', function() {
        selectedYearId = this.value ? parseInt(this.value) : null;
        
        // 選択した年度の数字を保存
        if (selectedYearId) {
            const selectedYear = years.find(y => y.course_year_id === selectedYearId);
            if (selectedYear) {
                selectedYearNumber = selectedYear.year;
            }
        }
        
        if (selectedYearId) {
            document.getElementById('assignment-area').style.display = 'block';
            document.getElementById('initial-message').style.display = 'none';
            document.getElementById('open-chat-btn').style.display = 'inline-block';
            loadAssignments(selectedYearId);
        } else {
            document.getElementById('assignment-area').style.display = 'none';
            document.getElementById('open-chat-btn').style.display = 'none';
        }
    });
});

/**
 * 年度一覧を読み込む
 */
async function loadYears(courseId) {
    try {
        const response = await fetch(`../api/years/get_years.php?course_id=${courseId}`);
        const data = await response.json();
        
        if (data.success) {
            years = data.years;
            renderYearSelect();
            
            // 前回選択していた年度の「数字」（例: 2024）で探す
            let yearToSelect = null;
            
            if (selectedYearNumber) {
                // 同じ年度の数字（例: 2024）があるか確認
                yearToSelect = years.find(y => y.year === selectedYearNumber);
            }
            
            if (yearToSelect) {
                // 同じ年度が存在する場合はそれを維持
                selectedYearId = yearToSelect.course_year_id;
                selectedYearNumber = yearToSelect.year;
            } else if (years.length > 0) {
                // 存在しない場合は最新年度を自動選択
                const latestYear = years[0];
                selectedYearId = latestYear.course_year_id;
                selectedYearNumber = latestYear.year;
            }
            
            // UIを更新
            if (selectedYearId) {
                document.getElementById('year-select').value = selectedYearId;
                document.getElementById('assignment-area').style.display = 'block';
                document.getElementById('initial-message').style.display = 'none';
                document.getElementById('open-chat-btn').style.display = 'inline-block';
                loadAssignments(selectedYearId);
            }
        }
    } catch (error) {
        console.error('Error loading years:', error);
    }
}

/**
 * 年度セレクトボックスを描画
 */
function renderYearSelect() {
    const yearSelect = document.getElementById('year-select');
    
    if (years.length === 0) {
        yearSelect.innerHTML = '<option value="">年度を追加してください</option>';
        return;
    }
    
    yearSelect.innerHTML = '<option value="">年度を選択してください</option>' +
        years.map(year => `
            <option value="${year.course_year_id}">
                ${year.year}年度
            </option>
        `).join('');
}

/**
 * 年度追加モーダルを開く
 */
function openAddYearModal() {
    if (!selectedCourseId) {
        alert('先に授業を選択してください');
        return;
    }
    document.getElementById('add-year-modal').classList.add('active');
}

/**
 * 年度追加モーダルを閉じる
 */
function closeAddYearModal() {
    document.getElementById('add-year-modal').classList.remove('active');
    document.getElementById('add-year-form').reset();
}

/**
 * 年度を追加
 */
async function addYear() {
    if (!selectedCourseId) {
        alert('先に授業を選択してください');
        return;
    }
    
    const form = document.getElementById('add-year-form');
    const formData = new FormData(form);
    formData.append('course_id', selectedCourseId);
    
    try {
        const response = await fetch('../api/years/add_year.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAddYearModal();
            loadYears(selectedCourseId);
            alert('年度を追加しました（トークルームも自動作成されました）');
        } else {
            alert('エラー: ' + data.error);
        }
    } catch (error) {
        console.error('Error adding year:', error);
        alert('通信エラーが発生しました');
    }
}