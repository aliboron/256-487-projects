package com.ctis487.project.digitalgamemarket

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
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

class MainActivity : AppCompatActivity() {
    lateinit var binding: ActivityMainBinding
    lateinit var db : DigitalGameAssetRoomDatabase

    lateinit var gameList: MutableList<Game>


    lateinit var userList: MutableList<Game>

    lateinit var userRepo : UserRepository
    lateinit var gameRepo : GameRepository
    lateinit var mediaRepo : GameMediaRepository

    private val apiService by lazy { ApiClient.getClient().create(GameMarketApiService::class.java) }

    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.setLocale(newBase, LocaleHelper.getLanguage(newBase)))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)
        /*
        binding.btnSettings.setOnClickListener {
            val intent = Intent(this, SettingsActivity::class.java)
            startActivity(intent)
        }

        binding.btnCheckout.setOnClickListener {
            val intent = Intent(this, CheckoutActivity::class.java)
            startActivity(intent)
        }

        binding.btnLanguage.setOnClickListener {
            changeLanguage()
        }*/

        db = DigitalGameAssetRoomDatabase.getDatabase(application)
        userRepo = UserRepository(db.userDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))
        gameRepo = GameRepository(db.gameDAO(), ApiClient.getClient().create(GameMarketApiService::class.java))

        gameRepo.allGames.observe(this) {

        }

        val storeAdapter = StoreGamesAdapter(emptyList()){ game ->
            val intent = Intent(this, CheckoutActivity::class.java)
            intent.putExtra("gameId", game.id)

            startActivity(intent)
        }

        binding.storeItemsRecycler.adapter = storeAdapter
        binding.storeItemsRecycler.layoutManager = GridLayoutManager(this, 2)

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
                }
                R.id.menuItemSettings -> {
                    val intent = Intent(this,SettingsActivity::class.java)
                    startActivity(intent)
                }
            }

            true
        }
        /*
        val storeAdapter = StoreGamesAdapter(
            gameList = myGameList,
            onGameClick = { game ->
                // Navigate to Game Details Activity
            },
            onBuyClick = { game ->
                // Add to cart or start checkout process
                Toast.makeText(this, "Added ${game.name} to cart!", Toast.LENGTH_SHORT).show()
            }
        )
        recyclerView.adapter = storeAdapter*/
    }

    private fun changeLanguage() {
        val currentLang = LocaleHelper.getLanguage(this)
        val newLang = if (currentLang == "tr") "en" else "tr"
        LocaleHelper.setLocale(this, newLang)
        displayToast(resources.getString(R.string.toast_language_changed))
        recreate()
    }

    private fun displayToast(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show()
    }
}