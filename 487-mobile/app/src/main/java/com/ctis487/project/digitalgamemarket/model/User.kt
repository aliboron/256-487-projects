package com.ctis487.project.digitalgamemarket.model

import androidx.room.ColumnInfo
import androidx.room.Entity
import androidx.room.Index
import androidx.room.PrimaryKey
import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.parcelize.Parcelize

@Parcelize
@Entity(
    tableName = "users",
    indices = [
        Index(value = ["username"], unique = true),
        Index(value = ["email"], unique = true),
        Index(value = ["phone"], unique = true),
        Index(value = ["type"])
    ]
)
data class User(
    @PrimaryKey(autoGenerate = true)
    @ColumnInfo(name = "id")
    val id: Int = 0,

    @ColumnInfo(name = "username")
    @SerializedName("username")
    val username: String,

    @ColumnInfo(name = "email")
    @SerializedName("email")
    val email: String,

    @ColumnInfo(name = "phone")
    @SerializedName("phone")
    val phone: String,

    @ColumnInfo(name = "gender")
    @SerializedName("gender")
    val gender: String,

    @ColumnInfo(name = "type")
    @SerializedName("type")
    val type: UserType = UserType.USER,

    @ColumnInfo(name = "registered_at")
    @SerializedName("registered_at")
    val registeredAt: String,

    @ColumnInfo(name = "is_verified")
    @SerializedName("is_verified")
    val isVerified: Int = 0,

    @ColumnInfo(name = "birth_date")
    @SerializedName("birth_date")
    val birthDate: String
) : Parcelable

enum class UserType {
    @SerializedName("admin")
    ADMIN,

    @SerializedName("game_developer")
    GAME_DEVELOPER,

    @SerializedName("user")
    USER,

    @SerializedName("guest")
    GUEST
}

