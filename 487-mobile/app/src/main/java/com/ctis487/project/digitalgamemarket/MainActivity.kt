package com.ctis487.project.digitalgamemarket

import android.content.Context
import android.content.Intent
import android.media.MediaPlayer
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.work.OneTimeWorkRequestBuilder
import androidx.work.WorkManager
import com.ctis487.project.digitalgamemarket.adapter.FeaturedGamesAdapter
import com.ctis487.project.digitalgamemarket.adapter.StoreGamesAdapter
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.databinding.ActivityMainBinding
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.GameMediaRepository
import com.ctis487.project.digitalgamemarket.db.GameRepository
import com.ctis487.project.digitalgamemarket.db.UserRepository
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.Game
import com.ctis487.project.digitalgamemarket.worker.BuyGameWorker

class MainActivity : AppCompatActivity() {
    lateinit var binding: ActivityMainBinding
    lateinit var db : DigitalGameAssetRoomDatabase


    lateinit var userRepo : UserRepository
    lateinit var gameRepo : GameRepository
    lateinit var storeLayoutManager : GridLayoutManager

    var currentLang = ""

    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.setLocale(newBase, LocaleHelper.getLanguage(newBase)))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        currentLang = LocaleHelper.getLanguage(this)

        WorkManager.getInstance(this).cancelAllWorkByTag("marketing_worker")

        val marketingWork = OneTimeWorkRequestBuilder<BuyGameWorker>()
            .addTag("marketing_worker")
            .build()

        WorkManager.getInstance(this).enqueue(marketingWork)

        val displayMetrics = resources.displayMetrics
        val config = resources.configuration

        val widthPx = displayMetrics.widthPixels
        val heightPx = displayMetrics.heightPixels
        val widthDp = config.screenWidthDp
        val heightDp = config.screenHeightDp

        val gridColumns =  if (widthDp < 500)  1 else 2
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        db = DigitalGameAssetRoomDatabase.getDatabase(application)
        userRepo = UserRepository(db.userDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))
        gameRepo = GameRepository(db.gameDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))


        val storeAdapter = StoreGamesAdapter(emptyList()){ game, price ->
            val intent = Intent(this, CheckoutActivity::class.java)
            intent.putExtra("gameId", game.id)
            intent.putExtra("price", price)
            if (price < game.price) playWowEffect()
            startActivity(intent)
        }
        storeLayoutManager = GridLayoutManager(this, gridColumns)

        binding.storeItemsRecycler.adapter = storeAdapter
        binding.storeItemsRecycler.layoutManager = storeLayoutManager


        val featuredGamesAdapter = FeaturedGamesAdapter(mutableMapOf()) { game ->
            Toast.makeText(this, "${game.name} tıklandı", Toast.LENGTH_SHORT).show()

        }

        binding.featuredRecycler.adapter = featuredGamesAdapter
        binding.featuredRecycler.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)

        gameRepo.allGames.observe(this) { games ->
            if (!games.isNullOrEmpty() && Utils.bannerImages.isNotEmpty()) {
                storeAdapter.updateList(games)
                val matchedPairs = games.mapNotNull { game ->
                    val validBanner = Utils.bannerImages.find { media ->
                        media.gameId == game.id &&
                                media.fileName.contains("banner", ignoreCase = true)
                    }
                    if (validBanner != null) {
                        game to validBanner.filePath
                    } else {
                        null
                    }
                }

                val randomSelectionMap = matchedPairs
                    .shuffled()
                    .take(3)
                    .toMap()

                if (randomSelectionMap.isNotEmpty()) {
                    featuredGamesAdapter.updateList(randomSelectionMap)
                }
            }
        }



        binding.bottomNavBar.setOnItemSelectedListener { item ->

            when (item.itemId) {
                R.id.menuItemStore -> {
                        Log.wtf("WTF", "WTF")
                        playClickSound()
                }
                R.id.menuItemSettings -> {
                    val intent = Intent(this,SettingsActivity::class.java)
                    startActivity(intent)
                    playClickSound()
                }
                R.id.menuItemLibrary -> {
                    val intent = Intent(this, LibraryActivity::class.java)
                    startActivity(intent)
                    playClickSound()
                }
            }

            true
        }

    }

    override fun onResume() {
        super.onResume()

        val newLang = LocaleHelper.getLanguage(this)

        if (!newLang.equals(currentLang)){
            currentLang = newLang
            recreate()
        }
    }

    private fun displayToast(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show()
    }

    private fun playClickSound() {
        try {
            val mediaPlayer = MediaPlayer.create(this, R.raw.click_sound)
            mediaPlayer.setOnCompletionListener { mp -> mp.release() }
            mediaPlayer.start()
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    private fun playWowEffect() {
        try {
            val mediaPlayer = MediaPlayer.create(this, R.raw.wow)
            mediaPlayer.setOnCompletionListener { mp -> mp.release() }
            mediaPlayer.start()
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }
}