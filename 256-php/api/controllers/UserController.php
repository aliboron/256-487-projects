<?php

class User
{
    public function __construct(
        public ?int $id,
        public string $username,
        public string $email,
        public ?string $phone,
        public string $password,
        public ?string $gender,
        public string $type,
        public int $is_verified,
        public ?string $birth_date,
        public ?string $registered_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'phone'         => $this->phone,
            // 'password'   => $this->password, // Typically hidden in API responses
            'gender'        => $this->gender,
            'type'          => $this->type,
            'is_verified'   => $this->is_verified,
            'birth_date'    => $this->birth_date,
            'registered_at' => $this->registered_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            username: $data['username'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'],
            gender: $data['gender'] ?? null,
            type: $data['type'] ?? 'user',
            is_verified: (int)($data['is_verified'] ?? 0),
            birth_date: $data['birth_date'] ?? null,
            registered_at: $data['registered_at'] ?? null
        );
    }
}


class UserRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all users
     * @return User[]
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY registered_at DESC");
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = User::fromArray($row);
        }
        return $users;
    }

    /**
     * Find user by ID
     */
    public function find(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromArray($row) : null;
    }

    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, phone, password, gender, type, is_verified, birth_date)
            VALUES (:username, :email, :phone, :password, :gender, :type, :is_verified, :birth_date)
        ");

        $stmt->execute([
            'username'    => $data['username'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'password'    => password_hash($data['password'], PASSWORD_DEFAULT), // Securely hash password
            'gender'      => $data['gender'] ?? null,
            'type'        => $data['type'] ?? 'user',
            'is_verified' => $data['is_verified'] ?? 0,
            'birth_date'  => $data['birth_date'] ?? null
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Update a user
     */
    public function update(int $id, array $data): ?User
    {
        $fields = [];
        $params = ['id' => $id];

        // Allowed fields for update
        $allowedFields = ['username', 'email', 'phone', 'gender', 'type', 'is_verified', 'birth_date'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        // Handle password separately to ensure hashing
        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return $this->find($id);
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->find($id);
    }

    /**
     * Delete a user
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete a user (alias for delete method)
     */
    public function deleteUser(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Deactivate a user by setting is_verified to 0
     */
    public function deactivateUser(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a user is verified
     */
    public function isUserVerified(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT is_verified FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && (int)$result['is_verified'] === 1;
    }
}


class UserController
{
    private UserRepository $repository;

    public function __construct()
    {
        global $db;
        $this->repository = new UserRepository($db);
    }

    #[Route('/users', 'GET')]
    public function listUsers(): ApiResponse
    {
        $users = array_map(
            fn(User $u) => $u->toArray(),
            $this->repository->getAll()
        );
        return new ApiResponse(true, $users);
    }

    #[Route('/users/{id}', 'GET')]
    public function getUser(int $id): ApiResponse
    {
        $user = $this->repository->find($id);
        return $user
            ? new ApiResponse(true, $user->toArray())
            : new ApiResponse(false, null, 'User not found');
    }

    #[Route('/users', 'POST')]
    public function createUser(): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        // Validate required fields based on schema
        $required = ['username', 'email', 'password'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                return new ApiResponse(false, null, "Field '$field' is required");
            }
        }

        try {
            $newUser = $this->repository->create($input);
            return new ApiResponse(true, $newUser->toArray(), 'User created successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to create user: ' . $e->getMessage());
        }
    }

    #[Route('/users/{id}', 'PUT')]
    public function updateUser(string $id): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        $user = $this->repository->find((int)$id);
        if (!$user) {
            return new ApiResponse(false, null, 'User not found');
        }

        try {
            $updatedUser = $this->repository->update((int)$id, $input);
            return new ApiResponse(true, $updatedUser->toArray(), 'User updated successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to update user: ' . $e->getMessage());
        }
    }

    #[Route('/users/{id}', 'DELETE')]
    public function deleteUser(string $id): ApiResponse
    {
        $user = $this->repository->find((int)$id);
        if (!$user) {
            return new ApiResponse(false, null, 'User not found');
        }

        try {
            $deleted = $this->repository->delete((int)$id);
            return $deleted
                ? new ApiResponse(true, null, 'User deleted successfully')
                : new ApiResponse(false, null, 'Failed to delete user');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
