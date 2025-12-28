<?php
// Sample games data - will be replaced with API call later
require_once __DIR__ . "/vendor/autoload.php";
$httpClient = new \GuzzleHttp\Client(["verify" => false]);
$response = $httpClient->get("http://ctis256.aliboron.tr/api/games");
$responseContent = json_decode($response->getBody()->getContents());
$games = $responseContent->data;
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
                        $genres = [];
                        foreach ($games as $key => $game) {
                            array_push($genres, $game->genre);
                        }
                        $genres = array_unique($genres);
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