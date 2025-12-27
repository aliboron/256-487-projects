package com.ctis487.project.digitalgamemarket.model

import androidx.room.ColumnInfo
import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey
import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.parcelize.Parcelize

@Parcelize
@Entity(
    tableName = "game_media",
    foreignKeys = [
        ForeignKey(
            entity = Game::class,
            parentColumns = ["id"],
            childColumns = ["game_id"],
            onDelete = ForeignKey.CASCADE,
            onUpdate = ForeignKey.CASCADE
        )
    ],
    indices = [
        Index(value = ["game_id"]),
        Index(value = ["media_type"]),
        Index(value = ["display_order"])
    ]
)
data class GameMedia(
    @PrimaryKey(autoGenerate = true)
    @ColumnInfo(name = "id")
    val id: Int = 0,

    @ColumnInfo(name = "game_id")
    @SerializedName("game_id")
    val gameId: Int,

    @ColumnInfo(name = "media_type")
    @SerializedName("media_type")
    val mediaType: MediaType,

    @ColumnInfo(name = "file_path")
    @SerializedName("file_path")
    val filePath: String = ".",

    @ColumnInfo(name = "file_name")
    @SerializedName("file_name")
    val fileName: String,

    @ColumnInfo(name = "display_order")
    @SerializedName("display_order")
    val displayOrder: Int = 0,

    @ColumnInfo(name = "uploaded_at")
    @SerializedName("uploaded_at")
    val uploadedAt: String = ""
) : Parcelable

enum class MediaType{
    @SerializedName("image")
    IMAGE,
    @SerializedName("video")
    VIDEO
}

