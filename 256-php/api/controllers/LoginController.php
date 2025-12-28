<?php

class LoginController
{
    private PDO $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    #[Route("/login", "POST")]
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
            return;
        }

        ensureSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'] ?? '',
            'type' => $user['type']
        ];
        $_SESSION['last_activity'] = time();

        $token = $this->createToken($user['id']);

        $_SESSION["role"] = $user["type"];

        return new ApiResponse(true, [
            "token" => $token,
            "user" => [
                "id" => $user["id"],
                "username" => $user["username"],
                "type" => $user["type"]
            ],
        ], "Login successfull");
    }

    #[Route("/register", "POST")]
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $gender = $data['gender'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['type'] ?? '';
        $birth_date = $data["birth_date"] ?? '';

        if ($role !== 'game_developer' && $role !== 'user') {
            return new ApiResponse(false, null, "Invalid login options.");
        }

        $check = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            http_response_code(409);
            return new ApiResponse(false, null, "User already exists");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, gender, phone, birth_date, password, type) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$username, $email, $gender, $phone, $birth_date, $hashedPassword, $role]);

        $userId = $this->db->lastInsertId();

        ensureSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_data'] = [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'type' => $role
        ];
        $_SESSION['last_activity'] = time();

        $token = $this->createToken($userId);


        $_SESSION["role"] = $role;

        return new ApiResponse(true, [
            "redirect" => "/",
            "token" => $token,
            "user" => [
                "id" => $userId,
                "username" => $username,
                "type" => $role
            ],
        ], "Register successfull");
    }

    #[Route("/login/guest", "POST")]
    public function guest()
    {
        session_regenerate_id(true);
        $token = bin2hex(random_bytes(32));

        $_SESSION["role"] = "guest";

        return new ApiResponse(true, ["token" => $token], "Guest login successfull");
    }

    #[Route("/logout", "POST")]
    public function logout()
    {
        logout();

        return new ApiResponse(true, null, "Logout successfull");
    }

    #[Route("/auth/me", "GET")]
    public function getCurrentUser()
    {
        ensureSession();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_data'])) {
            http_response_code(401);
            return new ApiResponse(false, null, "Not authenticated");
        }

        $stmt = $this->db->prepare("SELECT id, username, email, type, is_verified FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(401);
            return new ApiResponse(false, null, "User not found");
        }

        return new ApiResponse(true, $user, "User retrieved successfully");
    }

    private function createToken(int $userId): string
    {
        $token = session_id();
        return $token;
    }
}
