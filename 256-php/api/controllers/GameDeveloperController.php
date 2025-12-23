<?php

require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/../r2.php';

class GameDeveloperController
{
    private UserRepository $userRepository;
    private GameRepository $gameRepository;

    public function __construct()
    {
        global $db;
        $this->userRepository = new UserRepository($db);
        $this->gameRepository = new GameRepository($db);
    }

    #[Route('/developers/{developerId}/games', 'GET')]
    public function getDeveloperGames(int $developerId, ?string $statusFilter = 'all'): ApiResponse
    {
        if (!in_array($statusFilter, ['all', 'approved', 'pending', 'rejected'])) {
            return new ApiResponse(false, null, "Invalid status filter. Allowed values are: all, approved, pending, rejected.");
        }

        $isVerified = $this->isDeveloperVerified($developerId);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so that they cannot have their games listed.");

        try {
            $games = $this->gameRepository->getByDeveloper($developerId, $statusFilter);
            return new ApiResponse(true, $games);
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error retrieving games: " . $e->getMessage());
        }
    }

    #[Route('/developers/{developerId}/games/{gameId}/sales', 'GET')]
    public function getUsersForGame(int $developerId, int $gameId): ApiResponse
    {
        $isVerified = $this->isDeveloperVerified($developerId);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so that they cannot access game users.");

        try {
            $users = $this->gameRepository->getUsersByGame($gameId);
            return new ApiResponse(true, $users);
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error retrieving users for game: " . $e->getMessage());
        }
    }

    #[Route('/developers/{developerId}/games', 'POST')]
    public function createGameForDeveloper(Game $game): ApiResponse
    {
        $isVerified = $this->isDeveloperVerified($game->developer_id);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so that they cannot create games.");

        try {
            $newGame = $this->gameRepository->createGame($game);
            return new ApiResponse(true, $newGame);
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error creating game: " . $e->getMessage());
        }
    }

    #[Route('/developers/{developerId}/games/{gameId}', 'DELETE')]
    public function deleteGameForDeveloper(int $developerId, int $gameId): ApiResponse
    {
        $isVerified = $this->isDeveloperVerified($developerId);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so that they cannot delete games.");

        try {
            $deleted = $this->gameRepository->delete($gameId);
            if ($deleted) {
                return new ApiResponse(true, null, "Game deleted successfully.");
            } else {
                return new ApiResponse(false, null, "Game not found or could not be deleted.");
            }
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error deleting game: " . $e->getMessage());
        }
    }

    #[Route('/developers/{developerId}/games/{gameId}', 'PUT')]
    public function updateGameForDeveloper(int $developerId, Game $game): ApiResponse
    {
        $isVerified = $this->isDeveloperVerified($developerId);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so that they cannot update games.");

        try {
            $updatedGame = $this->gameRepository->updateGame($game);
            if ($updatedGame) {
                return new ApiResponse(true, $updatedGame);
            } else {
                return new ApiResponse(false, null, "Game not found or could not be updated.");
            }
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error updating game: " . $e->getMessage());
        }
    }

    #[Route('/developers/{developerId}/games/{gameId}/assets', 'POST')]
    public function uploadGameAsset(int $developerId, int $gameId): ApiResponse
    {
        $isVerified = $this->isDeveloperVerified($developerId);
        if (!$isVerified) return new ApiResponse(false, null, "Developer is not verified, so they cannot upload game assets.");

        // Check if file was uploaded
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = isset($_FILES['file']) ? $this->getUploadErrorMessage($_FILES['file']['error']) : "No file uploaded.";
            return new ApiResponse(false, null, $errorMsg);
        }

        try {
            $uploadedFile = $_FILES['file'];
            $originalFileName = basename($uploadedFile['name']);
            $tempPath = $uploadedFile['tmp_name'];

            // Determine media type from file extension
            $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];

            if (in_array($extension, $imageExtensions)) {
                $mediaType = 'image';
            } elseif (in_array($extension, $videoExtensions)) {
                $mediaType = 'video';
            } else {
                return new ApiResponse(false, null, "Invalid file type. Only images and videos are allowed.");
            }

            // Get S3 client
            $s3Client = getS3Client();

            // Generate completely unique file name (UUID-like format)
            $uniqueId = uniqid('', true); // e.g., 6762abc123.456789
            $randomHash = bin2hex(random_bytes(8)); // 16 character hex string
            $generatedFileName = "{$mediaType}_{$gameId}_{$uniqueId}_{$randomHash}.{$extension}";
            $r2Path = "games/{$gameId}/media/{$generatedFileName}";

            // Read file content
            $fileContent = file_get_contents($tempPath);

            // Determine MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tempPath);
            finfo_close($finfo);

            // Upload to R2
            $result = $s3Client->putObject([
                'Bucket' => getBucketName(),
                'Key' => $r2Path,
                'Body' => $fileContent,
                'ContentType' => $mimeType ?: 'application/octet-stream'
            ]);

            // Get the next display order for this game
            global $db;
            $stmt = $db->prepare("SELECT COALESCE(MAX(display_order), -1) + 1 as next_order FROM game_media WHERE game_id = ?");
            $stmt->execute([$gameId]);
            $displayOrder = $stmt->fetchColumn();

            // Insert record into game_media table
            $stmt = $db->prepare("
                INSERT INTO game_media (game_id, media_type, file_path, file_name, display_order) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$gameId, $mediaType, $r2Path, $generatedFileName, $displayOrder]);

            $mediaId = $db->lastInsertId();

            return new ApiResponse(true, [
                'id' => $mediaId,
                'game_id' => $gameId,
                'media_type' => $mediaType,
                'file_path' => $r2Path,
                'file_name' => $generatedFileName,
                'original_file_name' => $originalFileName,
                'display_order' => $displayOrder,
                'url' => $result['ObjectURL'] ?? null
            ], "Asset uploaded successfully.");
        } catch (Exception $e) {
            return new ApiResponse(false, null, "Error uploading asset: " . $e->getMessage());
        }
    }

    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE directive in HTML form.",
            UPLOAD_ERR_PARTIAL => "File was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "File upload stopped by extension.",
            default => "Unknown upload error."
        };
    }


    private function isDeveloperVerified(int $developerId): bool
    {
        try {
            $isVerified = $this->userRepository->isUserVerified($developerId);
            return $isVerified;
        } catch (Exception $e) {
            return false;
        }
    }
}
