package com.ctis487.project.digitalgamemarket.worker

import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Context
import android.os.Build
import androidx.core.app.NotificationCompat
import androidx.work.OneTimeWorkRequestBuilder
import androidx.work.WorkManager
import androidx.work.Worker
import androidx.work.WorkerParameters
import com.ctis487.project.digitalgamemarket.R
import java.util.concurrent.TimeUnit

class BuyGameWorker(context: Context, params: WorkerParameters) : Worker(context, params) {

    override fun doWork(): Result {

        sendNotification("HEY BUY A GAME", "Don't miss out on the latest deals!")


        val nextWorkRequest = OneTimeWorkRequestBuilder<BuyGameWorker>()
            .setInitialDelay(1, TimeUnit.MINUTES)
            .addTag("marketing_worker")
            .build()

        WorkManager.getInstance(applicationContext).enqueue(nextWorkRequest)

        return Result.success()
    }

    private fun sendNotification(title: String, message: String) {
        val notificationManager = applicationContext.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        val channelId = "game_marketing_channel"

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Marketing Notifications",
                NotificationManager.IMPORTANCE_HIGH
            )
            notificationManager.createNotificationChannel(channel)
        }

        val notification = NotificationCompat.Builder(applicationContext, channelId)
            .setSmallIcon(android.R.drawable.ic_dialog_info)
            .setContentTitle(title)
            .setContentText(message)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .build()


        notificationManager.notify(System.currentTimeMillis().toInt(), notification)
    }
}