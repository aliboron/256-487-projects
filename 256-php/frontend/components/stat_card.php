<?php

/**
 * Stat Card Component
 * 
 * @param string $icon - Font Awesome icon class (e.g., 'fa-gamepad')
 * @param string|int $number - The stat number to display
 * @param string $label - The stat label text
 */

$iconClass = $icon ?? 'fa-chart-simple';
$statNumber = $number ?? '0';
$statLabel = $label ?? 'Stat';
?>

<div class="col-md-4">
    <div class="stat-card">
        <div class="stat-icon-circle">
            <i class="fa-solid <?php echo htmlspecialchars($iconClass); ?>"></i>
        </div>
        <div class="stat-number"><?php echo htmlspecialchars($statNumber); ?></div>
        <div class="stat-label"><?php echo htmlspecialchars($statLabel); ?></div>
    </div>
</div>