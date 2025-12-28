<?php
require_once __DIR__ . '/../api/db.php';

// Get developer filter if set
$developerFilter = $_GET['developer'] ?? null;

// Build the query with optional developer filter
if ($developerFilter) {
    $stmt = $db->prepare("SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id AND g.developer_id = :developer_id");
    $stmt->execute(['developer_id' => $developerFilter]);
    $allGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $allGames = $db->query("SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id")->fetchAll(PDO::FETCH_ASSOC);
}
$approvedGames = $db->query(" SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id AND is_approved = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$developers = $db->query("
         SELECT u.*, COUNT(g.id) AS game_count
    FROM users u
    LEFT JOIN games g ON g.developer_id = u.id
     WHERE u.type = 'game_developer'
    GROUP BY u.id
    ")->fetchAll(PDO::FETCH_ASSOC);
$developersGame = $db->query("SELECT COUNT(*) as game_count FROM games g JOIN users u WHERE u.id=g.developer_id AND u.type = 'game_developer'")->fetchAll(PDO::FETCH_ASSOC);
$pendingGames = $db->query(" SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id =g.developer_id AND is_approved = 0 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$activeSection = $_GET["section"] ?? "dashboard";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTIS256 - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/admin_sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h4><i class="fa-solid fa-shield-halved"></i> Admin Panel</h4>
                <p>Game Store Management</p>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?section=dashboard" class="sidebar-menu-link <?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=pending-games" class="sidebar-menu-link <?php echo $activeSection === 'pending-games' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-clock"></i>
                        <span>Pending Games</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=approved-games" class="sidebar-menu-link <?php echo $activeSection === 'approved-games' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Approved Games</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=developers" class="sidebar-menu-link <?php echo $activeSection === 'developers' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Developers</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=all-games" class="sidebar-menu-link <?php echo $activeSection === 'all-games' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gamepad"></i>
                        <span>All Games</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="index.php" class="btn btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Exit Admin</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <!-- Dashboard Section -->
            <?php if ($activeSection === 'dashboard'): ?>
                <div class="admin-header">
                    <h1>Dashboard</h1>
                    <p>Welcome to the admin panel. Here's an overview of your game store.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo count($pendingGames); ?></div>
                        <div class="stat-box-label">Pending Approvals</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo count($approvedGames); ?></div>
                        <div class="stat-box-label">Approved Games</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo count($developers); ?></div>
                        <div class="stat-box-label">Active Developers</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo count($approvedGames) + count($pendingGames); ?></div>
                        <div class="stat-box-label">Total Games</div>
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fa-solid fa-bell"></i>
                        Recent Activity
                    </h2>
                    <p style="color: #a0a0a0;">You have <?php echo count($pendingGames); ?> games waiting for approval.</p>
                </div>
            <?php endif; ?>

            <!-- Pending Games Section -->
            <?php if ($activeSection === 'pending-games'): ?>
                <div class="admin-header">
                    <h1>Pending Game Approvals</h1>
                    <p>Review and approve or reject newly submitted games.</p>
                </div>

                <div class="section-card">
                    <?php if (empty($pendingGames)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-circle-check"></i>
                            <h3>No Pending Games</h3>
                            <p>All games have been reviewed!</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Game</th>
                                    <th>Name</th>
                                    <th>Developer</th>
                                    <th>Genre</th>
                                    <th>Price</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingGames as $game): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $path = $game['logo_path'] ?? null;
                                            $imgSrc = $path
                                                ?? 'https://r2.ctis256.sezertetik.dev/' . $game['file_path']
                                            ?>
                                            <img
                                                src="<?= htmlspecialchars($imgSrc) ?>"
                                                alt="<?= htmlspecialchars($game['name'] ?? 'Game') ?>"
                                                class="game-thumbnail">
                                        </td>
                                        <td style="color: #ffffff;"><?= $game['name'] ?></td>
                                        <td><?= $game['username'] ?></td>
                                        <td><?= $game['genre'] ?></td>
                                        <td style="color: #4CAF50;">$<?= $game['price'] ?></td>
                                        <td><?= $game['created_at'] ?></td>
                                        <td>
                                            <button class="btn-action btn-approve" data-game-id="<?= $game['id'] ?>"><i class="fa-solid fa-check"></i> Approve</button>
                                            <button class="btn-action btn-reject" data-game-id="<?= $game['id'] ?>"><i class="fa-solid fa-times"></i> Reject</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Approved Games Section -->
            <?php if ($activeSection === 'approved-games'): ?>
                <div class="admin-header">
                    <h1>Approved Games</h1>
                    <p>Manage all approved games in the store.</p>
                </div>

                <div class="section-card">
                    <?php if (empty($approvedGames)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-gamepad"></i>
                            <h3>No Approved Games</h3>
                            <p>No games have been approved yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Game</th>
                                    <th>Name</th>
                                    <th>Developer</th>
                                    <th>Genre</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvedGames as $game): ?>
                                    <tr>
                                        <td><img src="<?= $game['logo_path'] ?>" alt="<?= $game['name'] ?>" class="game-thumbnail"></td>
                                        <td style="color: #ffffff;"><?= $game['name'] ?></td>
                                        <td><?= $game['username'] ?></td>
                                        <td><?= $game['genre'] ?></td>
                                        <td style="color: #4CAF50;">$<?= $game['price'] ?></td>
                                        <td>
                                            <?php if (isset($game['is_approved']) && $game['is_approved'] == 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn-action btn-edit"><i class="fa-solid fa-pen"></i> Edit</button>
                                            <button class="btn-action btn-delete"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Developers Section -->
            <?php if ($activeSection === 'developers'): ?>
                <div class="admin-header">
                    <h1>Game Developers</h1>
                    <p>Manage game developer accounts and their games.</p>
                </div>

                <div class="section-card">
                    <?php if (empty($developers)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-users"></i>
                            <h3>No Developers</h3>
                            <p>No developers have registered yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Games Published</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($developers as $dev): ?>
                                    <tr>
                                        <td style="color: #ffffff;"><i class="fa-solid fa-user"></i> <?= $dev['username'] ?></td>
                                        <td><?= $dev['email'] ?></td>
                                        <td><?= $dev['game_count'] ?> games</td>
                                        <td>
                                            <?php if (isset($dev['is_verified']) && $dev['is_verified'] == 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $dev['registered_at'] ?></td>
                                        <td>
                                            <button class="btn-action btn-view" data-developer-id="<?= $dev['id'] ?>"><i class="fa-solid fa-eye"></i> View Games</button>
                                            <button class="btn-action btn-deactivate"><i class="fa-solid fa-ban"></i> Deactivate</button>
                                            <button class="btn-action btn-delete"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- All Games Section -->
            <?php if ($activeSection === 'all-games'): ?>
                <div class="admin-header">
                    <h1>All Games</h1>
                    <p>View and manage all games in the system.</p>
                    <div style="margin-top: 20px; display: flex; align-items: center; gap: 10px;">
                        <label for="developer-filter" style="color: #ffffff; font-weight: 500;">
                            <i class="fa-solid fa-filter"></i> Filter by Developer:
                        </label>
                        <select id="developer-filter" class="form-select" style="width: 300px; background-color: #2a2a2a; color: #ffffff; border: 1px solid #444; border-radius: 8px; padding: 8px 12px;">
                            <option value="">All Developers</option>
                            <?php foreach ($developers as $dev): ?>
                                <option value="<?= $dev['id'] ?>" <?= $developerFilter == $dev['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dev['username']) ?> (<?= $dev['game_count'] ?> games)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="section-card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Game</th>
                                <th>Name</th>
                                <th>Developer</th>
                                <th>Genre</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allGames as $game): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $path = $game['logo_path'] ?? null;
                                        $imgSrc = $path
                                            ?? 'https://r2.ctis256.sezertetik.dev/' . $game['file_path'];
                                        ?>
                                        <img src="<?= $imgSrc ?>" alt="<?= $game['name'] ?>" class="game-thumbnail">
                                    </td>
                                    <td style="color: #ffffff;"><?= $game['name'] ?></td>
                                    <td><?= $game['username'] ?></td>
                                    <td><?= $game['genre'] ?></td>
                                    <td style="color: #4CAF50;">$<?= $game['price'] ?></td>
                                    <td>
                                        <?php if (isset($game['is_approved']) && $game['is_approved'] == 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif (isset($game['is_approved']) && $game['is_approved'] == 0) : ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif (isset($game['is_approved']) && $game['is_approved'] == -1) : ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit"><i class="fa-solid fa-pen"></i> Edit</button>
                                        <button class="btn-action btn-delete"><i class="fa-solid fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add confirmation for delete actions
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    // Handle delete action
                    console.log('Delete confirmed');
                }
            });
        });

        // Add confirmation for deactivate actions
        document.querySelectorAll('.btn-deactivate').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to deactivate this developer account?')) {
                    // Handle deactivate action
                    console.log('Deactivate confirmed');
                }
            });
        });

        // Handle approve/reject actions
        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Approve this game for publication?')) {
                    const gameId = this.getAttribute('data-game-id');
                    const row = this.closest('tr');

                    try {
                        const response = await fetch(`../api/admin/games/${gameId}/approve`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            row.style.backgroundColor = '#1b4d1b';
                            setTimeout(() => {
                                row.remove();
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Failed to approve game: ' + (data.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error approving game:', error);
                        alert('An error occurred while approving the game.');
                    }
                }
            });
        });

        document.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Reject this game? The developer will be notified.')) {
                    const gameId = this.getAttribute('data-game-id');
                    const row = this.closest('tr');

                    try {
                        const response = await fetch(`../api/admin/games/${gameId}/reject`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            row.style.backgroundColor = '#4d1b1b';
                            setTimeout(() => {
                                row.remove();
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Failed to reject game: ' + (data.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error rejecting game:', error);
                        alert('An error occurred while rejecting the game.');
                    }
                }
            });
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-view') || e.target.closest('.btn-view')) {
                e.preventDefault();
                const button = e.target.classList.contains('btn-view') ? e.target : e.target.closest('.btn-view');
                const developerId = button.getAttribute('data-developer-id');
                window.location.href = `admin.php?section=all-games&developer=${developerId}`;
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-deactivate')) {
                e.preventDefault();
                window.location.href = 'admin.php?section=all-games';
            }
        });

        // Handle developer filter change in all-games section
        const developerFilter = document.getElementById('developer-filter');
        if (developerFilter) {
            developerFilter.addEventListener('change', function() {
                const developerId = this.value;
                if (developerId) {
                    window.location.href = `admin.php?section=all-games&developer=${developerId}`;
                } else {
                    window.location.href = 'admin.php?section=all-games';
                }
            });
        }
    </script>
</body>

</html>