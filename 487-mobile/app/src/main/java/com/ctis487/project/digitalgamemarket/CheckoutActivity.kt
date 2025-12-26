package com.ctis487.project.digitalgamemarket

import android.os.Bundle
import android.util.Log
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.databinding.ActivityCheckoutBinding
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.GameRepository
import com.ctis487.project.digitalgamemarket.model.Game
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class CheckoutActivity : AppCompatActivity() {
    lateinit var binding: ActivityCheckoutBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        binding = ActivityCheckoutBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val gameId = intent.getIntExtra("gameId", 21)
        val db = DigitalGameAssetRoomDatabase.getDatabase(this)

        val apiService = ApiClient.getClient().create(GameMarketApiService::class.java)
        val gameRepository = GameRepository(db.gameDAO(), apiService)

        db.gameDAO().getGameById(gameId).observe(this) { gameOrNull ->
            if (gameOrNull != null)
                renderGame(gameOrNull)
            else {
                Log.d("CheckoutActivity", "Game $gameId not in DB. Fetching from API.")
                lifecycleScope.launch(Dispatchers.IO) {
                    val result: Result<Game> = gameRepository.fetchGameByIdFromApi(gameId)

                    val apiGame: Game? = result.getOrNull()
                    if (apiGame != null) {
                        withContext(Dispatchers.Main) {
                            renderGame(apiGame)
                        }
                        db.gameDAO().insert(apiGame)
                    } else
                        Log.e("CheckoutActivity", "API fetch failed", result.exceptionOrNull())
                }
            }
        }
    }

    fun renderGame(game: Game) {
        binding.checkoutTxtGameTitle.text = game.name
        binding.checkoutTxtGamePrice.text = "${game.price} $"
        binding.checkoutTxtDetails.text = "Total: ${game.price} $ (${game.name})"
        binding.checkoutTxtGameDetails.text = game.description

        Glide.with(this)
            .load(game.logoPath)
            .placeholder(R.drawable.ic_launcher_foreground)
            .error(R.drawable.ic_launcher_foreground)
            .centerCrop()
            .into(binding.imageView)
    }
}