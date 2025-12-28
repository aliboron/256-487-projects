<?php
    require_once __DIR__ . '/../api/db.php';
   $allGames = $db->query("SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id")->fetchAll(PDO::FETCH_ASSOC);
    $approvedGames = $db->query(" SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id AND is_approved = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
   $developers = $db->query("
         SELECT u.*, COUNT(g.id) AS game_count
    FROM users u
    LEFT JOIN games g ON g.developer_id = u.id
     WHERE 
     u.type = 'game_developer'
    GROUP BY u.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    $developersGame=$db->query("SELECT COUNT(*) as game_count FROM games g JOIN users u WHERE u.id=g.developer_id AND u.type = 'game_developer'")->fetchAll(PDO::FETCH_ASSOC);
    $pendingGames = $db->query(" SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id =g.developer_id AND is_approved = 0 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $activeSection=$_GET["section"]??"dashboard";
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
                                        <td><?=  $game['username'] ?></td>
                                        <td><?=   $game['genre'] ?></td>
                                        <td style="color: #4CAF50;">$<?= $game['price'] ?></td>
                                        <td><?=  $game['created_at'] ?></td>
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
                                        <td style="color: #ffffff;"><?=  $game['name'] ?></td>
                                        <td><?= $game['username'] ?></td>
                                        <td><?=  $game['genre'] ?></td>
                                        <td style="color: #4CAF50;">$<?=  $game['price'] ?></td>
                                        <td>
                                        <?php if (isset($game['is_approved'])&&$game['is_approved']==1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn-action btn-edit"
                                                data-game-id="<?php echo $game['id']; ?>"
                                                data-game-name="<?php echo htmlspecialchars($game['name']); ?>"
                                                data-game-genre="<?php echo htmlspecialchars($game['genre']); ?>"
                                                data-game-description="<?php echo htmlspecialchars($game['description']); ?>"
                                                data-game-price="<?php echo $game['price']; ?>">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" id="gameDelete" data-game-id="<?php echo $game['id']; ?>" data-game-name="<?php echo htmlspecialchars($game['name']); ?>"><i class="fa-solid fa-trash"></i> Delete</button>
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
                                        <td><?=   $dev['email'] ?></td>
                                        <td><?=    $dev['game_count'] ?> games</td>
                                        <td>
                                        <?php if (isset($dev['is_verified'])&&$dev['is_verified']==1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif(isset($dev['is_verified'])&&$dev['is_verified']==0): ?>
                                            <span class="badge badge-danger">Passive</span>   
                                        <?php endif; ?>
                                        </td>
                                        <td><?=   $dev['registered_at'] ?></td>
                                        <td>
                                            <button class="btn-action btn-view"><i class="fa-solid fa-eye"></i> View Games</button>
                                            <?php if (isset($dev['is_verified'])&&$dev['is_verified']==1): ?>
                                            <button class="btn-action btn-deactivate" id="btnDeveloperDeactivate" data-developer-id='<?= $dev['id']?>'><i class="fa-solid fa-ban"></i> Deactivate</button>
                                            <?php elseif(isset($dev['is_verified'])&&$dev['is_verified']==0): ?>
                                            <button class="btn-action btn-activate" id="btnDeveloperActivate" data-developer-id='<?= $dev['id']?>'><i class="fa-solid fa-check"></i> Activate</button>
                                         <?php endif; ?>
                                            
                                            <button class="btn-action btn-delete"id="btnDeveloperDel" data-developer-id='<?= $dev['id']?>'><i class="fa-solid fa-trash"></i> Delete</button>
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
                                                ?? 'https://r2.ctis256.sezertetik.dev/' . $game['file_path']
                                            ?>
                                            <img
                                                src="<?= htmlspecialchars($imgSrc) ?>"
                                                alt="<?= htmlspecialchars($game['name'] ?? 'Game') ?>"
                                                class="game-thumbnail">
                                        </td>
                                    <td style="color: #ffffff;"><?= $game['name'] ?></td>
                                    <td><?=  $game['username'] ?></td>
                                    <td><?=  $game['genre'] ?></td>
                                    <td style="color: #4CAF50;">$<?= $game['price'] ?></td>
                                    <td>
                                        <?php if (isset($game['is_approved'])&&$game['is_approved']==1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif (isset($game['is_approved'])&&$game['is_approved']==0) : ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif(isset($game['is_approved'])&&$game['is_approved']==-1) : ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                            <button class="btn-action btn-edit"
                                                data-game-id="<?php echo $game['id']; ?>"
                                                data-game-name="<?php echo htmlspecialchars($game['name']); ?>"
                                                data-game-genre="<?php echo htmlspecialchars($game['genre']); ?>"
                                                data-game-description="<?php echo htmlspecialchars($game['description']); ?>"
                                                data-game-price="<?php echo $game['price']; ?>">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" id="gameDelete" data-game-id="<?php echo $game['id']; ?>" data-game-name="<?php echo htmlspecialchars($game['name']); ?>"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
      <!-- Edit Game Modal -->
    <div class="modal fade" id="editGameModal" tabindex="-1" aria-labelledby="editGameModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: #1a1a1a; color: #ffffff;">
                <div class="modal-header" style="border-bottom: 1px solid #2d2d2d;">
                    <h5 class="modal-title" id="editGameModalLabel">
                        <i class="fa-solid fa-pen"></i> Edit Game
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <form id="editGameForm">
                        <input type="hidden" id="editGameId">

                        <div class="form-group" style=" margin-bottom:1rem;">
                            <label for="editGameName" style="display: block; margin-bottom:0.5rem;">
                                <i class="fa-solid fa-gamepad"></i> Game Name *
                            </label>
                            <input type="text" id="editGameName" class="form-control" required
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;">
                        </div>

                        <div class="form-group" style=" margin-bottom:1rem;">
                            <label for="editGameGenre" style="display: block; margin-bottom:0.5rem;">
                                <i class="fa-solid fa-tag"></i> Genre *
                            </label>
                            <select id="editGameGenre" class="form-control" required
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;">
                                <option value="">Select Genre</option>
                                <option value="Action">Action</option>
                                <option value="Adventure">Adventure</option>
                                <option value="RPG">RPG</option>
                                <option value="Strategy">Strategy</option>
                                <option value="Shooter">Shooter</option>
                                <option value="Sports">Sports</option>
                                <option value="Racing">Racing</option>
                                <option value="Fighting">Fighting</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:1rem;">
                            <label for="editGameDescription" style="display: block; margin-bottom:0.5rem;">
                                <i class="fa-solid fa-align-left"></i> Description *
                            </label>
                            <textarea id="editGameDescription" class="form-control" required rows="4"
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;"></textarea>
                        </div>

                        <div class="form-group" style=" margin-bottom:1rem;">
                            <label for="editGamePrice" style="display: block; margin-bottom:0.5rem;">
                                <i class="fa-solid fa-dollar-sign"></i> Price (USD) *
                            </label>
                            <input type="number" id="editGamePrice" class="form-control" step="0.01" min="0" required
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #2d2d2d;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveGameChanges">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        
       
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-view')) {
                    e.preventDefault();
                    window.location.href = 'admin.php?section=all-games';
            }
        });
       
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
        document.addEventListener('click', async function (e) {
        if (e.target.id === 'btnDeveloperDel') {
        e.preventDefault();

        const developerId = e.target.dataset.developerId;
        if (!developerId) {
            alert('Developer ID missing');
            return;
        }

        if (!confirm('Are you sure you want to delete this developer? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`../api/admin/developer/${developerId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok || result.success === false) {
                alert(result.message || 'Delete failed');
                return;
            }

            alert('Developer deleted successfully');

        } catch (error) {
            console.error(error);
            alert('Network error occurred');
        }
    }
    });
    document.addEventListener('click', async function (e) {
     if (e.target.id === 'btnDeveloperDeactivate') {
        e.preventDefault();

        const developerId = e.target.dataset.developerId;
        if (!developerId) {
            alert('Developer ID not found');
            return;
        }

        if (!confirm('Are you sure you want to deactivate this developer?')) {
            return;
        }

        try {
            const response = await fetch(`../api/admin/developer/${developerId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok || result.success === false) {
                alert(result.message || 'Deactivate failed');
                return;
            }

            alert('Developer deactivated successfully');

        } catch (error) {
            console.error(error);
            alert('Network error');
        }
    }
    else if (e.target.id === 'btnDeveloperActivate') {
        e.preventDefault();

        const developerId = e.target.dataset.developerId;
        if (!developerId) {
            alert('Developer ID not found');
            return;
        }

        if (!confirm('Are you sure you want to activate this developer?')) {
            return;
        }

        try {
            const response = await fetch(`../api/admin/developer/${developerId}/activate`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok || result.success === false) {
                alert(result.message || 'Activation failed');
                return;
            }

            alert('Developer activated successfully');

        } catch (error) {
            console.error(error);
            alert('Network error');
        }
    }
});
 
 document.querySelectorAll('#gameDelete').forEach(btn => {
            btn.addEventListener('click', function() {
                const gameId = this.getAttribute('data-game-id');
                const gameName = this.getAttribute('data-game-name');
                if (!gameId) {
                    alert('Unable to delete: Game ID not found');
                    return;
                }

                if (confirm(`Are you sure you want to delete "${gameName}"? This action cannot be undone.`)) {
                    const deleteBtn = $(this);
                    const originalBtnHtml = deleteBtn.html();

                    deleteBtn.prop('disabled', true);
                    deleteBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Deleting...');

                    $.ajax({
                        url: '/api/games/' + gameId,
                        type: 'DELETE',
                        xhrFields: {
                            withCredentials: true
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Game deleted successfully!');
                                deleteBtn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    window.location.reload();
                                });
                            } else {
                                alert('Failed to delete game: ' + (response.message || 'Unknown error'));
                                deleteBtn.prop('disabled', false);
                                deleteBtn.html(originalBtnHtml);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting game:', error);
                            console.error('XHR Response:', xhr);
                            let errorMsg = 'Failed to delete game. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                errorMsg = 'Server error: ' + xhr.responseText;
                            }

                            alert(errorMsg);
                            deleteBtn.prop('disabled', false);
                            deleteBtn.html(originalBtnHtml);
                        }
                    });
                }
            });
        });
         document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const gameId = this.getAttribute('data-game-id');
                const gameName = this.getAttribute('data-game-name');
                const gameGenre = this.getAttribute('data-game-genre');
                const gameDescription = this.getAttribute('data-game-description');
                const gamePrice = this.getAttribute('data-game-price');

                // Populate modal fields
                $('#editGameId').val(gameId);
                $('#editGameName').val(gameName);
                $('#editGameGenre').val(gameGenre);
                $('#editGameDescription').val(gameDescription);
                $('#editGamePrice').val(gamePrice);

                // Show modal
                const editModal = new bootstrap.Modal(document.getElementById('editGameModal'));
                editModal.show();
            });
           
        });
         $('#saveGameChanges').on('click', function() {
            const gameId = $('#editGameId').val();
            const gameData = {
                name: $('#editGameName').val(),
                genre: $('#editGameGenre').val(),
                description: $('#editGameDescription').val(),
                price: parseFloat($('#editGamePrice').val())
            };

            // Validate
            if (!gameData.name || !gameData.genre || !gameData.description || !gameData.price) {
                alert('Please fill in all required fields.');
                return;
            }

            const saveBtn = $(this);
            const originalBtnHtml = saveBtn.html();
            saveBtn.prop('disabled', true);
            saveBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

            // Make PUT request
            $.ajax({
                url: '/api/admin/games/' + gameId,
                type: 'PATCH',
                contentType: 'application/json',
                data: JSON.stringify(gameData),
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    if (response.success) {
                        alert('Game updated successfully!');
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('editGameModal')).hide();
                        // Reload page to show updated data
                        window.location.reload();
                    } else {
                        alert('Failed to update game: ' + (response.message || 'Unknown error'));
                        saveBtn.prop('disabled', false);
                        saveBtn.html(originalBtnHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating game:', error);
                    let errorMsg = 'Failed to update game. Please try again.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    alert(errorMsg);
                    saveBtn.prop('disabled', false);
                    saveBtn.html(originalBtnHtml);
                }
            });
            });

    </script>
</body>

</html>