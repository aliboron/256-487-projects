<?php
// Sample games data - will be replaced with API call later
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . '/../api/db.php';

$stmt = $db->prepare("select * from games");
$stmt->execute();
$games = $db->query("SELECT g.*, u.username, gm.file_path FROM games g JOIN users u JOIN game_media gm ON g.id = gm.game_id WHERE u.id=g.developer_id")->fetchAll(PDO::FETCH_ASSOC);

?>
<?php $activePage = 'store'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTIS256 - Game Store - Browse Games</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/search_input.css">
    <link rel="stylesheet" href="css/game_card.css">
    <link rel="stylesheet" href="css/index.css">
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
</head>

<body>
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Discover Amazing Games</h1>
            <p>Browse through our collection of the best games</p>
        </div>
    </section>



    <!-- Main Content -->
    <div class="container">
        <section class="featured-games">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Featured Games</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" data-bs-target="#featured-carousel" data-bs-slide="prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-target="#featured-carousel" data-bs-slide="next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <?php
                $featuredGames = $games;
                shuffle($featuredGames);
                $featuredGames = array_slice($featuredGames, 0, 4);
                ?>

                <?php if (!empty($featuredGames)): ?>
                    <div id="featured-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-inner">
                            <?php foreach ($featuredGames as $i => $game): ?>
                                <?php
                                $title = $game['title'] ?? $game['name'] ?? 'Untitled';
                                $banner = $game['file_path'] ?? 'img/placeholder-banner.jpg';
                                ?>
                                <div class="carousel-item <?php echo $i === 0 ? 'active' : '' ?>">
                                    <div class="featured-banner">
                                        <img src="<?php echo htmlspecialchars($banner); ?>" class="w-100" alt="<?php echo htmlspecialchars($title); ?>">
                                        <div class="featured-banner-content">
                                            <h3 class="featured-banner-title mb-1"><?php echo htmlspecialchars($title); ?></h3>
                                            <?php if (!empty($game['genre'])): ?>
                                                <span class="badge bg-dark-subtle text-dark"><?php echo htmlspecialchars($game['genre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No games to feature.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <?php include 'components/search_input.php'; ?>
                </div>
                <div class="col-md-3">
                    <label class="mb-2" for="genreFilter"><i class="fa-solid fa-filter"></i> Filter by Genre:</label>
                    <select id="genreFilter" class="form-select">
                        <option value="all">All Genres</option>
                        <?php
                        // Extract unique genres

                        $genres = array_unique(array_column($games, 'genre'));
                        sort($genres);
                        foreach ($genres as $genre):
                        ?>
                            <option value="<?php echo htmlspecialchars($genre); ?>">
                                <?php echo htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <span id="gameCount" class="text-muted">
                        Showing <strong><?php echo count($games); ?></strong> games
                    </span>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 games-grid">
            <?php foreach ($games as $game): ?>
                <?php
                //var_dump($game);
                $type = 'store';
                include 'components/game_card.php';
                ?>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter and search functionality
        const searchInput = $("#searchInput");
        const genreFilter = $("#genreFilter");
        const gameItems = $('.game-item');
        const gameCount = $('#gameCount');

        function filterGames() {
            const searchTerm = searchInput.val().toLowerCase();
            const selectedGenre = genreFilter.val();
            let visibleCount = 0;

            gameItems.each(function() {
                const gameName = $(this).find('.game-title').text().toLowerCase();
                const gameGenre = $(this).attr('data-genre');
                const matchesSearch = gameName.includes(searchTerm);
                const matchesGenre = selectedGenre === 'all' || gameGenre === selectedGenre;

                if (matchesSearch && matchesGenre) {
                    $(this).removeClass('hidden');
                    visibleCount++;
                } else {
                    $(this).addClass('hidden');
                }
            });

            // Update game count
            gameCount.html(
                `Showing <strong>${visibleCount}</strong> game${visibleCount !== 1 ? 's' : ''}`
            );
        }

        searchInput.on('input', filterGames);
        genreFilter.on('change', filterGames);
    </script>
</body>

</html>