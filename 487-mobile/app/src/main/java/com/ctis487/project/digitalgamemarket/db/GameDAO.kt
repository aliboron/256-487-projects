package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import androidx.room.*
import com.ctis487.project.digitalgamemarket.model.Game
import okhttp3.Callback
import retrofit2.Call

@Dao
interface GameDAO {

    @Query("SELECT * FROM games ORDER BY created_at DESC")
    fun getAllGames(): LiveData<List<Game>>

    @Query("SELECT * FROM games ORDER BY created_at DESC")
    fun getAllGamesDirect(): List<Game>

    @Query("SELECT * FROM games WHERE id = :gameId")
    fun getGameById(gameId: Int): LiveData<Game>

    @Query("SELECT * FROM games WHERE is_approved = 1 ORDER BY created_at DESC")
    fun getApprovedGames(): LiveData<List<Game>>

    @Query("SELECT * FROM games WHERE developer_id = :developerId ORDER BY created_at DESC")
    fun getGamesByDeveloper(developerId: Int): LiveData<List<Game>>

    @Query("SELECT * FROM games WHERE genre = :genre AND is_approved = 1 ORDER BY created_at DESC")
    fun getGamesByGenre(genre: String): LiveData<List<Game>>

    @Query("SELECT * FROM games WHERE name LIKE '%' || :searchQuery || '%' AND is_approved = 1")
    fun searchGames(searchQuery: String): LiveData<List<Game>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(game: Game): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(games: List<Game>)

    @Update
    suspend fun update(game: Game)

    @Delete
    suspend fun delete(game: Game)

    @Query("DELETE FROM games")
    suspend fun deleteAll()

    @Query("SELECT DISTINCT genre FROM games WHERE is_approved = 1 ORDER BY genre ASC")
    fun getAllGenres(): LiveData<List<String>>
}

