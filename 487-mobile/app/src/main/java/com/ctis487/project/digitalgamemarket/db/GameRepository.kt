package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.model.Game
import kotlinx.coroutines.Dispatchers
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
    suspend fun fetchGamesFromApi(): Result<List<Game>> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.getAllGames()
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        // Optionally save to local database
                        insertAll(apiResponse.data)
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "Unknown error"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }

    suspend fun fetchAllGamesIncludingUnapprovedFromApi(): Result<List<Game>> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.getAllGamesIncludingUnapproved()
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "Unknown error"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }

    suspend fun createGameOnApi(game: Game): Result<Game> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.createGame(game)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        // Optionally save to local database
                        insert(apiResponse.data)
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "Unknown error"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }

    suspend fun updateGameOnApi(gameId: Int, game: Game): Result<Game> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.updateGame(gameId, game)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        // Update local database
                        update(apiResponse.data)
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "Unknown error"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }

    suspend fun searchGamesOnApi(query: String): Result<List<Game>> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.searchGames(query)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "Unknown error"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }
}

