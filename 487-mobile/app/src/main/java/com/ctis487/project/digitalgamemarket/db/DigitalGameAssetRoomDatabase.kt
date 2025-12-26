package com.ctis487.project.digitalgamemarket.db

import android.content.Context
import androidx.room.AutoMigration
import androidx.room.Database
import androidx.room.Room
import androidx.room.RoomDatabase
import androidx.room.TypeConverters
import com.ctis487.project.digitalgamemarket.model.*

@Database(
    entities = [
        User::class,
        Game::class,
        Checkout::class,
        GameMedia::class
    ],
    version = 1
)
@TypeConverters(Converters::class)
abstract class DigitalGameAssetRoomDatabase : RoomDatabase() {
    abstract fun userDAO(): UserDAO
    abstract fun gameDAO(): GameDAO
    abstract fun checkoutDAO(): CheckoutDAO
    abstract fun gameMediaDAO(): GameMediaDAO

    companion object {
        @Volatile
        private var INSTANCE: DigitalGameAssetRoomDatabase? = null

        fun getDatabase(context: Context): DigitalGameAssetRoomDatabase {
            val tempInstance = INSTANCE
            if (tempInstance != null) {
                return tempInstance
            }

            synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    DigitalGameAssetRoomDatabase::class.java,
                    Utils.DATABASENAME
                )
                    .build()
                INSTANCE = instance
                return instance
            }
        }

    }
}
