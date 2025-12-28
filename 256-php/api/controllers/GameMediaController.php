<?php

class GameMedia
{
    public function __construct(
        public ?int $id,
        public int $game_id,
        public string $media_type,
        public string $file_path,
        public string $file_name,
        public int $display_order,
        public ?string $uploaded_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'game_id'       => $this->game_id,
            'media_type'    => $this->media_type,
            'file_path'     => $this->file_path,
            'file_name'     => $this->file_name,
            'display_order' => $this->display_order,
            'uploaded_at'   => $this->uploaded_at
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            game_id: (int)$data['game_id'],
            media_type: $data['media_type'],
            file_path: $data['file_path'],
            file_name: $data['file_name'],
            display_order: (int)$data['display_order'],
            uploaded_at: $data['uploaded_at'] ?? null
        );
    }
}

class GameMediaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all media entries
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM game_media ORDER BY id DESC");
        $stmt->execute();

        $media = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $media[] = GameMedia::fromArray($row);
        }
        return $media;
    }

    /**
     * Get all media for a specific game
     */
    public function getByGame(int $game_id): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM game_media 
            WHERE game_id = :game_id 
            ORDER BY display_order ASC
        ");
        $stmt->execute(['game_id' => $game_id]);

        $media = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $media[] = GameMedia::fromArray($row);
        }
        return $media;
    }

    /**
     * Find specific media by ID
     */
    public function find(int $id): ?GameMedia
    {
        $stmt = $this->db->prepare("SELECT * FROM game_media WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? GameMedia::fromArray($row) : null;
    }

    /**
     * Create a new media entry
     */
    public function create(array $data): GameMedia
    {
        $stmt = $this->db->prepare("
            INSERT INTO game_media (game_id, media_type, file_path, file_name, display_order)
            VALUES (:game_id, :media_type, :file_path, :file_name, :display_order)
        ");

        $stmt->execute([
            'game_id'       => $data['game_id'],
            'media_type'    => $data['media_type'] ?? 'image',
            'file_path'     => $data['file_path'],
            'file_name'     => $data['file_name'],
            'display_order' => $data['display_order'] ?? 0
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Delete a media entry
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM game_media WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}

class GameMediaController
{
    private GameMediaRepository $repository;

    public function __construct()
    {
        global $db;
        $this->repository = new GameMediaRepository($db);
    }

    #[Route('/media/all', 'GET')]
    public function listAllMedia(): ApiResponse
    {
        $media = array_map(
            fn(GameMedia $m) => $m->toArray(),
            $this->repository->getAll()
        );
        return new ApiResponse(true, $media);
    }

    #[Route('/media/game/{game_id}', 'GET')]
    public function getMediaByGame(string $game_id): ApiResponse
    {
        $media = array_map(
            fn(GameMedia $m) => $m->toArray(),
            $this->repository->getByGame((int)$game_id)
        );
        return new ApiResponse(true, $media);
    }

    #[Route('/media/{id}', 'GET')]
    public function getMedia(string $id): ApiResponse
    {
        $media = $this->repository->find((int)$id);
        return $media
            ? new ApiResponse(true, $media->toArray())
            : new ApiResponse(false, null, 'Media not found');
    }

    #[Route('/media', 'POST')]
    public function createMedia(): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        $required = ['game_id', 'file_path', 'file_name'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                return new ApiResponse(false, null, "Field '$field' is required");
            }
        }

        try {
            $newMedia = $this->repository->create($input);
            return new ApiResponse(true, $newMedia->toArray(), 'Media created successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to create media: ' . $e->getMessage());
        }
    }

    #[Route('/media/{id}', 'DELETE')]
    public function deleteMedia(string $id): ApiResponse
    {
        $media = $this->repository->find((int)$id);
        if (!$media) {
            return new ApiResponse(false, null, 'Media not found');
        }

        try {
            $deleted = $this->repository->delete((int)$id);
            return $deleted
                ? new ApiResponse(true, null, 'Media deleted successfully')
                : new ApiResponse(false, null, 'Failed to delete media');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to delete media: ' . $e->getMessage());
        }
    }
}
