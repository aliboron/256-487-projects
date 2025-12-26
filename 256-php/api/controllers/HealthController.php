<?php
class HealthController
{
    #[Route('/', 'GET')]
    public function health(): ApiResponse
    {
        return new ApiResponse(
            true,
            ['status' => 'healthy'],
            'API is up and running'
        );
    }
}
