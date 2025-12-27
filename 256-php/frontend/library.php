<?php
// Sample library data - will be replaced with API call later
$libraryGames = [
    [
        "id" => 30,
        "name" => "Super Smash Bros. Melee",
        "description" => "Super Smash Bros. Melee includes all playable characters from the first game and adds characters from franchises such as Fire Emblem. Its major focus is the multiplayer mode.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co21yv.jpg",
        "genre" => "Fighting",
        "purchase_date" => "2025-12-15",
        "playtime" => rand(5, 150) . " hours"
    ],
    [
        "id" => 28,
        "name" => "The Legend of Zelda: Breath of the Wild",
        "description" => "The Legend of Zelda: Breath of the Wild is the first 3D open-world game in the Zelda series. Link can travel anywhere and be equipped with weapons and armor found throughout the world.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co3p2d.jpg",
        "genre" => "Adventure",
        "purchase_date" => "2025-12-10",
        "playtime" => rand(5, 150) . " hours"
    ],
    [
        "id" => 25,
        "name" => "Elden Ring",
        "description" => "Elden Ring is an action RPG developed by FromSoftware. Players assume the role of a customisable character known as the Tarnished, who must explore the Lands Between and seek to become the Elden Lord.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co4jni.jpg",
        "genre" => "Role-playing (RPG)",
        "purchase_date" => "2025-12-05",
        "playtime" => rand(5, 150) . " hours"
    ],
    [
        "id" => 37,
        "name" => "Red Dead Redemption 2",
        "description" => "Red Dead Redemption 2 is the epic tale of outlaw Arthur Morgan and the infamous Van der Linde gang, on the run across America at the dawn of the modern age.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1q1f.jpg",
        "genre" => "Shooter",
        "purchase_date" => "2025-11-28",
        "playtime" => rand(5, 150) . " hours"
    ],
    [
        "id" => 33,
        "name" => "Persona 5 Royal",
        "description" => "An enhanced version of Persona 5 with some new characters and a third semester added to the game. Released Internationally in 2020.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/coateg.jpg",
        "genre" => "Role-playing (RPG)",
        "purchase_date" => "2025-11-20",
        "playtime" => rand(5, 150) . " hours"
    ],
    [
        "id" => 32,
        "name" => "God of War",
        "description" => "This game focuses on Norse mythology and follows an older and more seasoned Kratos and his son Atreus in the years since the third game.",
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1tmu.jpg",
        "genre" => "Role-playing (RPG)",
        "purchase_date" => "2025-11-15",
        "playtime" => rand(5, 150) . " hours"
    ]
];

// Calculate total hours played
$totalHours = 0;
foreach ($libraryGames as $game) {
    $totalHours += (int)filter_var($game['playtime'], FILTER_SANITIZE_NUMBER_INT);
}
?>
<?php $activePage = 'library'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTIS256 - My Library - Game Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/search_input.css">
    <link rel="stylesheet" href="css/stat_card.css">
    <link rel="stylesheet" href="css/game_card.css">
    <link rel="stylesheet" href="css/library.css">
</head>

<body>
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>My Library</h1>
            <p>Your collection of purchased games</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="row g-3">
                <?php
                $icon = 'fa-gamepad';
                $number = count($libraryGames);
                $label = 'Games Owned';
                include 'components/stat_card.php';

                $icon = 'fa-clock';
                $number = $totalHours;
                $label = 'Total Hours Played';
                include 'components/stat_card.php';

                $icon = 'fa-layer-group';
                $number = count(array_unique(array_column($libraryGames, 'genre')));
                $label = 'Unique Genres';
                include 'components/stat_card.php';
                ?>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <?php include 'components/search_input.php'; ?>
                </div>
                <div class="col-md-3">
                    <label class="mb-2" for="genreFilter"><i class="fa-solid fa-filter"></i> Filter by Genre:</label>
                    <select id="genreFilter" class="form-select">
                        <option value="all">All Genres</option>
                        <?php
                        // Extract unique genres
                        $genres = array_unique(array_column($libraryGames, 'genre'));
                        sort($genres);
                        foreach ($genres as $genre):
                        ?>
                            <option value="<?php echo htmlspecialchars($genre); ?>">
                                <?php echo htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="mb-2" for="sortBy"><i class="fa-solid fa-arrow-down-wide-short"></i> Sort by:</label>
                    <select id="sortBy" class="form-select">
                        <option value="recent">Recently Added</option>
                        <option value="playtime">Most Played</option>
                        <option value="name">Name (A-Z)</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <span id="gameCount" class="game-count">
                        Showing <strong><?php echo count($libraryGames); ?></strong> games
                    </span>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 games-grid">
            <?php foreach ($libraryGames as $game): ?>
                <?php
                $type = 'library';
                include 'components/game_card.php';
                ?>
            <?php endforeach; ?>
        </div>

        <?php if (empty($libraryGames)): ?>
            <div class="empty-library">
                <div class="empty-icon">📚</div>
                <h3>Your library is empty</h3>
                <p>Browse the store to purchase your first game!</p>
                <a href="index.php" class="btn btn-primary">Browse Store</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter, sort, and search functionality
        const searchInput = document.getElementById('searchInput');
        const genreFilter = document.getElementById('genreFilter');
        const sortBy = document.getElementById('sortBy');
        const gameItems = document.querySelectorAll('.game-item');
        const gameCount = document.getElementById('gameCount');

        function filterAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedGenre = genreFilter.value;
            const sortOrder = sortBy.value;
            let visibleGames = [];

            // Filter games
            gameItems.forEach(item => {
                const gameName = item.getAttribute('data-name').toLowerCase();
                const gameGenre = item.getAttribute('data-genre');
                const matchesSearch = gameName.includes(searchTerm);
                const matchesGenre = selectedGenre === 'all' || gameGenre === selectedGenre;

                if (matchesSearch && matchesGenre) {
                    item.classList.remove('hidden');
                    visibleGames.push(item);
                } else {
                    item.classList.add('hidden');
                }
            });

            // Sort games
            const parent = visibleGames[0]?.parentElement;
            if (parent) {
                visibleGames.sort((a, b) => {
                    switch (sortOrder) {
                        case 'recent':
                            return new Date(b.dataset.date) - new Date(a.dataset.date);
                        case 'playtime':
                            return parseInt(b.dataset.playtime) - parseInt(a.dataset.playtime);
                        case 'name':
                            return a.dataset.name.localeCompare(b.dataset.name);
                        default:
                            return 0;
                    }
                });

                visibleGames.forEach(game => {
                    parent.appendChild(game);
                });
            }

            // Update count
            gameCount.innerHTML =
                `Showing <strong>${visibleGames.length}</strong> game${visibleGames.length !== 1 ? 's' : ''}`;
        }

        searchInput.addEventListener('input', filterAndSort);
        genreFilter.addEventListener('change', filterAndSort);
        sortBy.addEventListener('change', filterAndSort);
    </script>
</body>

</html>