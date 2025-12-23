<?php

class Game
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $description,
        public float $price,
        public int $is_approved,
        public ?string $logo_path,
        public int $developer_id,
        public string $genre,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'price'        => $this->price,
            'is_approved'  => $this->is_approved,
            'logo_path'    => $this->logo_path,
            'developer_id' => $this->developer_id,
            'genre'        => $this->genre,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'],
            price: (float)$data['price'],
            is_approved: (bool)($data['is_approved'] ?? false),
            logo_path: $data['logo_path'] ?? null,
            developer_id: (int)$data['developer_id'],
            genre: $data['genre'],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }
}

class GameRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all games
     * @return Game[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM games 
            ORDER BY created_at DESC
        ");
        $stmt->execute();

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = Game::fromArray($row);
        }
        return $games;
    }

    /**
     * Get all approved games
     * @return Game[]
     */
    public function getAllApproved(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM games 
            WHERE is_approved = 1 
            ORDER BY created_at DESC
        ");
        $stmt->execute();

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = Game::fromArray($row);
        }
        return $games;
    }

    /**
     * Get games by genre
     * @return Game[]
     */
    public function getByGenre(string $genre): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM games 
            WHERE genre = :genre AND is_approved = 1 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['genre' => $genre]);

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = Game::fromArray($row);
        }
        return $games;
    }

    /**
     * Get games by developer
     * @return Game[]
     */
    public function getByDeveloper(int $developerId, string $statusFilter): array
    {
        if ($statusFilter === 'approved') {
            $stmt = $this->db->prepare("
                SELECT * FROM games 
                WHERE developer_id = :developer_id AND is_approved = 1
                ORDER BY created_at DESC
            ");
        } elseif ($statusFilter === 'pending') {
            $stmt = $this->db->prepare("
                SELECT * FROM games 
                WHERE developer_id = :developer_id AND is_approved = 0
                ORDER BY created_at DESC
            ");
        } elseif ($statusFilter === 'rejected') {
            $stmt = $this->db->prepare("
                SELECT * FROM games 
                WHERE developer_id = :developer_id AND is_approved = -1
                ORDER BY created_at DESC
            ");
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM games 
                WHERE developer_id = :developer_id 
                ORDER BY created_at DESC
            ");
        }
        $stmt->execute(['developer_id' => $developerId]);

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = Game::fromArray($row);
        }
        return $games;
    }

    /**
     * Find a game by ID
     */
    public function find(int $id): ?Game
    {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Game::fromArray($row) : null;
    }

    /**
     * Create a new game
     */
    public function create(array $data): Game
    {
        $stmt = $this->db->prepare("
            INSERT INTO games (name, description, price, is_approved, logo_path, developer_id, genre)
            VALUES (:name, :description, :price, :is_approved, :logo_path, :developer_id, :genre)
        ");

        $stmt->execute([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'price'        => $data['price'],
            'is_approved'  => $data['is_approved'] ?? 0,
            'logo_path'    => $data['logo_path'] ?? null,
            'developer_id' => $data['developer_id'],
            'genre'        => $data['genre']
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    public function createGame(Game $game): Game
    {
        $stmt = $this->db->prepare("
            INSERT INTO games (name, description, price, is_approved, logo_path, developer_id, genre)
            VALUES (:name, :description, :price, :is_approved, :logo_path, :developer_id, :genre)
        ");

        $stmt->execute([
            'name'         => $game->name,
            'description'  => $game->description,
            'price'        => $game->price,
            'is_approved'  => $game->is_approved ? 1 : 0,
            'logo_path'    => $game->logo_path,
            'developer_id' => $game->developer_id,
            'genre'        => $game->genre
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    public function reject(int $id): ?Game
    {
        $stmt = $this->db->prepare("UPDATE games SET is_approved = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $this->find($id);
    }

    public function updateGame(Game $game): ?Game
    {
        $stmt = $this->db->prepare("
            UPDATE games SET 
                name = :name,
                description = :description,
                price = :price,
                is_approved = :is_approved,
                logo_path = :logo_path,
                developer_id = :developer_id,
                genre = :genre
            WHERE id = :id
        ");

        $stmt->execute([
            'id'           => $game->id,
            'name'         => $game->name,
            'description'  => $game->description,
            'price'        => $game->price,
            'is_approved'  => $game->is_approved ? 1 : 0,
            'logo_path'    => $game->logo_path,
            'developer_id' => $game->developer_id,
            'genre'        => $game->genre
        ]);

        return $this->find((int)$game->id);
    }

    public function getUsersByGame(int $gameId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.* FROM users u
            JOIN game_users gu ON u.id = gu.user_id
            WHERE gu.game_id = :game_id
        "); # TODO: FIX
        $stmt->execute(['game_id' => $gameId]);

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row; // TODO: Convert to User
        }
        return $users;
    }

    /**
     * Update an existing game
     */
    public function update(int $id, array $data): ?Game
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['name', 'description', 'price', 'is_approved', 'logo_path', 'developer_id', 'genre'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return $this->find($id);
        }

        $sql = "UPDATE games SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->find($id);
    }

    /**
     * Delete a game
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Approve a game
     */
    public function approve(int $id): ?Game
    {
        $stmt = $this->db->prepare("UPDATE games SET is_approved = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $this->find($id);
    }


    public function reject(int $id): ?Game
    {
        $stmt = $this->db->prepare("UPDATE games SET is_approved = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $this->find($id);
    }

    /**
     * Search games by name
     * @return Game[]
     */
    public function search(string $query): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM games 
            WHERE name LIKE :query AND is_approved = 1 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['query' => "%$query%"]);

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = Game::fromArray($row);
        }
        return $games;
    }
}

class GameController
{
    private GameRepository $repository;

    public function __construct()
    {
        global $db;
        $this->repository = new GameRepository($db);
    }

    /**
     * GET /games - List all approved games
     */
    #[Route('/games', 'GET')]
    public function listGames(): ApiResponse
    {
        $games = array_map(
            fn(Game $g) => $g->toArray(),
            $this->repository->getAllApproved()
        );
        return new ApiResponse(true, $games);
    }

    /**
     * GET /games/all - List all games (including unapproved, for admin)
     */
    #[Route('/games/all', 'GET')]
    public function listAllGames(): ApiResponse
    {
        $games = array_map(
            fn(Game $g) => $g->toArray(),
            $this->repository->getAll()
        );
        return new ApiResponse(true, $games);
    }

    /**
     * GET /games/{id} - Get a specific game
     */
    #[Route('/games/{id}', 'GET')]
    public function getGame(string $id): ApiResponse
    {
        $game = $this->repository->find((int)$id);
        return $game
            ? new ApiResponse(true, $game->toArray())
            : new ApiResponse(false, null, 'Game not found');
    }

    /**
     * GET /games/genre/{genre} - Get games by genre
     */
    #[Route('/games/genre/{genre}', 'GET')]
    public function getGamesByGenre(string $genre): ApiResponse
    {
        $games = array_map(
            fn(Game $g) => $g->toArray(),
            $this->repository->getByGenre($genre)
        );
        return new ApiResponse(true, $games);
    }

    /**
     * GET /games/developer/{developerId} - Get games by developer
     */
    // #[Route('/games/developer/{developerId}', 'GET')]
    // public function getGamesByDeveloper(string $developerId): ApiResponse
    // {
    //     $games = array_map(
    //         fn(Game $g) => $g->toArray(),
    //         $this->repository->getByDeveloper((int)$developerId)
    //     );
    //     return new ApiResponse(true, $games);
    // }

    /**
     * GET /games/search/{query} - Search games by name
     */
    #[Route('/games/search/{query}', 'GET')]
    public function searchGames(string $query): ApiResponse
    {
        $games = array_map(
            fn(Game $g) => $g->toArray(),
            $this->repository->search($query)
        );
        return new ApiResponse(true, $games);
    }

    /**
     * POST /games - Create a new game
     */
    #[Route('/games', 'POST')]
    public function createGame(): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        // Validate required fields
        $required = ['name', 'description', 'price', 'developer_id', 'genre'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                return new ApiResponse(false, null, "Field '$field' is required");
            }
        }

        try {
            $newGame = $this->repository->create($input);
            return new ApiResponse(true, $newGame->toArray(), 'Game created successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to create game: ' . $e->getMessage());
        }
    }

    /**
     * PUT /games/{id} - Update an existing game
     */
    #[Route('/games/{id}', 'PUT')]
    public function updateGame(string $id): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        $game = $this->repository->find((int)$id);
        if (!$game) {
            return new ApiResponse(false, null, 'Game not found');
        }

        try {
            $updatedGame = $this->repository->update((int)$id, $input);
            return new ApiResponse(true, $updatedGame->toArray(), 'Game updated successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to update game: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /games/{id} - Delete a game
     */
    #[Route('/games/{id}', 'DELETE')]
    public function deleteGame(string $id): ApiResponse
    {
        $game = $this->repository->find((int)$id);
        if (!$game) {
            return new ApiResponse(false, null, 'Game not found');
        }

        try {
            $deleted = $this->repository->delete((int)$id);
            return $deleted
                ? new ApiResponse(true, null, 'Game deleted successfully')
                : new ApiResponse(false, null, 'Failed to delete game');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to delete game: ' . $e->getMessage());
        }
    }

    /**
     * PATCH /games/{id}/approve - Approve a game
     */
    #[Route('/games/{id}/approve', 'PATCH')]
    public function approveGame(string $id): ApiResponse
    {
        $game = $this->repository->find((int)$id);
        if (!$game) {
            return new ApiResponse(false, null, 'Game not found');
        }

        try {
            $approvedGame = $this->repository->approve((int)$id);
            return new ApiResponse(true, $approvedGame->toArray(), 'Game approved successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to approve game: ' . $e->getMessage());
        }
    }
}
