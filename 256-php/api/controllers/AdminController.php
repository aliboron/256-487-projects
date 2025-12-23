<?php
include "db.php";
class AdminController
{
    private UserRepository $repository;
    private GameRepository $game_repository;
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->repository = new UserRepository($db);
        $this->game_repository = new GameRepository($db);
    }



    #[Route('/admin', 'GET')]
    public function listGames(): ApiResponse
    {
        $approvedGames = array_map(fn(Game $g) => $g->toArray(), $this->game_repository->getAllApproved());
        return new ApiResponse(true, $approvedGames);
    }
    #[Route('/admin/developer/{developerId}', 'DELETE')]
    public function deleteDeveloper(int $developerId): ApiResponse
    {
        $developer = $this->repository->find($developerId);
        if (!$developer) {
            return new ApiResponse(false, null, 'Developer not found');
        }
        if (!$this->repository->deleteUser($developerId)) {
            return new ApiResponse(false, null, 'Delete failed');
        }
        return new ApiResponse(true, null, 'Developer deleted successfully');
    }
    #[Route('/admin/developer/{developerId}', 'PATCH')]
    public function deactivateDeveloper(int $developerId): ApiResponse
    {
        $developer = $this->repository->find($developerId);
        if (!$developer) {
            return new ApiResponse(false, null, 'Developer not found');
        }

        if (!$this->repository->deactivateUser($developerId)) {
            return new ApiResponse(false, null, 'Deactivate failed');
        }
        $updatedDeveloper = $this->repository->find($developerId);

        return new ApiResponse(true, $updatedDeveloper?->toArray());
    }
    #[Route('/admin/developer/{developerId}', 'GET')]
    public function getGamesByDeveloper(string $developerId): ApiResponse
    {
        $games = array_map(
            fn(Game $g) => $g->toArray(),
            $this->game_repository->getByDeveloper((int)$developerId)
        );
        return new ApiResponse(true, $games);
    }

    #[Route('/admin/games/{gameid}', 'PATCH')]
    public function updateGame(string $id): ApiResponse
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        $game = $this->repository->find((int)$id);
        if (!$game) {
            return new ApiResponse(false, null, 'Game not found');
        }

        try {
            $updatedGame = $this->game_repository->update((int)$id, $input);
            return new ApiResponse(true, $updatedGame->toArray(), 'Game updated successfully');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to update game: ' . $e->getMessage());
        }
    }
    #[Route('/admin/games/{gameid}', 'PATCH')]
    public function approve(int $id): ?Game
    {
        $stmt = $this->db->prepare("UPDATE games SET is_approved = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $this->game_repository->find($id);
    }
    #[Route('/admin/games/{gameid}', 'PATCH')]
    public function reject(int $id): ?Game
    {
        $stmt = $this->db->prepare("UPDATE games SET is_approved = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $this->game_repository->find($id);
    }
    #[Route('/admin/games/{gameid}', 'DELETE')]
    public function deleteGame(string $id): ApiResponse
    {
        $game = $this->repository->find((int)$id);
        if (!$game) {
            return new ApiResponse(false, null, 'Game not found');
        }

        try {
            $deleted = $this->game_repository->delete((int)$id);
            return $deleted
                ? new ApiResponse(true, null, 'Game deleted successfully')
                : new ApiResponse(false, null, 'Failed to delete game');
        } catch (Exception $e) {
            return new ApiResponse(false, null, 'Failed to delete game: ' . $e->getMessage());
        }
    }
}
