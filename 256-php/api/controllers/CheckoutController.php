<?php

class Checkout
{
    public function __construct(
        public ?int $id,
        public string $date,
        public ?int $user_id,
        public float $payment_total,
        public ?int $game_id
    ) {}

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'date'          => $this->date,
            'user_id'       => $this->user_id,
            'payment_total' => $this->payment_total,
            'game_id'       => $this->game_id
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            date: $data['date'],
            user_id: $data['user_id'] ?? null,
            payment_total: (float)$data['payment_total'] ?? null,
            game_id: $data['game_id'] ?? null
        );
    }
}

class CheckoutRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all checkouts, only for admins
     * @return Checkout[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM checkouts 
            ORDER BY date DESC
        ");
        $stmt->execute();

        $checkouts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $checkouts[] = Checkout::fromArray($row);
        }
        return $checkouts;
    }


    /**
     * Get checkouts of specificUser
     * @return Checkout[]
     */
    public function getByUser(int $user_id): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM checkouts 
            WHERE user_id = :user_id 
            ORDER BY date DESC
        ");
        $stmt->execute(['user_id' => $user_id]);

        $checkouts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $checkouts[] = Checkout::fromArray($row);
        }
        return $checkouts;
    }

    /**
     * Find a checkout by ID
     */
    public function find(int $id): ?Checkout
    {
        $stmt = $this->db->prepare("SELECT * FROM checkouts WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Checkout::fromArray($row) : null;
    }

    /**
     * Create a new checkout
     */
    public function create(array $data): Checkout
    {
        $stmt = $this->db->prepare("
            INSERT INTO checkouts (date, user_id, payment_total, game_id)
            VALUES (:date, :user_id, :payment_total, :game_id)
        ");

        $stmt->execute([
            'date'  => $data['date'],
            'user_id'        => $data['user_id'] ?? null,
            'payment_total'  => $data['payment_total'],
            'game_id'    => $data['game_id'] ?? null
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Delete a checkout
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM checkouts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // UNDER CONSTRUCTION !!!!!!!!!!!!!!!!!!
    // /**
    //  * Search checkouts by name
    //  * @return Checkout[]
    //  */
    // public function search(string $query): array
    //  {
    //     $stmt = $this->db->prepare("
    //         SELECT * FROM checkouts 
    //         WHERE name LIKE :query
    //         ORDER BY date DESC
    //     ");
    //     $stmt->execute(['query' => "%$query%"]);

    //     $checkouts = [];
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         $checkouts[] = Checkout::fromArray($row);
    //     }
    //     return $checkouts;
    // }
}

class CheckoutController
{
    private CheckoutRepository $repository;

    public function __construct()
    {
        global $db;
        $this->repository = new CheckoutRepository($db);
    }

    /**
     * GET /checkouts/all - List all checkouts (including unapproved, for admin)
     */
    #[Route('/checkouts/all', 'GET')]
    public function listAllCheckouts(): ApiResponse
    {
        $checkouts = array_map(
            fn(Checkout $c) => $c->toArray(),
            $this->repository->getAll()
        );
        return new ApiResponse(true, $checkouts);
    }

    /**
     * GET /checkouts/user/{user_id} - Get checkouts by user
     */
    #[Route('/checkouts/user/{user_id}', 'GET')]
    public function getCheckoutsByUser(string $user_id): ApiResponse
    {
        $checkouts = array_map(
            fn(Checkout $c) => $c->toArray(),
            $this->repository->getByUser((int)$user_id)
        );
        return new ApiResponse(true, $checkouts);
    }


    /**
     * GET /checkouts/{id} - Get a specific checkouts
     */
    #[Route('/checkouts/{id}', 'GET')]
    public function getCheckout(string $id): ApiResponse
    {
        $checkout = $this->repository->find((int)$id);
        return $checkout
            ? new ApiResponse(true, $checkout->toArray())
            : new ApiResponse(false, null, 'Checkout not found');
    }


    // UNDER CONSTRUCTION !!!!!!!!!!!!!!
    // /**
    //  * GET /games/search/{query} - Search games by name
    //  */
    // #[Route('/games/search/{query}', 'GET')]
    // public function searchGames(string $query): ApiResponse
    // {
    //     $games = array_map(
    //         fn(Game $g) => $g->toArray(),
    //         $this->repository->search($query)
    //     );
    //     return new ApiResponse(true, $games);
    // }


    /**
     * POST /checkouts - Create a new game
     */
    #[Route('/checkouts', 'POST')]
    public function createCheckout(): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        // Validate required fields
        $required = ['date', 'user_id', 'payment_total', 'game_id'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                return new ApiResponse(false, null, "Field '$field' is required");
            }
        }

        try {
            $newCheckout = $this->repository->create($input);
            return new ApiResponse(true, $newCheckout->toArray(), 'Checkout created successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to create checkout: ' . $e->getMessage());
        }
    }


    /**
     * DELETE /games/{id} - Delete a game
     */
    #[Route('/checkouts/{id}', 'DELETE')]
    public function deleteCheckout(string $id): ApiResponse
    {
        $checkout = $this->repository->find((int)$id);
        if (!$checkout) {
            return new ApiResponse(false, null, 'Checkout not found');
        }

        try {
            $deleted = $this->repository->delete((int)$id);
            return $deleted
                ? new ApiResponse(true, null, 'Checkout deleted successfully')
                : new ApiResponse(false, null, 'Failed to delete checkout');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to delete checkout: ' . $e->getMessage());
        }
    }

}
