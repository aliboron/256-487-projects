<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_data'])) {
    header('Location: login.php?redirect=game_developer');
    exit;
}

if ($_SESSION['user_data']['type'] !== 'game_developer') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../api/db.php';

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$isDeveloperApproved = $userData['is_verified'] ?? false;
$developerName = $userData['username'] ?? 'Developer';

$activeSection = $_GET['section'] ?? 'dashboard';
$statusFilter = $_GET['status'] ?? 'all';

$gamesQuery = "SELECT g.*, gm.file_path,
    (SELECT COUNT(*) FROM checkouts WHERE game_id = g.id) as sales
    FROM games g
    JOIN game_media gm ON g.id = gm.game_id
    WHERE g.developer_id = ?";

$params = [$_SESSION['user_id']];

if ($statusFilter === 'approved') {
    $gamesQuery .= " AND g.is_approved = 1";
} elseif ($statusFilter === 'pending') {
    $gamesQuery .= " AND g.is_approved = 0";
} elseif ($statusFilter === 'rejected') {
    $gamesQuery .= " AND g.is_approved = -1";
}

$gamesQuery .= " ORDER BY g.created_at DESC";

$stmt = $db->prepare($gamesQuery);
$stmt->execute($params);
$myGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

$myGames = array_map(function ($game) {
    if ($game['is_approved'] == 1) {
        $game['status'] = 'approved';
    } elseif ($game['is_approved'] == 0) {
        $game['status'] = 'pending';
    } else {
        $game['status'] = 'rejected';
    }
    $game['created_date'] = $game['created_at'];
    $game['sales'] = (int)$game['sales'];
    return $game;
}, $myGames);

$gamersQuery = "SELECT 
    u.id,
    u.username,
    u.email,
    g.name as game_purchased,
    c.date as purchase_date,
    c.payment_total as price_paid
    FROM checkouts c
    INNER JOIN users u ON c.user_id = u.id
    INNER JOIN games g ON c.game_id = g.id
    WHERE g.developer_id = ?
    ORDER BY c.date DESC";

$stmt = $db->prepare($gamersQuery);
$stmt->execute([$_SESSION['user_id']]);
$gamers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filteredGames = $myGames;
if ($statusFilter !== 'all') {
    $filteredGames = array_filter($myGames, function ($game) use ($statusFilter) {
        return $game['status'] === $statusFilter;
    });
}

$totalSales = array_sum(array_column($myGames, 'sales'));
$totalRevenue = 0;
foreach ($myGames as $game) {
    $totalRevenue += $game['sales'] * $game['price'];
}
$approvedGamesCount = count(array_filter($myGames, function ($game) {
    return $game['status'] === 'approved';
}));
$pendingGamesCount = count(array_filter($myGames, function ($game) {
    return $game['status'] === 'pending';
}));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTIS256 - Developer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/developer_sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/developer.css">
</head>

<body>
    <div class="developer-wrapper">
        <!-- Sidebar -->
        <aside class="developer-sidebar">
            <div class="sidebar-brand">
                <h4><i class="fa-solid fa-code"></i> Developer</h4>
                <p><?php echo htmlspecialchars($developerName); ?></p>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?section=dashboard" class="sidebar-menu-link <?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo !$isDeveloperApproved ? 'javascript:void(0)' : '?section=my-games'; ?>" class="sidebar-menu-link <?php echo $activeSection === 'my-games' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
                        <i class="fa-solid fa-gamepad"></i>
                        <span>My Games</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo !$isDeveloperApproved ? 'javascript:void(0)' : '?section=add-game'; ?>" class="sidebar-menu-link <?php echo $activeSection === 'add-game' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Add New Game</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?php echo !$isDeveloperApproved ? 'javascript:void(0)' : '?section=gamers'; ?>" class="sidebar-menu-link <?php echo $activeSection === 'gamers' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Gamers</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="index.php" class="btn btn-exit">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Store</span>
                </a>
            </div>
        </aside>

        <main class="developer-content">
            <?php if (!$isDeveloperApproved): ?>
                <div class="approval-warning">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <div>
                        <strong>Account Pending Approval</strong>
                        <p style="margin: 0;">Your developer account is pending admin approval. You will be able to manage games once approved.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($activeSection === 'dashboard'): ?>
                <div class="developer-header">
                    <h1>Developer Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($developerName); ?>! Here's your game portfolio overview.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo count($myGames); ?></div>
                        <div class="stat-box-label">Total Games</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo $approvedGamesCount; ?></div>
                        <div class="stat-box-label">Approved Games</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number"><?php echo $totalSales; ?></div>
                        <div class="stat-box-label">Total Sales</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-number">$<?php echo number_format($totalRevenue, 2); ?></div>
                        <div class="stat-box-label">Total Revenue</div>
                    </div>
                </div>

                <?php if ($pendingGamesCount > 0): ?>
                    <div class="section-card">
                        <h2 class="section-title">
                            <i class="fa-solid fa-bell"></i>
                            Notifications
                        </h2>
                        <p style="color: #a0a0a0;">You have <?php echo $pendingGamesCount; ?> game(s) awaiting admin approval.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($activeSection === 'my-games' && $isDeveloperApproved): ?>
                <div class="developer-header">
                    <h1>My Games</h1>
                    <p>Manage your published games and track their status.</p>
                </div>

                <div class="filter-bar">
                    <label for="statusFilter"><i class="fa-solid fa-filter"></i> Filter by Status:</label>
                    <select id="statusFilter" onchange="window.location.href='?section=my-games&status=' + this.value">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Games</option>
                        <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    <span style="color: #a0a0a0; margin-left: auto;">
                        Showing <strong style="color: #ffffff;"><?php echo count($filteredGames); ?></strong> game(s)
                    </span>
                </div>

                <div class="section-card">
                    <?php if (empty($filteredGames)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-gamepad"></i>
                            <h3>No Games Found</h3>
                            <p>No games match your filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Game</th>
                                    <th>Name</th>
                                    <th>Genre</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Sales</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filteredGames as $game): ?>
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
                                        <td style="color: #ffffff;"><?php echo htmlspecialchars($game['name']); ?></td>
                                        <td><?php echo htmlspecialchars($game['genre']); ?></td>
                                        <td style="color: #4CAF50;">$<?php echo number_format($game['price'], 2); ?></td>
                                        <td>
                                            <?php if ($game['status'] === 'approved'): ?>
                                                <span class="badge badge-success">Approved</span>
                                            <?php elseif ($game['status'] === 'pending'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $game['sales']; ?> units</td>
                                        <td><?php echo date('M d, Y', strtotime($game['created_date'])); ?></td>
                                        <td>
                                            <button class="btn-action btn-edit"
                                                data-game-id="<?php echo $game['id']; ?>"
                                                data-game-name="<?php echo htmlspecialchars($game['name']); ?>"
                                                data-game-genre="<?php echo htmlspecialchars($game['genre']); ?>"
                                                data-game-description="<?php echo htmlspecialchars($game['description']); ?>"
                                                data-game-price="<?php echo $game['price']; ?>">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" data-game-id="<?php echo $game['id']; ?>" data-game-name="<?php echo htmlspecialchars($game['name']); ?>"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($activeSection === 'add-game' && $isDeveloperApproved): ?>
                <div class="developer-header">
                    <h1>Add New Game</h1>
                    <p>Submit a new game for admin approval.</p>
                </div>

                <div class="section-card">
                    <form class="game-form" id="addGameForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gameName"><i class="fa-solid fa-gamepad"></i> Game Name *</label>
                                    <input type="text" id="gameName" name="gameName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gameGenre"><i class="fa-solid fa-tag"></i> Genre *</label>
                                    <select id="gameGenre" name="gameGenre" required>
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
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gameDescription"><i class="fa-solid fa-align-left"></i> Description *</label>
                            <textarea id="gameDescription" name="gameDescription" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gamePrice"><i class="fa-solid fa-dollar-sign"></i> Price (USD) *</label>
                                    <input type="number" id="gamePrice" name="gamePrice" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-image"></i> Game Images/Posters *</label>
                            <div class="file-upload-area" onclick="document.getElementById('gameImages').click()">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Click to upload images (PNG, JPG, JPEG)</p>
                                <p style="font-size: 0.85rem;">You can upload multiple images</p>
                                <input type="file" id="gameImages" name="gameImages[]" accept="image/*" multiple>
                            </div>
                            <div id="imagePreview" class="image-preview"></div>
                        </div>

                        <div style="margin-top: 2rem;">
                            <button type="submit" class="btn-primary">
                                <i class="fa-solid fa-paper-plane"></i> Submit for Approval
                            </button>
                            <button type="reset" class="btn-secondary">
                                <i class="fa-solid fa-rotate-left"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($activeSection === 'gamers' && $isDeveloperApproved): ?>
                <div class="developer-header">
                    <h1>Gamers</h1>
                    <p>View gamers who have purchased your games.</p>
                </div>

                <div class="section-card">
                    <?php if (empty($gamers)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-users"></i>
                            <h3>No Gamers Yet</h3>
                            <p>No one has purchased your games yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="customer-table">
                            <thead>
                                <tr>
                                    <th>Gamer</th>
                                    <th>Email</th>
                                    <th>Game Purchased</th>
                                    <th>Purchase Date</th>
                                    <th>Price Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gamers as $gamer): ?>
                                    <tr>
                                        <td style="color: #ffffff;"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($gamer['username']); ?></td>
                                        <td><?php echo htmlspecialchars($gamer['email']); ?></td>
                                        <td style="color: #4CAF50;"><?php echo htmlspecialchars($gamer['game_purchased']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($gamer['purchase_date'])); ?></td>
                                        <td style="color: #4CAF50;">$<?php echo number_format($gamer['price_paid'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

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

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="editGameName" style="display: block; margin-bottom: 0.5rem;">
                                <i class="fa-solid fa-gamepad"></i> Game Name *
                            </label>
                            <input type="text" id="editGameName" class="form-control" required
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;">
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="editGameGenre" style="display: block; margin-bottom: 0.5rem;">
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

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="editGameDescription" style="display: block; margin-bottom: 0.5rem;">
                                <i class="fa-solid fa-align-left"></i> Description *
                            </label>
                            <textarea id="editGameDescription" class="form-control" required rows="4"
                                style="background: #2d2d2d; border: 1px solid #404040; color: #ffffff; padding: 0.5rem;"></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="editGamePrice" style="display: block; margin-bottom: 0.5rem;">
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

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function restoreFormData() {
            const formData = sessionStorage.getItem('gameFormData');
            if (formData) {
                const data = JSON.parse(formData);
                $('#gameName').val(data.name || '');
                $('#gameGenre').val(data.genre || '');
                $('#gameDescription').val(data.description || '');
                $('#gamePrice').val(data.price || '');
            }
        }

        function saveFormData() {
            const formData = {
                name: $('#gameName').val(),
                genre: $('#gameGenre').val(),
                description: $('#gameDescription').val(),
                price: $('#gamePrice').val(),
                timestamp: new Date().toISOString()
            };
            sessionStorage.setItem('gameFormData', JSON.stringify(formData));
        }

        function clearFormData() {
            sessionStorage.removeItem('gameFormData');
        }

        $(document).ready(function() {
            restoreFormData();

            let saveTimeout;
            $('#gameName, #gameGenre, #gameDescription, #gamePrice').on('input change', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    saveFormData();
                }, 500);
            });

            $('#addGameForm').on('reset', function() {
                clearFormData();
                imagePreview.innerHTML = '';
            });
        });
        const imageInput = document.getElementById('gameImages');
        const imagePreview = document.getElementById('imagePreview');

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                imagePreview.innerHTML = '';
                const files = Array.from(e.target.files);

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            previewItem.innerHTML = `
                                <img src="${event.target.result}" alt="Preview">
                                <button type="button" class="remove-btn" onclick="removeImage(${index})">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            `;
                            imagePreview.appendChild(previewItem);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }

        function removeImage(index) {
            const dt = new DataTransfer();
            const files = Array.from(imageInput.files);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
            imageInput.dispatchEvent(new Event('change'));
        }

        const addGameForm = document.getElementById('addGameForm');
        if (addGameForm) {
            addGameForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!confirm('Submit this game for admin approval?')) {
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');

                const gameData = {
                    name: $('#gameName').val(),
                    description: $('#gameDescription').val(),
                    price: parseFloat($('#gamePrice').val()),
                    genre: $('#gameGenre').val(),
                    developer_id: <?php echo $_SESSION['user_id']; ?>,
                    is_approved: 0 // Pending
                };

                $.ajax({
                    url: '/api/developers/<?php echo $_SESSION['user_id']; ?>/games',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(gameData),
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(response) {
                        console.log('Game created:', response);

                        if (response.success && response.data && response.data.id) {
                            const gameId = response.data.id;

                            const files = imageInput ? imageInput.files : [];
                            if (files.length > 0) {
                                uploadGameAssets(gameId, files, submitBtn, originalBtnText);
                            } else {
                                alert('Game submitted successfully! It will be reviewed by an admin.');
                                submitBtn.prop('disabled', false);
                                submitBtn.html(originalBtnText);
                                window.location.href = '?section=my-games';
                            }
                        } else {
                            alert('Game created but response data is missing.');
                            submitBtn.prop('disabled', false);
                            submitBtn.html(originalBtnText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error creating game:', error);
                        console.error('XHR Response:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);
                        let errorMsg = 'Failed to create game. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMsg = 'Server error: ' + xhr.responseText;
                        }

                        alert(errorMsg);
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalBtnText);
                    }
                });
            });
        }

        function uploadGameAssets(gameId, files, submitBtn, originalBtnText) {
            let uploadedCount = 0;
            let failedCount = 0;
            const totalFiles = files.length;

            submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Uploading images... (0/' + totalFiles + ')');

            Array.from(files).forEach((file, index) => {
                const formData = new FormData();
                formData.append('file', file);

                fetch('/api/developers/<?php echo $_SESSION['user_id']; ?>/games/' + gameId + '/assets', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            uploadedCount++;
                        } else {
                            failedCount++;
                            console.error('Failed to upload:', file.name, data.message);
                        }

                        submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Uploading images... (' + (uploadedCount + failedCount) + '/' + totalFiles + ')');

                        if (uploadedCount + failedCount === totalFiles) {
                            finishGameCreation(uploadedCount, failedCount, totalFiles, submitBtn, originalBtnText);
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading asset:', error);
                        failedCount++;

                        submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Uploading images... (' + (uploadedCount + failedCount) + '/' + totalFiles + ')');

                        if (uploadedCount + failedCount === totalFiles) {
                            finishGameCreation(uploadedCount, failedCount, totalFiles, submitBtn, originalBtnText);
                        }
                    });
            });
        }

        function finishGameCreation(uploadedCount, failedCount, totalFiles, submitBtn, originalBtnText) {
            let message = 'Game submitted successfully!';

            if (failedCount > 0) {
                message += ` However, ${failedCount} out of ${totalFiles} images failed to upload.`;
            } else {
                message += ' All images uploaded successfully.';
            }

            message += ' The game will be reviewed by an admin.';

            alert(message);

            submitBtn.prop('disabled', false);
            submitBtn.html(originalBtnText);

            window.location.href = '?section=my-games';
        }

        document.querySelectorAll('.btn-delete').forEach(btn => {
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

                $('#editGameId').val(gameId);
                $('#editGameName').val(gameName);
                $('#editGameGenre').val(gameGenre);
                $('#editGameDescription').val(gameDescription);
                $('#editGamePrice').val(gamePrice);

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

            if (!gameData.name || !gameData.genre || !gameData.description || !gameData.price) {
                alert('Please fill in all required fields.');
                return;
            }

            const saveBtn = $(this);
            const originalBtnHtml = saveBtn.html();
            saveBtn.prop('disabled', true);
            saveBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: '/api/developers/<?php echo $_SESSION['user_id']; ?>/games/' + gameId,
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(gameData),
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    if (response.success) {
                        alert('Game updated successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('editGameModal')).hide();
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