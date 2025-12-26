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
    tableName = "checkouts",
//    foreignKeys = [
//        ForeignKey(
//            entity = User::class,
//            parentColumns = ["id"],
//            childColumns = ["user_id"],
//            onDelete = ForeignKey.CASCADE,
//            onUpdate = ForeignKey.CASCADE
//        ),
//        ForeignKey(
//            entity = Game::class,
//            parentColumns = ["id"],
//            childColumns = ["game_id"],
//            onDelete = ForeignKey.CASCADE,
//            onUpdate = ForeignKey.CASCADE
//        )
//    ],
    indices = [
        Index(value = ["user_id"]),
        Index(value = ["game_id"])
    ]
)
data class Checkout(
    @PrimaryKey(autoGenerate = true)
    @ColumnInfo(name = "id")
    val id: Int = 0,

    @ColumnInfo(name = "date")
    val date: String,

    @SerializedName("user_id")
    @ColumnInfo(name = "user_id")
    val userId: Int,

    @SerializedName("payment_total")
    @ColumnInfo(name = "payment_total")
    val paymentTotal: Double,

    @SerializedName("game_id")
    @ColumnInfo(name = "game_id")
    val gameId: Int
) : Parcelable

