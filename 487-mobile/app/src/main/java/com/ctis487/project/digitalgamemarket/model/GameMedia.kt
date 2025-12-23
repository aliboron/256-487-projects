package com.ctis487.project.digitalgamemarket.model

import androidx.room.ColumnInfo
import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey
import android.os.Parcelable
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
    val gameId: Int,

    @ColumnInfo(name = "media_type")
    val mediaType: MediaType,

    @ColumnInfo(name = "file_path")
    val filePath: String = ".",

    @ColumnInfo(name = "file_name")
    val fileName: String,

    @ColumnInfo(name = "display_order")
    val displayOrder: Int = 0,

    @ColumnInfo(name = "uploaded_at")
    val uploadedAt: Long = System.currentTimeMillis()
) : Parcelable

enum class MediaType {
    IMAGE,
    VIDEO
}

