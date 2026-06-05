<?php
require_once __DIR__ . '/includes/security.php';

// Route Protection Layer
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=Unauthorized access. Please log in first.");
    exit();
}

// Inactivity Timeout Logic (15 minutes)
$timeout_duration = 900; 
if (isset($_SESSION['last_login_time']) && (time() - $_SESSION['last_login_time']) > $timeout_duration) {
    header("Location: logout.php?msg=Session expired due to inactivity.");
    exit();
}
$_SESSION['last_login_time'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Cloud Dashboard</title>
    <style>
        :root {
            --bg-dark: #0f172a;
            --panel-bg: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-success: #10b981;
            --accent-danger: #ef4444;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation Layout */
        .sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--border-glass);
            display: flex;
            flex-direction: column;
            padding: 24px;
            box-sizing: border-box;
            position: fixed;
            height: 100vh;
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 40px;
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-item button {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            display: block;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-item.active button, .nav-item button:hover {
            background: var(--accent-primary);
            color: var(--text-main);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Main Workspace Content Area */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            margin-left: 260px; /* Leave space for fixed sidebar */
            overflow-y: auto;
            box-sizing: border-box;
        }

        .header-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .status-badge {
            padding: 6px 14px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-success);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        /* Glassmorphic Metrics Card Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Core Workspace Area Sections */
        .workspace-panel {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            padding: 32px;
            border-radius: 16px;
            min-height: 200px;
        }

        /* Dynamic Panel Switching Properties */
        .tab-panel {
            display: none; /* Hide panels by default */
            animation: fadeIn 0.4s ease forwards;
        }

        .tab-panel.active-panel {
            display: block; /* Only show active class panel */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-highlight {
            color: #60a5fa;
            border-bottom: 2px dashed rgba(96, 165, 250, 0.4);
            padding-bottom: 2px;
        }

        /* System Logs Table Formatting */
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .log-table th, .log-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border-glass);
        }
        .log-table th { color: var(--text-muted); font-size: 14px; }
        .log-success { color: var(--accent-success); font-weight: bold; }

        .logout-btn {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 24px;
            background: var(--accent-danger);
            color: var(--text-main);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .logout-btn:hover {
            background: #dc2626;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">CORTEX SECURITY</div>
        <ul class="nav-links">
            <li class="nav-item active" data-tab="overview"><button>Overview</button></li>
            <li class="nav-item" data-tab="analytics"><button>Analytics</button></li>
            <li class="nav-item" data-tab="system-logs"><button>System Logs</button></li>
            <li class="nav-item" data-tab="settings"><button>Settings</button></li>
        </ul>
        <a href="logout.php" class="logout-btn">Terminate Session</a>
    </aside>

    <main class="main-content">
        <header class="header-panel">
            <div>
                <h1 id="console-title">System Control Console</h1>
                <p style="color: var(--text-muted); margin: 4px 0 0 0;">Real-time node administration and session token monitoring.</p>
            </div>
            <div class="status-badge">System Node: Secure</div>
        </header>

        <section class="metrics-grid">
            <div class="card">
                <div class="card-title">Active Environment</div>
                <div class="card-value" style="color: #60a5fa;">Localhost</div>
            </div>
            <div class="card">
                <div class="card-title">Token Guard</div>
                <div class="card-value" style="color: var(--accent-success);">Anti-CSRF</div>
            </div>
            <div class="card">
                <div class="card-title">Inactivity Gate</div>
                <div class="card-value">15m</div>
            </div>
        </section>

        <section class="workspace-panel">
            
            <div id="panel-overview" class="tab-panel active-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Authenticated Secure Workspace</h3>
                <p style="font-size: 16px; line-height: 1.6; color: #cbd5e1;">
                    Welcome back, operator <strong class="user-highlight"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. 
                    Your browser cryptographic handshake session variables are rendering dynamically via the local session cache loop.
                </p>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 0;">
                    All directory structures outside this gateway are hidden behind server-side routing logic arrays.
                </p>
            </div>

            <div id="panel-analytics" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Real-Time System Analytics</h3>
                <p style="color: #cbd5e1;">Tracking server session handshakes and metrics allocation loops:</p>
                <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; border: 1px solid var(--border-glass);">
                    <p style="margin: 5px 0;">📊 <strong>Active Session Traffic:</strong> 100% Operational</p>
                    <p style="margin: 5px 0;">🔒 <strong>Encryption Protocol:</strong> PASSWORD_BCRYPT (Cost: 10)</p>
                    <p style="margin: 5px 0;">⚡ <strong>Response Latency:</strong> 0.04ms (Optimal)</p>
                </div>
            </div>

            <div id="panel-system-logs" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Security Event Ledger</h3>
                <p style="color: var(--text-muted);">Latest administrative interactions registered by the session core:</p>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Event Action</th>
                            <th>Status State</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s'); ?></td>
                            <td>Session Token Regenerated (ID Cycler)</td>
                            <td class="log-success">SUCCESS</td>
                        </tr>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', strtotime('-1 minute')); ?></td>
                            <td>User Password Hash Verified via `password_verify`</td>
                            <td class="log-success">SUCCESS</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="panel-settings" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Console Configurations</h3>
                <p style="color: #cbd5e1;">Manage active session tracking and gateway parameters details:</p>
                <div style="padding: 10px 0;">
                    <label style="display:block; margin-bottom: 10px; color: var(--text-muted);">
                        <input type="checkbox" checked disabled> Force Session Fixation Protection Layer
                    </label>
                    <label style="display:block; margin-bottom: 10px; color: var(--text-muted);">
                        <input type="checkbox" checked disabled> Enable XSS Input Sanitization Filters
                    </label>
                </div>
            </div>

        </section>
    </main>

    <script>
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active styling color from all navigation elements
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                
                // Add active styling to the currently clicked item
                this.classList.add('active');
                
                // Hide all main interface workspace content panels
                document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active-panel'));
                
                // Dynamically targeted panel mapping matching the data attribute
                const targetTab = this.getAttribute('data-tab');
                document.getElementById('panel-' + targetTab).classList.add('active-panel');
                
                // Dynamically shift the main layout title header text smoothly
                const titleElement = document.getElementById('console-title');
                if(targetTab === 'overview') titleElement.innerText = "System Control Console";
                if(targetTab === 'analytics') titleElement.innerText = "Performance Analytics";
                if(targetTab === 'system-logs') titleElement.innerText = "Security Core Ledger";
                if(targetTab === 'settings') titleElement.innerText = "Console Preferences";
            });
        });
    </script>

</body>
</html>