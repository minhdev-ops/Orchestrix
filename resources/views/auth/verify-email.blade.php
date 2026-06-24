<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác thực email - AgriVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8faf5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 420px; width: 100%; padding: 20px; }
        .card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px 32px; text-align: center; }
        .logo { font-size: 24px; font-weight: 700; color: #486730; margin-bottom: 8px; }
        .icon { width: 64px; height: 64px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .icon .material-symbols-outlined { font-size: 32px; color: #486730; }
        h1 { font-size: 20px; color: #1a1a1a; margin-bottom: 8px; }
        .subtitle { color: #666; font-size: 14px; margin-bottom: 24px; }
        .email { color: #486730; font-weight: 600; }
        .code-inputs { display: flex; gap: 8px; justify-content: center; margin-bottom: 24px; }
        .code-input { width: 48px; height: 56px; border: 2px solid #e0e0e0; border-radius: 8px; text-align: center; font-size: 24px; font-weight: 600; transition: all 0.2s; }
        .code-input:focus { border-color: #486730; outline: none; box-shadow: 0 0 0 3px rgba(72,103,48,0.1); }
        .btn { width: 100%; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #486730; color: white; }
        .btn-primary:hover { background: #3d5728; }
        .btn-primary:disabled { background: #ccc; cursor: not-allowed; }
        .btn-secondary { background: transparent; color: #486730; margin-top: 12px; }
        .btn-secondary:hover { background: #f5f5f5; }
        .error { background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: none; }
        .success { background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: none; }
        .resend-text { color: #666; font-size: 13px; margin-top: 16px; }
        .timer { color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">AgriVerse</div>
            
            <div class="icon">
                <span class="material-symbols-outlined">mark_email_read</span>
            </div>
            
            <h1>Xác thực email của bạn</h1>
            <p class="subtitle">
                Chúng tôi đã gửi mã xác thực 6 chữ số đến<br>
                <span class="email" id="email">{{ $email }}</span>
            </p>
            
            <div id="error" class="error"></div>
            <div id="success" class="success"></div>
            
            <div class="code-inputs">
                <input type="text" class="code-input" maxlength="1" data-index="0" inputmode="numeric">
                <input type="text" class="code-input" maxlength="1" data-index="1" inputmode="numeric">
                <input type="text" class="code-input" maxlength="1" data-index="2" inputmode="numeric">
                <input type="text" class="code-input" maxlength="1" data-index="3" inputmode="numeric">
                <input type="text" class="code-input" maxlength="1" data-index="4" inputmode="numeric">
                <input type="text" class="code-input" maxlength="1" data-index="5" inputmode="numeric">
            </div>
            
            <button class="btn btn-primary" id="verifyBtn" disabled>
                Xác thực
            </button>
            
            <p class="resend-text">
                Không nhận được mã? 
                <button class="btn btn-secondary" id="resendBtn">Gửi lại</button>
                <span class="timer" id="timer"></span>
            </p>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.code-input');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const errorDiv = document.getElementById('error');
        const successDiv = document.getElementById('success');
        const timerSpan = document.getElementById('timer');
        
        let countdown = 0;
        let timerInterval = null;

        // Handle input
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value;
                
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                updateVerifyButton();
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });
                
                if (digits.length > 0) {
                    inputs[Math.min(digits.length, 5)].focus();
                }
                
                updateVerifyButton();
            });
        });

        function updateVerifyButton() {
            const code = getCode();
            verifyBtn.disabled = code.length !== 6;
        }

        function getCode() {
            return Array.from(inputs).map(i => i.value).join('');
        }

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            successDiv.style.display = 'none';
        }

        function showSuccess(message) {
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            errorDiv.style.display = 'none';
        }

        function hideMessages() {
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
        }

        // Verify
        verifyBtn.addEventListener('click', async () => {
            const code = getCode();
            if (code.length !== 6) return;
            
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Đang xác thực...';
            hideMessages();
            
            try {
                const response = await fetch('/auth/verify-email/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect || '/';
                    }, 1500);
                } else {
                    showError(data.message);
                    inputs.forEach(i => i.value = '');
                    inputs[0].focus();
                    updateVerifyButton();
                }
            } catch (error) {
                showError('Đã xảy ra lỗi. Vui lòng thử lại.');
            } finally {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Xác thực';
                updateVerifyButton();
            }
        });

        // Resend
        resendBtn.addEventListener('click', async () => {
            if (countdown > 0) return;
            
            resendBtn.disabled = true;
            hideMessages();
            
            try {
                const response = await fetch('/auth/verify-email/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Mã xác thực đã được gửi lại.');
                    startCountdown(60);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Không thể gửi mã. Vui lòng thử lại.');
            } finally {
                resendBtn.disabled = false;
            }
        });

        function startCountdown(seconds) {
            countdown = seconds;
            resendBtn.style.display = 'none';
            
            timerInterval = setInterval(() => {
                countdown--;
                timerSpan.textContent = `Gửi lại sau ${countdown}s`;
                
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    timerSpan.textContent = '';
                    resendBtn.style.display = 'inline';
                }
            }, 1000);
        }

        // Auto send on page load
        (async () => {
            try {
                await fetch('/auth/verify-email/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                startCountdown(60);
            } catch (e) {}
        })();

        inputs[0].focus();
    </script>
</body>
</html>
