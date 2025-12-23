package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import androidx.room.*
import com.ctis487.project.digitalgamemarket.model.GameMedia

@Dao
interface GameMediaDAO {

    @Query("SELECT * FROM game_media ORDER BY display_order ASC")
    fun getAllGameMedia(): LiveData<List<GameMedia>>

    @Query("SELECT * FROM game_media WHERE id = :mediaId")
    fun getGameMediaById(mediaId: Int): LiveData<GameMedia>

    @Query("SELECT * FROM game_media WHERE game_id = :gameId ORDER BY display_order ASC")
    fun getMediaByGame(gameId: Int): LiveData<List<GameMedia>>

    @Query("SELECT * FROM game_media WHERE game_id = :gameId AND media_type = :mediaType ORDER BY display_order ASC")
    fun getMediaByGameAndType(gameId: Int, mediaType: String): LiveData<List<GameMedia>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(gameMedia: GameMedia): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(gameMediaList: List<GameMedia>)

    @Update
    suspend fun update(gameMedia: GameMedia)

    @Delete
    suspend fun delete(gameMedia: GameMedia)

    @Query("DELETE FROM game_media")
    suspend fun deleteAll()

    @Query("DELETE FROM game_media WHERE game_id = :gameId")
    suspend fun deleteByGame(gameId: Int)
}

