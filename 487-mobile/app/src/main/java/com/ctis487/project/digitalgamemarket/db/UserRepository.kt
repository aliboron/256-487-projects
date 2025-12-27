package com.ctis487.project.digitalgamemarket.db

import androidx.lifecycle.LiveData
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.model.User
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class UserRepository(
    private val userDAO: UserDAO,
    private val apiService: GameMarketApiService
) {

    // Local Database Operations
    val allUsers: LiveData<List<User>> = userDAO.getAllUsers()
    val allDevelopers: LiveData<List<User>> = userDAO.getAllDevelopers()

    fun getUserById(userId: Int): LiveData<User> {
        return userDAO.getUserById(userId)
    }

    fun getUsersByType(userType: String): LiveData<List<User>> {
        return userDAO.getUsersByType(userType)
    }

    suspend fun getUserByUsername(username: String): User? {
        return withContext(Dispatchers.IO) {
            userDAO.getUserByUsername(username)
        }
    }

    suspend fun getUserByEmail(email: String): User? {
        return withContext(Dispatchers.IO) {
            userDAO.getUserByEmail(email)
        }
    }

    suspend fun insert(user: User): Long {
        return withContext(Dispatchers.IO) {
            userDAO.insert(user)
        }
    }

    suspend fun insertAll(users: List<User>) {
        withContext(Dispatchers.IO) {
            userDAO.insertAll(users)
        }
    }

    suspend fun update(user: User) {
        withContext(Dispatchers.IO) {
            userDAO.update(user)
        }
    }

    suspend fun delete(user: User) {
        withContext(Dispatchers.IO) {
            userDAO.delete(user)
        }
    }

    suspend fun deleteAll() {
        withContext(Dispatchers.IO) {
            userDAO.deleteAll()
        }
    }

    // API Operations
    suspend fun fetchUsersFromApi(): Result<List<User>> {
        val result = Utils.safeApiCall { apiService.getAllUsers() }
        result.onSuccess { insertAll(it) }
        return result
    }

    suspend fun fetchUserByIdFromApi(userId: Int): Result<User> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.getUserById(userId)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        insert(apiResponse.data)
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "User not found"))
                    }
                } else {
                    Result.failure(Exception("HTTP ${response.code()}: ${response.message()}"))
                }
            } catch (e: Exception) {
                Result.failure(e)
            }
        }
    }

    suspend fun createUserOnApi(userData: Map<String, String>): Result<User> {
        return withContext(Dispatchers.IO) {
            try {
                val response = apiService.createUser(userData)
                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true && apiResponse.data != null) {
                        insert(apiResponse.data)
                        Result.success(apiResponse.data)
                    } else {
                        Result.failure(Exception(apiResponse?.message ?: "User creation failed"))
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

