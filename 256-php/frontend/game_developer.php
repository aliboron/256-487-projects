<?php
// Sample data - will be replaced with API calls later
$isDeveloperApproved = true; // Set to false to test approval warning
$developerName = 'TechStudio';

$myGames = [
    [
        'id' => 1,
        'name' => 'Cyber Quest 2077',
        'logo_path' => 'https://via.placeholder.com/60x80/2a2a2a/ffffff?text=CQ',
        'genre' => 'RPG',
        'price' => 49.99,
        'status' => 'approved',
        'sales' => 150,
        'created_date' => '2025-11-15'
    ],
    [
        'id' => 2,
        'name' => 'Space Raiders',
        'logo_path' => 'https://via.placeholder.com/60x80/2a2a2a/ffffff?text=SR',
        'genre' => 'Shooter',
        'price' => 29.99,
        'status' => 'pending',
        'sales' => 0,
        'created_date' => '2025-12-20'
    ],
    [
        'id' => 3,
        'name' => 'Fantasy Kingdom',
        'logo_path' => 'https://via.placeholder.com/60x80/2a2a2a/ffffff?text=FK',
        'genre' => 'Strategy',
        'price' => 39.99,
        'status' => 'rejected',
        'sales' => 0,
        'created_date' => '2025-12-10'
    ]
];

$customers = [
    [
        'id' => 1,
        'username' => 'gamer123',
        'email' => 'gamer123@email.com',
        'game_purchased' => 'Cyber Quest 2077',
        'purchase_date' => '2025-12-15',
        'price_paid' => 49.99
    ],
    [
        'id' => 2,
        'username' => 'player456',
        'email' => 'player456@email.com',
        'game_purchased' => 'Cyber Quest 2077',
        'purchase_date' => '2025-12-18',
        'price_paid' => 49.99
    ]
];

$activeSection = $_GET['section'] ?? 'dashboard';
$statusFilter = $_GET['status'] ?? 'all';

// Filter games by status
$filteredGames = $myGames;
if ($statusFilter !== 'all') {
    $filteredGames = array_filter($myGames, function($game) use ($statusFilter) {
        return $game['status'] === $statusFilter;
    });
}

// Calculate stats
$totalSales = array_sum(array_column($myGames, 'sales'));
$totalRevenue = 0;
foreach ($myGames as $game) {
    $totalRevenue += $game['sales'] * $game['price'];
}
$approvedGamesCount = count(array_filter($myGames, function($game) { return $game['status'] === 'approved'; }));
$pendingGamesCount = count(array_filter($myGames, function($game) { return $game['status'] === 'pending'; }));
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
                    <a href="?section=my-games" class="sidebar-menu-link <?php echo $activeSection === 'my-games' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
                        <i class="fa-solid fa-gamepad"></i>
                        <span>My Games</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=add-game" class="sidebar-menu-link <?php echo $activeSection === 'add-game' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Add New Game</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?section=gamers" class="sidebar-menu-link <?php echo $activeSection === 'gamers' ? 'active' : ''; ?> <?php echo !$isDeveloperApproved ? 'disabled' : ''; ?>">
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

        <!-- Main Content -->
        <main class="developer-content">
            <!-- Approval Warning -->
            <?php if (!$isDeveloperApproved): ?>
                <div class="approval-warning">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <div>
                        <strong>Account Pending Approval</strong>
                        <p style="margin: 0;">Your developer account is pending admin approval. You will be able to manage games once approved.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dashboard Section -->
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

            <!-- My Games Section -->
            <?php if ($activeSection === 'my-games' && $isDeveloperApproved): ?>
                <div class="developer-header">
                    <h1>My Games</h1>
                    <p>Manage your published games and track their status.</p>
                </div>

                <!-- Filter Bar -->
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
                                        <td><img src="<?php echo htmlspecialchars($game['logo_path']); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>" class="game-thumbnail"></td>
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

            <!-- Add New Game Section -->
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

            <!-- Gamers Section -->
            <?php if ($activeSection === 'gamers' && $isDeveloperApproved): ?>
                <div class="developer-header">
                    <h1>Gamers</h1>
                    <p>View gamers who have purchased your games.</p>
                </div>

                <div class="section-card">
                    <?php if (empty($customers)): ?>
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
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td style="color: #ffffff;"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($customer['username']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td style="color: #4CAF50;"><?php echo htmlspecialchars($customer['game_purchased']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($customer['purchase_date'])); ?></td>
                                        <td style="color: #4CAF50;">$<?php echo number_format($customer['price_paid'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload preview
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
            // Remove image from preview and input
            const dt = new DataTransfer();
            const files = Array.from(imageInput.files);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
            imageInput.dispatchEvent(new Event('change'));
        }

        // Form submission
        const addGameForm = document.getElementById('addGameForm');
        if (addGameForm) {
            addGameForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Submit this game for admin approval?')) {
                    alert('Game submitted successfully! It will be reviewed by an admin.');
                    // Handle form submission
                    this.reset();
                    imagePreview.innerHTML = '';
                }
            });
        }

        // Delete confirmation
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this game? This action cannot be undone.')) {
                    console.log('Game deleted');
                    this.closest('tr').remove();
                }
            });
        });
    </script>
</body>

</html>
