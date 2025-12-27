<?php

/**
 * Game Card Component
 * 
 * @param array $game - Game data array
 * @param string $type - 'store' or 'library'
 */

$cardType = $type ?? 'store';
$gameData = $game ?? [];
?>

<div class="col game-item"
    data-genre="<?php echo htmlspecialchars($gameData['genre']); ?>"
    <?php if ($cardType === 'library'): ?>
    data-name="<?php echo htmlspecialchars($gameData['name']); ?>"
    data-date="<?php echo htmlspecialchars($gameData['purchase_date']); ?>"
    data-playtime="<?php echo (int)filter_var($gameData['playtime'], FILTER_SANITIZE_NUMBER_INT); ?>"
    <?php endif; ?>>
    <div class="game-card">
        <img src="<?php echo htmlspecialchars($gameData['logo_path']); ?>"
            alt="<?php echo htmlspecialchars($gameData['name']); ?>"
            class="game-card-img"
            onerror="this.src='https://via.placeholder.com/264x352/2a2a2a/ffffff?text=No+Image'">
        <div class="game-card-body">
            <h5 class="game-title"><?php echo htmlspecialchars($gameData['name']); ?></h5>
            <span class="game-genre"><?php echo htmlspecialchars($gameData['genre']); ?></span>
            <p class="game-description"><?php echo htmlspecialchars($gameData['description']); ?></p>

            <?php if ($cardType === 'library'): ?>
                <!-- Library view: Show playtime and purchase date -->
                <div class="game-stats">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fa-solid fa-clock"></i></span>
                        <span class="stat-text"><?php echo htmlspecialchars($gameData['playtime']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fa-solid fa-calendar"></i></span>
                        <span class="stat-text"><?php echo date('M d, Y', strtotime($gameData['purchase_date'])); ?></span>
                    </div>
                </div>
                <div class="game-footer">
                    <a href="steam://rungameid/<?php echo $gameData['id']; ?>" class="btn btn-play w-100"><i class="fa-solid fa-play"></i> Play Now</a>
                </div>
            <?php else: ?>
                <!-- Store view: Show price and buy button -->
                <div class="game-footer">
                    <div class="game-price"><i class="fa-solid fa-dollar-sign"></i><?php echo number_format($gameData['price'], 2); ?></div>
                    <a href="checkout.php?game_id=<?php echo $gameData['id']; ?>" class="btn btn-add-cart"><i class="fa-solid fa-cart-shopping"></i> Buy Game</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>