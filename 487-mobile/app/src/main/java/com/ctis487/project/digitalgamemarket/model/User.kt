package com.ctis487.project.digitalgamemarket.model

import androidx.room.ColumnInfo
import androidx.room.Entity
import androidx.room.Index
import androidx.room.PrimaryKey
import android.os.Parcelable
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
    val username: String,

    @ColumnInfo(name = "email")
    val email: String,

    @ColumnInfo(name = "phone")
    val phone: String,

    @ColumnInfo(name = "password")
    val password: String,

    @ColumnInfo(name = "gender")
    val gender: String,

    @ColumnInfo(name = "type")
    val type: UserType = UserType.USER,

    @ColumnInfo(name = "registered_at")
    val registeredAt: Long = System.currentTimeMillis(),

    @ColumnInfo(name = "is_verified")
    val isVerified: Boolean = false,

    @ColumnInfo(name = "birth_date")
    val birthDate: String
) : Parcelable

enum class UserType {
    ADMIN,
    GAME_DEVELOPER,
    USER,
    GUEST
}

