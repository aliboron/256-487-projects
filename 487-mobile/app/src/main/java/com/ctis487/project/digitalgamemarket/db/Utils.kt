package com.ctis487.project.digitalgamemarket.db

import com.ctis487.project.digitalgamemarket.model.ApiResponse
import com.ctis487.project.digitalgamemarket.model.GameMedia
import com.ctis487.project.digitalgamemarket.model.User

object Utils {
    var baseUrl: String = "https://ctis256.sezertetik.dev"
    const val DATABASENAME = "digital_game_market_db"
    val SESSION: User? = null

    val bannerImages = ArrayList<GameMedia>()

    suspend fun <T> safeApiCall(block: suspend () -> retrofit2.Response<ApiResponse<T>>): Result<T> {
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
