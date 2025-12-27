package com.ctis487.project.digitalgamemarket.model

import com.google.gson.annotations.SerializedName

/**
 * Generic API Response wrapper
 * @param T The type of data being returned
 */
data class ApiResponse<T>(
    @SerializedName("success")
    val success: Boolean,

    @SerializedName("data")
    val data: T?,

    @SerializedName("message")
    val message: String? = null
)

/**
 * Health check response
 */
data class HealthStatus(
    @SerializedName("status")
    val status: String
)

