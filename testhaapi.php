<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRPAuth API测试工具</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .response {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .response h3 {
            margin-bottom: 10px;
            color: #333;
        }
        pre {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .endpoint-selector {
            margin-bottom: 30px;
        }
        .param-group {
            margin-top: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .param-group h3 {
            margin-bottom: 15px;
            color: #555;
        }
        .param-row {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>HRPAuth API测试工具</h1>
        
        <div class="form-group">
            <label for="baseUrl">后端地址</label>
            <input type="text" id="baseUrl" value="http://backend.auth.samuelcheston.com/" placeholder="输入后端API地址">
        </div>
        
        <div class="form-group endpoint-selector">
            <label for="endpoint">选择API端点</label>
            <select id="endpoint" onchange="updateParams()">
                <option value="login.php">用户登录 (POST)</option>
                <option value="register.php">用户注册 (POST)</option>
                <option value="user.php">获取用户信息 (GET)</option>
                <option value="logout.php">用户退出登录 (GET)</option>
                <option value="send-verification-code.php">发送邮箱验证码 (POST)</option>
                <option value="verify-code.php">验证邮箱验证码 (POST)</option>
                <option value="send-test-email.php">发送测试邮件 (POST)</option>
                <option value="totpgen.php">生成TOTP验证码 (GET)</option>
            </select>
        </div>
        
        <div id="paramsContainer" class="param-group">
            <h3>请求参数</h3>
            <!-- 参数表单将通过JavaScript动态生成 -->
        </div>
        
        <button onclick="sendRequest()">发送请求</button>
        
        <div class="response">
            <h3>响应结果</h3>
            <pre id="responseResult">等待请求...</pre>
        </div>
    </div>
    
    <script>
        // API端点参数配置
        const endpoints = {
            'login.php': {
                method: 'POST',
                params: [
                    { name: 'email', type: 'email', placeholder: '用户邮箱' },
                    { name: 'password', type: 'password', placeholder: '用户密码' }
                ]
            },
            'register.php': {
                method: 'POST',
                params: [
                    { name: 'email', type: 'email', placeholder: '用户邮箱' },
                    { name: 'nickname', type: 'text', placeholder: '用户昵称' },
                    { name: 'password', type: 'password', placeholder: '用户密码' },
                    { name: 'password2', type: 'password', placeholder: '确认密码' }
                ]
            },
            'user.php': {
                method: 'GET',
                params: []
            },
            'logout.php': {
                method: 'GET',
                params: []
            },
            'send-verification-code.php': {
                method: 'POST',
                params: [
                    { name: 'email', type: 'email', placeholder: '用户邮箱' }
                ]
            },
            'verify-code.php': {
                method: 'POST',
                params: [
                    { name: 'email', type: 'email', placeholder: '用户邮箱' },
                    { name: 'code', type: 'text', placeholder: '验证码' }
                ]
            },
            'send-test-email.php': {
                method: 'POST',
                params: [
                    { name: 'to', type: 'email', placeholder: '收件人邮箱' },
                    { name: 'subject', type: 'text', placeholder: '邮件主题', defaultValue: '测试邮件' },
                    { name: 'message', type: 'textarea', placeholder: '邮件内容' }
                ]
            },
            'totpgen.php': {
                method: 'GET',
                params: [
                    { name: 'secret', type: 'text', placeholder: 'TOTP密钥' }
                ]
            }
        };
        
        // 更新参数表单
        function updateParams() {
            const endpoint = document.getElementById('endpoint').value;
            const paramsContainer = document.getElementById('paramsContainer');
            const config = endpoints[endpoint];
            
            let html = '<h3>请求参数</h3>';
            
            if (config.params.length === 0) {
                html += '<p>此端点不需要参数</p>';
            } else {
                config.params.forEach(param => {
                    html += `<div class="param-row">
                        <label for="${param.name}">${param.name}</label>
                        ${param.type === 'textarea' ? 
                            `<textarea id="${param.name}" placeholder="${param.placeholder}">${param.defaultValue || ''}</textarea>` : 
                            `<input type="${param.type}" id="${param.name}" placeholder="${param.placeholder}" value="${param.defaultValue || ''}">`
                        }
                    </div>`;
                });
            }
            
            paramsContainer.innerHTML = html;
        }
        
        // 发送请求
        async function sendRequest() {
            const baseUrl = document.getElementById('baseUrl').value;
            const endpoint = document.getElementById('endpoint').value;
            const config = endpoints[endpoint];
            
            let url = baseUrl.endsWith('/') ? baseUrl + endpoint : baseUrl + '/' + endpoint;
            
            let data = {};
            config.params.forEach(param => {
                const element = document.getElementById(param.name);
                if (element) {
                    data[param.name] = element.value;
                }
            });
            
            let options = {
                method: config.method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            
            if (config.method === 'POST') {
                options.body = JSON.stringify(data);
            } else if (config.method === 'GET' && Object.keys(data).length > 0) {
                const queryString = new URLSearchParams(data).toString();
                url += '?' + queryString;
            }
            
            const responseResult = document.getElementById('responseResult');
            responseResult.textContent = '发送请求中...';
            
            try {
                const response = await fetch(url, options);
                
                // 先读取响应文本
                const text = await response.text();
                
                // 尝试解析为JSON
                try {
                    const responseData = JSON.parse(text);
                    responseResult.textContent = JSON.stringify(responseData, null, 2);
                } catch (jsonError) {
                    // 如果不是JSON，显示原始文本
                    responseResult.textContent = '非JSON响应:\n' + text;
                }
            } catch (error) {
                responseResult.textContent = '请求失败: ' + error.message;
            }
        }
        
        // 初始化参数表单
        updateParams();
    </script>
</body>
</html>