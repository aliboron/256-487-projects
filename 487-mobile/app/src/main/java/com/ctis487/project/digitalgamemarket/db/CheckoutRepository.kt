package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.model.ApiResponse
import com.ctis487.project.digitalgamemarket.model.Checkout
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class CheckoutRepository(
    private val checkoutDAO: CheckoutDAO,
    private val apiService: GameMarketApiService
) {

    // Local Database Operations
    val allCheckouts: LiveData<List<Checkout>> = checkoutDAO.getAllCheckouts()

    fun getCheckoutById(checkoutId: Int): LiveData<Checkout> {
        return checkoutDAO.getCheckoutById(checkoutId)
    }

    fun getCheckoutsByUser(userId: Int): LiveData<List<Checkout>> {
        return checkoutDAO.getCheckoutsByUser(userId)
    }

    fun getCheckoutsByGame(gameId: Int): LiveData<List<Checkout>> {
        return checkoutDAO.getCheckoutsByGame(gameId)
    }

    fun getTotalSpentByUser(userId: Int): LiveData<Double> {
        return checkoutDAO.getTotalSpentByUser(userId)
    }

    suspend fun insert(checkout: Checkout): Long {
        return withContext(Dispatchers.IO) {
            checkoutDAO.insert(checkout)
        }
    }

    suspend fun insertAll(checkouts: List<Checkout>) {
        withContext(Dispatchers.IO) {
            checkoutDAO.insertAll(checkouts)
        }
    }

    suspend fun update(checkout: Checkout) {
        withContext(Dispatchers.IO) {
            checkoutDAO.update(checkout)
        }
    }

    suspend fun delete(checkout: Checkout) {
        withContext(Dispatchers.IO) {
            checkoutDAO.delete(checkout)
        }
    }

    suspend fun deleteAll() {
        withContext(Dispatchers.IO) {
            checkoutDAO.deleteAll()
        }
    }

    // API Operations
    suspend fun fetchCheckoutsFromApi(): Result<List<Checkout>> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.getAllCheckouts()
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
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

    suspend fun fetchCheckoutsByUserFromApi(userId: Int): Result<List<Checkout>> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.getCheckoutsByUser(userId)
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

    suspend fun createCheckoutOnApi(checkout: Checkout): Result<Checkout> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.createCheckout(checkout)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
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
}

