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
    <title>Smart Student Attendance System - Dashboard</title>
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
            --input-bg: rgba(15, 23, 42, 0.6);
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

        /* Sidebar Navigation Layout (Desktop Standard) */
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
            z-index: 10;
        }

        .sidebar-brand {
            font-size: 18px;
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

        /* Main Workspace Content Area (Desktop Standard) */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            margin-left: 260px;
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
            font-size: 35px;
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
            margin-bottom: 30px;
        }

        /* Dynamic Panel Switching Properties */
        .tab-panel {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        .tab-panel.active-panel {
            display: block;
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

        /* Forms Elements */
        form {
            display: grid;
            gap: 15px;
            max-width: 600px;
            margin-top: 20px;
        }

        input, select {
            padding: 12px;
            background: var(--input-bg);
            border: 1px solid var(--border-glass);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: var(--accent-primary);
        }

        input::placeholder {
            color: var(--text-muted);
        }

        select option {
            background: var(--bg-dark);
            color: var(--text-main);
        }

        form button {
            background: var(--accent-primary);
            color: var(--text-main);
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }

        form button:hover {
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Attendance Data Table Formatting */
        .log-table-wrapper {
            width: 100%;
            overflow-x: auto; /* Enables sliding overflow data grid safety fallback */
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .log-table th, .log-table td {
            text-align: center;
            padding: 14px;
            border-bottom: 1px solid var(--border-glass);
        }

        .log-table th { 
            color: var(--text-muted); 
            font-size: 14px; 
            text-transform: uppercase;
            font-weight: 600;
        }

        .log-table tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .present { color: var(--accent-success); font-weight: bold; }
        .absent { color: var(--accent-danger); font-weight: bold; }

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

        /* =======================================================
           DYNAMIC RESPONSIVENESS MODULE (CSS MEDIA QUERIES)
           ======================================================= */
        @media screen and (max-width: 992px) {
            body {
                flex-direction: column; /* Stacks navigation blocks above workspace */
            }

            /* Transforms sidebar container into standard horizontal fluid top block banner */
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 20px;
                border-right: none;
                border-bottom: 1px solid var(--border-glass);
            }

            .sidebar-brand {
                margin-bottom: 20px;
                text-align: center;
            }

            .nav-links {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                justify-content: center;
            }

            .nav-item button {
                padding: 10px 14px;
                font-size: 14px;
                margin-bottom: 0;
            }

            .logout-btn {
                margin-top: 15px;
                width: 100%;
                box-sizing: border-box;
            }

            /* Adjust workspace offset spacing vectors */
            .main-content {
                margin-left: 0;
                padding: 24px;
            }

            .header-panel {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .status-badge {
                width: 100%;
                text-align: center;
                box-sizing: border-box;
            }

            .workspace-panel {
                padding: 20px;
            }
        }

        @media screen and (max-width: 480px) {
            .nav-links {
                flex-direction: column;
                width: 100%;
            }

            .nav-item {
                width: 100%;
            }

            .nav-item button {
                text-align: center;
            }
            
            .log-table th, .log-table td {
                padding: 10px 6px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">ATTENDANCE SYSTEM</div>
        <ul class="nav-links">
            <li class="nav-item active" data-tab="overview"><button>Dashboard Overview</button></li>
            <li class="nav-item" data-tab="register"><button>Take Attendance</button></li>
            <li class="nav-item" data-tab="records"><button>Attendance Logs</button></li>
            <li class="nav-item" data-tab="security"><button>Account Settings</button></li>
        </ul>
        <a href="logout.php" class="logout-btn">Log Out Portal</a>
    </aside>

    <main class="main-content">
        <header class="header-panel">
            <div>
                <h1 id="console-title">Classroom Management Portal</h1>
                <p style="color: var(--text-muted); margin: 4px 0 0 0;">Real-time classroom monitoring and daily student log management.</p>
            </div>
            <div class="status-badge">System Status: Active</div>
        </header>

        <section class="metrics-grid">
            <div class="card">
                <div class="card-title">Total Students</div>
                <div class="card-value" id="totalStudents" style="color: #60a5fa;">0</div>
            </div>
            <div class="card">
                <div class="card-title">Present Today</div>
                <div class="card-value" id="presentStudents" style="color: var(--accent-success);">0</div>
            </div>
            <div class="card">
                <div class="card-title">Absent Today</div>
                <div class="card-value" id="absentStudents" style="color: var(--accent-danger);">0</div>
            </div>
        </section>

        <section class="workspace-panel">
            
            <div id="panel-overview" class="tab-panel active-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Instructor Administration Terminal</h3>
                <p style="font-size: 16px; line-height: 1.6; color: #cbd5e1;">
                    Welcome back, <strong class="user-highlight"><?php echo htmlspecialchars(ucwords($_SESSION['username'])); ?></strong>. 
                    Manage your classroom presence records, review student rosters, and generate attendance insights seamlessly.
                </p>
                <div style="background: rgba(15, 23, 42, 0.4); padding: 24px; border-radius: 12px; border: 1px solid var(--border-glass); margin-top: 24px; display: grid; gap: 12px;">
                    <p style="margin: 0; font-size: 14px; color: #cbd5e1;"><span style="margin-right: 8px;">📊</span> <strong>Connection Status:</strong> <span style="color: var(--accent-success);">Active & Synced</span></p>
                    <p style="margin: 0; font-size: 14px; color: #cbd5e1;"><span style="margin-right: 8px;">🔒</span> <strong>System Security:</strong> Secured Administration Session</p>
                    <p style="margin: 0; font-size: 14px; color: #cbd5e1;"><span style="margin-right: 8px;">⏰</span> <strong>Auto-Logout:</strong> Logs out after 15 minutes of inactivity</p>
                </div>
            </div>

            <div id="panel-register" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Record New Attendance</h3>
                <p style="color: var(--text-muted);">Enter student details below to log their daily classroom attendance status.</p>
                
                <form id="studentForm">
                    <input type="text" id="name" placeholder="Enter Student Name" required>
                    <input type="text" id="admission" placeholder="Enter Admission Number" required>
                    <input type="text" id="course" placeholder="Enter Course" required>
                    <select id="status">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                    <button type="submit">Save Attendance Record</button>
                </form>
            </div>

            <div id="panel-records" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Student Attendance Records</h3>
                <p style="color: var(--text-muted); margin-bottom: 15px;">Comprehensive log of all student entries recorded for this academic session:</p>
                
                <div class="log-table-wrapper">
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Admission No</th>
                                <th>Course</th>
                                <th>Status State</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTable">
                            </tbody>
                    </table>
                </div>
            </div>

            <div id="panel-security" class="tab-panel">
                <h3 style="margin-top: 0; font-size: 20px;">Account Settings & Preferences</h3>
                <p style="color: #cbd5e1;">Active configurations running securely in the background environment:</p>
                <div style="padding: 10px 0;">
                    <label style="display:block; margin-bottom: 10px; color: var(--text-muted);">
                        <input type="checkbox" checked disabled> Enable Session Hijacking Protection
                    </label>
                    <label style="display:block; margin-bottom: 10px; color: var(--text-muted);">
                        <input type="checkbox" checked disabled> Enable Automated Data Integrity Check
                    </label>
                    <label style="display:block; margin-bottom: 10px; color: var(--text-muted);">
                        <input type="checkbox" checked disabled> Form Sanitization & XSS Protections
                    </label>
                </div>
            </div>

        </section>
    </main>

    <script>
        // Tab switching controller
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active-panel'));
                
                const targetTab = this.getAttribute('data-tab');
                document.getElementById('panel-' + targetTab).classList.add('active-panel');
                
                const titleElement = document.getElementById('console-title');
                if(targetTab === 'overview') titleElement.innerText = "Classroom Management Portal";
                if(targetTab === 'register') titleElement.innerText = "Record New Attendance";
                if(targetTab === 'records') titleElement.innerText = "Student Attendance Records";
                if(targetTab === 'security') titleElement.innerText = "Account Settings & Preferences";
            });
        });

        // Backend Integration DOM Hooks
        const form = document.getElementById("studentForm");
        const table = document.getElementById("attendanceTable");

        const totalDisplay = document.getElementById("totalStudents");
        const presentDisplay = document.getElementById("presentStudents");
        const absentDisplay = document.getElementById("absentStudents");

        // LOAD DATA FROM DATABASE ASYNCHRONOUSLY
        function loadAttendance() {
            fetch("fetch.php")
                .then(res => res.json())
                .then(data => {
                    table.innerHTML = "";
                    let total = 0;
                    let present = 0;
                    let absent = 0;

                    data.forEach(student => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${student.name}</td>
                            <td>${student.admission}</td>
                            <td>${student.course}</td>
                            <td class="${student.status === 'Present' ? 'present' : 'absent'}">
                                ${student.status}
                            </td>
                        `;
                        table.appendChild(row);

                        total++;
                        if (student.status === "Present") {
                            present++;
                        } else {
                            absent++;
                        }
                    });

                    totalDisplay.textContent = total;
                    presentDisplay.textContent = present;
                    absentDisplay.textContent = absent;
                })
                .catch(err => console.error("Error fetching data: ", err));
        }

        // DISPATCH DATA AND COMMUNICATE RECORD WITH BACKEND DATABASE
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append("name", document.getElementById("name").value);
            formData.append("admission", document.getElementById("admission").value);
            formData.append("course", document.getElementById("course").value);
            formData.append("status", document.getElementById("status").value);

            fetch("save.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(result => {
                if (result.trim() === "success") {
                    form.reset();
                    loadAttendance();
                    
                    alert("Registered successfully!");
                    document.querySelector('[data-tab="records"]').click();
                } else {
                    alert("System Notification: " + result);
                }
            })
            .catch(err => console.error("Transmission error: ", err));
        });

        // Initial background initialization load
        loadAttendance();
    </script>
</body>
</html>