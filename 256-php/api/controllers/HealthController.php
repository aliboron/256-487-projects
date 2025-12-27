<?php
class HealthController
{
    private PDO $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    #[Route('/', 'GET')]
    public function health(): ApiResponse
    {
        return new ApiResponse(
            true,
            ['status' => 'healthy'],
            'API is up and running'
        );
    }

    #[Route('/health', 'GET')]
    public function healthAuth(): ApiResponse
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            return new ApiResponse(
                false,
                null,
                'Not authenticated'
            );
        }

        // Get user information from session
        ensureSession();
        $userId = $_SESSION['user_id'];
        $userData = $_SESSION['user_data'] ?? null;

        // If user data is not in session, fetch from database
        if (!$userData) {
            $stmt = $this->db->prepare("SELECT id, username, email, type FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return new ApiResponse(
            true,
            [
                'status' => 'authenticated',
                'user' => [
                    'id' => $userData['id'] ?? $userId,
                    'username' => $userData['username'] ?? 'Unknown',
                    'type' => $userData['type'] ?? 'user'
                ]
            ],
            'User is authenticated'
        );
    }

    #[Route('/forbidden', 'GET')]
    public function forbidden(): ApiResponse
    {
        return new ApiResponse(
            true,
            ['status' => 'forbidden'],
            'Access is forbidden'
        );
    }
}
