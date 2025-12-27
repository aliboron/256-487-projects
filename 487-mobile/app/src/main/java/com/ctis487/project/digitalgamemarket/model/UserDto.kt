package com.ctis487.project.digitalgamemarket.model

import com.google.gson.annotations.SerializedName

data class UserDto(
    @SerializedName("id")
    val id: Int,

    @SerializedName("username")
    val username: String,

    @SerializedName("type")
    val type: String
)
