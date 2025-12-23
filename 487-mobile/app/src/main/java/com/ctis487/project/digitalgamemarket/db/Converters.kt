package com.ctis487.project.digitalgamemarket.db

import androidx.room.TypeConverter
import com.ctis487.project.digitalgamemarket.model.UserType
import com.ctis487.project.digitalgamemarket.model.MediaType

class Converters {

    @TypeConverter
    fun fromUserType(value: UserType): String {
        return value.name
    }

    @TypeConverter
    fun toUserType(value: String): UserType {
        return try {
            UserType.valueOf(value)
        } catch (e: IllegalArgumentException) {
            UserType.USER
        }
    }

    @TypeConverter
    fun fromMediaType(value: MediaType): String {
        return value.name
    }

    @TypeConverter
    fun toMediaType(value: String): MediaType {
        return try {
            MediaType.valueOf(value)
        } catch (e: IllegalArgumentException) {
            MediaType.IMAGE
        }
    }
}

