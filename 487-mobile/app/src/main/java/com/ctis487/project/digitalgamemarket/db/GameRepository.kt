package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import androidx.room.Transaction
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.model.ApiResponse
import com.ctis487.project.digitalgamemarket.model.Game
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.withContext

class GameRepository(
    private val gameDAO: GameDAO,
    private val apiService: GameMarketApiService
) {
    // Local Database Operations
    val allGames: LiveData<List<Game>> = gameDAO.getAllGames()
    val approvedGames: LiveData<List<Game>> = gameDAO.getApprovedGames()
    val allGenres: LiveData<List<String>> = gameDAO.getAllGenres()

    fun getGameById(gameId: Int): LiveData<Game> {
        return gameDAO.getGameById(gameId)
    }

    fun getGamesByDeveloper(developerId: Int): LiveData<List<Game>> {
        return gameDAO.getGamesByDeveloper(developerId)
    }

    fun getGamesByGenre(genre: String): LiveData<List<Game>> {
        return gameDAO.getGamesByGenre(genre)
    }

    fun searchGames(query: String): LiveData<List<Game>> {
        return gameDAO.searchGames(query)
    }

    suspend fun insert(game: Game): Long {
        return withContext(Dispatchers.IO) {
            gameDAO.insert(game)
        }
    }

    suspend fun insertAll(games: List<Game>) {
        withContext(Dispatchers.IO) {
            gameDAO.insertAll(games)
        }
    }

    suspend fun update(game: Game) {
        withContext(Dispatchers.IO) {
            gameDAO.update(game)
        }
    }

    suspend fun delete(game: Game) {
        withContext(Dispatchers.IO) {
            gameDAO.delete(game)
        }
    }

    suspend fun deleteAll() {
        withContext(Dispatchers.IO) {
            gameDAO.deleteAll()
        }
    }

    // API Operations
    suspend fun fetchGamesFromApi(): Result<List<Game>> = withContext(Dispatchers.IO) {
        val result = safeApiCall { apiService.getAllGames() }
        result.onSuccess { insertAll(it) }
        result
    }

    @Transaction
    suspend fun fetchGameByIdFromApi(gameId: Int): Result<Game> = withContext(Dispatchers.IO) {
        val result = safeApiCall { apiService.getGameById(gameId) }
        result.onSuccess { insert(it) }
        result
    }

    suspend fun fetchAllGamesIncludingUnapprovedFromApi(): Result<List<Game>> = withContext(Dispatchers.IO) {
        safeApiCall { apiService.getAllGamesIncludingUnapproved() }
    }

    suspend fun createGameOnApi(game: Game): Result<Game> = withContext(Dispatchers.IO) {
        val result = safeApiCall { apiService.createGame(game) }
        result.onSuccess { insert(it) }
        result
    }

    suspend fun updateGameOnApi(gameId: Int, game: Game): Result<Game> = withContext(Dispatchers.IO) {
        val result = safeApiCall { apiService.updateGame(gameId, game) }
        result.onSuccess { update(it) }
        result
    }

    suspend fun searchGamesOnApi(query: String): Result<List<Game>> = withContext(Dispatchers.IO) {
        safeApiCall { apiService.searchGames(query) }
    }

    private suspend fun <T> safeApiCall(block: suspend () -> retrofit2.Response<ApiResponse<T>>): Result<T> {
        return try {
            val response = block()
            if (!response.isSuccessful) {
                return Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
            }

            val body = response.body()
            if (body?.success == true && body.data != null) {
                Result.success(body.data)
            } else {
                Result.failure(Exception(body?.message ?: "Unknown error"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

}

