/**
 * 新規登録処理
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // メッセージをクリア
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        
        // パスワード確認
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        
        if (password !== passwordConfirm) {
            errorMessage.textContent = 'パスワードが一致しません';
            errorMessage.style.display = 'block';
            return;
        }
        
        // フォームデータを取得
        const formData = new FormData(form);
        
        try {
            // 登録APIを呼び出し
            const response = await fetch('api/auth/register.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // 登録成功
                successMessage.textContent = '登録が完了しました。3秒後にログイン画面に移動します...';
                successMessage.style.display = 'block';
                
                // フォームをクリア
                form.reset();
                
                // 3秒後にログイン画面へ
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 3000);
            } else {
                // エラーメッセージを表示
                errorMessage.textContent = data.error || '登録に失敗しました';
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = '通信エラーが発生しました';
            errorMessage.style.display = 'block';
        }
    });
});
