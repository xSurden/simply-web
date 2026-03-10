<div class="profile-card">
    <div class="header-accent"></div>
    <div class="card-body">
        <div class="avatar">
            <span>SW</span>
        </div>
        
        <h2 class="name"><?= $name ?? ".." ?></h2>
        <p class="role"><?= $description ?? ".." ?></p>
        
        <div class="stats">
            <div class="stat-item">
                <span class="label">App Version</span>
                <span class="value active"><?= $version ?? ".." ?></span>
            </div>
            <div class="stat-item">
                <span class="label">App Branch</span>
                <span class="value"><?= $branch ?? ".." ?></span>
            </div>
        </div>
        
        <a href="https://abrhosting.com" class="profile-btn">View our sponser</a>
    </div>
</div>

<style>
    .profile-card {
        width: 320px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 20px auto;
        border: 1px solid #e1e8ed;
    }

    .header-accent {
        height: 80px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .card-body {
        padding: 0 24px 24px 24px;
        text-align: center;
        position: relative;
    }

    .avatar {
        width: 80px;
        height: 80px;
        background: #f3f4f6;
        border: 4px solid #ffffff;
        border-radius: 50%;
        margin: -40px auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #4f46e5;
        font-size: 1.5rem;
    }

    .name {
        margin: 0;
        color: #1f2937;
        font-size: 1.25rem;
    }

    .role {
        margin: 4px 0 20px;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .stats {
        display: flex;
        justify-content: space-around;
        padding: 15px 0;
        border-top: 1px solid #f3f4f6;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 20px;
    }

    .stat-item .label {
        display: block;
        font-size: 0.75rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-item .value {
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .value.active {
        color: #10b981;
    }

    .profile-btn {
        display: block;
        background: #4f46e5;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background 0.2s;
    }

    .profile-btn:hover {
        background: #4338ca;
    }
</style>