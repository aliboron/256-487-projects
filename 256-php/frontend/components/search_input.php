<?php
$placeholderText = $placeholder ?? 'Search by game name...';
$searchInputId = $inputId ?? 'searchInput';
?>

<div class="search-input-wrapper">
    <label class="mb-2" for="<?php echo htmlspecialchars($searchInputId); ?>">
        <i class="fa-solid fa-magnifying-glass"></i> Search Games:
    </label>
    <input
        type="text"
        id="<?php echo htmlspecialchars($searchInputId); ?>"
        class="form-control search-input"
        placeholder="<?php echo htmlspecialchars($placeholderText); ?>">
</div>