/**
 * ログイン処理
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // エラーメッセージをクリア
        errorMessage.style.display = 'none';
        
        // フォームデータを取得
        const formData = new FormData(form);
        
        try {
            // ログインAPIを呼び出し
            const response = await fetch('../api/auth/login.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // ログイン成功 → ホーム画面へ
                window.location.href = 'home.php';
            } else {
                // エラーメッセージを表示
                errorMessage.textContent = data.error || 'ログインに失敗しました';
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = '通信エラーが発生しました';
            errorMessage.style.display = 'block';
        }
    });
});
