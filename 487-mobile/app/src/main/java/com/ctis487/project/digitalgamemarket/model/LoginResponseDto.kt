package com.ctis487.project.digitalgamemarket.model

import com.google.gson.annotations.SerializedName

data class LoginResponseDto(
    @SerializedName("token")
    val token: String,

    @SerializedName("user")
    val user: UserDto
)