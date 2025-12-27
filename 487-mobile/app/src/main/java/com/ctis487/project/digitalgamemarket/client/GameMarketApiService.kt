package com.ctis487.project.digitalgamemarket.client

import com.ctis487.project.digitalgamemarket.model.*
import retrofit2.Response
import retrofit2.http.*

interface GameMarketApiService {

    // Health Check
    @GET("/api")
    suspend fun healthCheck(): Response<ApiResponse<HealthStatus>>

    // User Endpoints
    @GET("/api/users")
    suspend fun getAllUsers(): Response<ApiResponse<List<User>>>

    @GET("/api/users/{id}")
    suspend fun getUserById(@Path("id") userId: Int): Response<ApiResponse<User>>

    @POST("/api/users")
    suspend fun createUser(@Body user: Map<String, String>): Response<ApiResponse<User>>

    // Game Endpoints - Public
    @GET("/api/games")
    suspend fun getAllGames(): Response<ApiResponse<List<Game>>>

    @GET("/api/games/all")
    suspend fun getAllGamesIncludingUnapproved(): Response<ApiResponse<List<Game>>>

    @GET("/api/games/{id}")
    suspend fun getGameById(@Path("id") gameId: Int): Response<ApiResponse<Game>>

    @GET("/api/games/genre/{genre}")
    suspend fun getGamesByGenre(
        @Path("genre") genre: String
    ): Response<ApiResponse<List<Game>>>

    @GET("/api/games/search/{query}")
    suspend fun searchGames(
        @Path("query") query: String
    ): Response<ApiResponse<List<Game>>>

    @POST("/api/games")
    suspend fun createGame(@Body game: Game): Response<ApiResponse<Game>>

    @PUT("/api/games/{id}")
    suspend fun updateGame(
        @Path("id") gameId: Int,
        @Body game: Game
    ): Response<ApiResponse<Game>>

    @DELETE("/api/games/{id}")
    suspend fun deleteGame(@Path("id") gameId: Int): Response<ApiResponse<Unit>>

    @PATCH("/api/games/{id}/approve")
    suspend fun approveGame(@Path("id") gameId: Int): Response<ApiResponse<Game>>

    // Developer Endpoints
    @GET("/api/developers/{developerId}/games")
    suspend fun getDeveloperGames(
        @Path("developerId") developerId: Int,
        @Query("statusFilter") statusFilter: String = "all"
    ): Response<ApiResponse<List<Game>>>

    @GET("/api/developers/{developerId}/games/{gameId}/sales")
    suspend fun getUsersForGame(
        @Path("developerId") developerId: Int,
        @Path("gameId") gameId: Int
    ): Response<ApiResponse<List<User>>>

    @POST("/api/developers/{developerId}/games")
    suspend fun createGameForDeveloper(
        @Path("developerId") developerId: Int,
        @Body game: Game
    ): Response<ApiResponse<Game>>

    @PUT("/api/developers/{developerId}/games/{gameId}")
    suspend fun updateGameForDeveloper(
        @Path("developerId") developerId: Int,
        @Path("gameId") gameId: Int,
        @Body game: Game
    ): Response<ApiResponse<Game>>

    @DELETE("/api/developers/{developerId}/games/{gameId}")
    suspend fun deleteGameForDeveloper(
        @Path("developerId") developerId: Int,
        @Path("gameId") gameId: Int
    ): Response<ApiResponse<Unit>>

    // Admin Endpoints
    @GET("/api/admin")
    suspend fun adminListGames(): Response<ApiResponse<List<Game>>>

    @GET("/api/admin/developer/{developerId}")
    suspend fun adminGetGamesByDeveloper(
        @Path("developerId") developerId: Int
    ): Response<ApiResponse<List<Game>>>

    @DELETE("/api/admin/developer/{developerId}")
    suspend fun adminDeleteDeveloper(
        @Path("developerId") developerId: Int
    ): Response<ApiResponse<Unit>>

    @PATCH("/api/admin/developer/{developerId}")
    suspend fun adminDeactivateDeveloper(
        @Path("developerId") developerId: Int
    ): Response<ApiResponse<User>>

    @PATCH("/api/admin/games/{gameId}")
    suspend fun adminUpdateGame(
        @Path("gameId") gameId: Int,
        @Body game: Game
    ): Response<ApiResponse<Game>>

    @DELETE("/api/admin/games/{gameId}")
    suspend fun adminDeleteGame(
        @Path("gameId") gameId: Int
    ): Response<ApiResponse<Unit>>

    // Checkout Endpoints
    @GET("/api/checkouts/all")
    suspend fun getAllCheckouts(): Response<ApiResponse<List<Checkout>>>

    @GET("/api/checkouts/{id}")
    suspend fun getCheckoutById(@Path("id") checkoutId: Int): Response<ApiResponse<Checkout>>

    @GET("/api/checkouts/user/{userId}")
    suspend fun getCheckoutsByUser(
        @Path("userId") userId: Int
    ): Response<ApiResponse<List<Checkout>>>

    @POST("/api/checkouts")
    suspend fun createCheckout(@Body checkout: Checkout): Response<ApiResponse<Checkout>>

    @DELETE("/api/checkouts/{id}")
    suspend fun deleteCheckout(@Path("id") checkoutId: Int): Response<ApiResponse<Unit>>

    // Game Media Endpoints (Developer uploads via multipart, not JSON)
    // Note: Media upload requires multipart/form-data, handled separately
    // @POST("/api/developers/{developerId}/games/{gameId}/assets")
    // suspend fun uploadGameAsset(...)
    @POST("/api/login")
    suspend fun login(
        @Body request: LoginApiRequest
    ): Response<ApiResponse<LoginResponseDto>>

    @GET("/api/media/all")
    suspend fun getMedia() : Response<ApiResponse<List<GameMedia>>>



    @POST("/api/login/guest")
    suspend fun guestLogin(): Response<ApiResponse<Any>>

    @POST("/api/logout")
    suspend fun logout(): Response<ApiResponse<Any>>
}

