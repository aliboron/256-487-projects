<?php
    include "db.php";
class UserRepository
{
    /** @var array<string, User> */
    private array $users;
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db= $db;
        $this->users = [
            '1' => new User('1', 'John'),
            '2' => new User('2', 'Jane'),
            '3' => new User('3', 'Alice'),
        ];
    }
    public function deactivateUser(int $id):bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount()>0;
    }
    public function deleteUser(int $id):bool
    {
        $stmt = $this->db->prepare("DELETE FROM USERS WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        return $stmt->rowCount()>0;
    }
    public function getAll(): array
    {
        return array_values($this->users);
    }

    public function find(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function create(string $name): User
    {
        $id = (string)(count($this->users) + 1);
        $user = new User($id, $name);
        $this->users[$id] = $user;
        return $user;
    }
}

class User
{
    public function __construct(
        public string $id,
        public string $name
    ) {}

    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
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
        $users = array_map(fn(User $u) => $u->toArray(), $this->repository->getAll());
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
        $name  = $input['name'] ?? 'New User';

        $newUser = $this->repository->create($name);

        return new ApiResponse(true, $newUser->toArray(), 'Created');
    }
}
