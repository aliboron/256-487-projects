<?php
// Sample games data - will be replaced with API call later
$sampleGames = [
    [
        "id" => 30,
        "name" => "Super Smash Bros. Melee",
        "description" => "Super Smash Bros. Melee includes all playable characters from the first game and adds characters from franchises such as Fire Emblem. Its major focus is the multiplayer mode.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co21yv.jpg",
        "genre" => "Fighting"
    ],
    [
        "id" => 38,
        "name" => "Mass Effect 2",
        "description" => "It is time to bring together your greatest allies and recruit the galaxy's fighting elite to continue the resistance against the invading Reapers.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co20ac.jpg",
        "genre" => "Shooter"
    ],
    [
        "id" => 37,
        "name" => "Red Dead Redemption 2",
        "description" => "Red Dead Redemption 2 is the epic tale of outlaw Arthur Morgan and the infamous Van der Linde gang, on the run across America at the dawn of the modern age.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1q1f.jpg",
        "genre" => "Shooter"
    ],
    [
        "id" => 36,
        "name" => "Metroid Prime",
        "description" => "A 3D exploration-focused metroidvania. Samus Aran boards a Space Pirate frigate, then chases her escaping archrival Ridley into the intricately structured Tallon IV.",
        "price" => 39.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co3w4w.jpg",
        "genre" => "Shooter"
    ],
    [
        "id" => 35,
        "name" => "Super Mario World 2: Yoshi's Island",
        "description" => "The game casts players as Yoshi as he escorts Baby Mario through 48 levels in order to reunite him with his brother Luigi. It features a hand-drawn aesthetic.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co2kn9.jpg",
        "genre" => "Platform"
    ],
    [
        "id" => 34,
        "name" => "Super Mario Galaxy",
        "description" => "A 3D platformer where Mario jumps across planets and galaxies with varying items, enemies, geographies and gravity mechanics in order to reach his enemy Bowser.",
        "price" => 29.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co21ro.jpg",
        "genre" => "Platform"
    ],
    [
        "id" => 33,
        "name" => "Persona 5 Royal",
        "description" => "An enhanced version of Persona 5 with some new characters and a third semester added to the game. Released Internationally in 2020.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/coateg.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 32,
        "name" => "God of War",
        "description" => "This game focuses on Norse mythology and follows an older and more seasoned Kratos and his son Atreus in the years since the third game.",
        "price" => 49.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1tmu.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 31,
        "name" => "The Legend of Zelda: Tears of the Kingdom",
        "description" => "The Legend of Zelda: Tears of the Kingdom is the sequel to Breath of the Wild. The setting for Link's adventure has been expanded to include the skies above the vast lands of Hyrule.",
        "price" => 69.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co5vmg.jpg",
        "genre" => "Adventure"
    ],
    [
        "id" => 21,
        "name" => "The Witcher 3: Wild Hunt - Game of the Year Editio",
        "description" => "The Witcher 3: Wild Hunt – Game of the Year Edition is a complete version of the game released in August 2016. It includes the base game along with all post-launch content, including the two major expansions.",
        "price" => 49.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1wz4.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 29,
        "name" => "Final Fantasy III",
        "description" => "Final Fantasy III is the sixth main installment in the Final Fantasy series. It was the final title to feature two-dimensional graphics and the first story that did not revolve around crystals.",
        "price" => 14.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/coaq5k.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 28,
        "name" => "The Legend of Zelda: Breath of the Wild",
        "description" => "The Legend of Zelda: Breath of the Wild is the first 3D open-world game in the Zelda series. Link can travel anywhere and be equipped with weapons and armor found throughout the world.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co3p2d.jpg",
        "genre" => "Adventure"
    ],
    [
        "id" => 27,
        "name" => "Baldur's Gate III",
        "description" => "An ancient evil has returned to Baldur's Gate, intent on devouring it from the inside out. The fate of Faerun lies in your hands. Alone, you may resist. But together, you can overcome.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co670h.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 26,
        "name" => "The Last of Us Remastered",
        "description" => "The Last of Us Remastered is an updated release of the PS3 game. It runs at 1080p resolution with higher resolution character models, improved shadows and lighting.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co5zks.jpg",
        "genre" => "Shooter"
    ],
    [
        "id" => 25,
        "name" => "Elden Ring",
        "description" => "Elden Ring is an action RPG developed by FromSoftware. Players assume the role of a customisable character known as the Tarnished, who must explore the Lands Between and seek to become the Elden Lord.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co4jni.jpg",
        "genre" => "Role-playing (RPG)"
    ],
    [
        "id" => 24,
        "name" => "Super Mario World",
        "description" => "A 2D platformer and first entry on the SNES in the Super Mario franchise, Super Mario World follows Mario as he attempts to defeat Bowser's underlings and rescue Princess Peach.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co8lo8.jpg",
        "genre" => "Platform"
    ],
    [
        "id" => 23,
        "name" => "Super Metroid",
        "description" => "The Space Pirates, merciless agents of the evil Mother Brain, have stolen the last Metroid from a research station, and once again Mother Brain threatens the safety of the galaxy!",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co5osy.jpg",
        "genre" => "Shooter"
    ],
    [
        "id" => 22,
        "name" => "The Legend of Zelda: A Link to the Past",
        "description" => "Venture back to Hyrule and an age of magic and heroes. The predecessors of Link and Zelda face monsters on the march when a menacing magician takes over the kingdom.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co3vzn.jpg",
        "genre" => "Adventure"
    ]
];
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
                        $genres = array_unique(array_column($sampleGames, 'genre'));
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
                        Showing <strong><?php echo count($sampleGames); ?></strong> games
                    </span>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 games-grid">
            <?php foreach ($sampleGames as $game): ?>
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
        const searchInput = document.getElementById('searchInput');
        const genreFilter = document.getElementById('genreFilter');
        const gameItems = document.querySelectorAll('.game-item');
        const gameCount = document.getElementById('gameCount');

        function filterGames() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedGenre = genreFilter.value;
            let visibleCount = 0;

            gameItems.forEach(item => {
                const gameName = item.querySelector('.game-title').textContent.toLowerCase();
                const gameGenre = item.getAttribute('data-genre');
                const matchesSearch = gameName.includes(searchTerm);
                const matchesGenre = selectedGenre === 'all' || gameGenre === selectedGenre;

                if (matchesSearch && matchesGenre) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            // Update game count
            gameCount.innerHTML =
                `Showing <strong>${visibleCount}</strong> game${visibleCount !== 1 ? 's' : ''}`;
        }

        searchInput.addEventListener('input', filterGames);
        genreFilter.addEventListener('change', filterGames);
    </script>
</body>

</html>