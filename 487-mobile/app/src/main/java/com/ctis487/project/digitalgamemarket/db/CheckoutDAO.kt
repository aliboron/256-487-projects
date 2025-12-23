package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import androidx.room.*
import com.ctis487.project.digitalgamemarket.model.Checkout

@Dao
interface CheckoutDAO {

    @Query("SELECT * FROM checkouts ORDER BY date DESC")
    fun getAllCheckouts(): LiveData<List<Checkout>>

    @Query("SELECT * FROM checkouts WHERE id = :checkoutId")
    fun getCheckoutById(checkoutId: Int): LiveData<Checkout>

    @Query("SELECT * FROM checkouts WHERE user_id = :userId ORDER BY date DESC")
    fun getCheckoutsByUser(userId: Int): LiveData<List<Checkout>>

    @Query("SELECT * FROM checkouts WHERE game_id = :gameId ORDER BY date DESC")
    fun getCheckoutsByGame(gameId: Int): LiveData<List<Checkout>>

    @Query("SELECT SUM(payment_total) FROM checkouts WHERE user_id = :userId")
    fun getTotalSpentByUser(userId: Int): LiveData<Double>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(checkout: Checkout): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(checkouts: List<Checkout>)

    @Update
    suspend fun update(checkout: Checkout)

    @Delete
    suspend fun delete(checkout: Checkout)

    @Query("DELETE FROM checkouts")
    suspend fun deleteAll()
}

