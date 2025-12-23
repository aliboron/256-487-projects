package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import com.ctis487.project.digitalgamemarket.model.GameMedia
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class GameMediaRepository(
    private val gameMediaDAO: GameMediaDAO
) {

    // Local Database Operations
    val allGameMedia: LiveData<List<GameMedia>> = gameMediaDAO.getAllGameMedia()

    fun getGameMediaById(mediaId: Int): LiveData<GameMedia> {
        return gameMediaDAO.getGameMediaById(mediaId)
    }

    fun getMediaByGame(gameId: Int): LiveData<List<GameMedia>> {
        return gameMediaDAO.getMediaByGame(gameId)
    }

    fun getMediaByGameAndType(gameId: Int, mediaType: String): LiveData<List<GameMedia>> {
        return gameMediaDAO.getMediaByGameAndType(gameId, mediaType)
    }

    suspend fun insert(gameMedia: GameMedia): Long {
        return withContext(Dispatchers.IO) {
            gameMediaDAO.insert(gameMedia)
        }
    }

    suspend fun insertAll(gameMediaList: List<GameMedia>) {
        withContext(Dispatchers.IO) {
            gameMediaDAO.insertAll(gameMediaList)
        }
    }

    suspend fun update(gameMedia: GameMedia) {
        withContext(Dispatchers.IO) {
            gameMediaDAO.update(gameMedia)
        }
    }

    suspend fun delete(gameMedia: GameMedia) {
        withContext(Dispatchers.IO) {
            gameMediaDAO.delete(gameMedia)
        }
    }

    suspend fun deleteAll() {
        withContext(Dispatchers.IO) {
            gameMediaDAO.deleteAll()
        }
    }

    suspend fun deleteByGame(gameId: Int) {
        withContext(Dispatchers.IO) {
            gameMediaDAO.deleteByGame(gameId)
        }
    }

    // Note: Media upload to API requires multipart/form-data
    // which should be handled separately using OkHttp MultipartBody
    // Example endpoint: POST /api/developers/{developerId}/games/{gameId}/assets
}

