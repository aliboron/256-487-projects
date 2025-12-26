package com.ctis487.project.digitalgamemarket

import android.os.Bundle
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.databinding.ActivityCheckoutBinding
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.GameRepository
import kotlinx.coroutines.launch

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

        lifecycleScope.launch {
            val gameDao = db.gameDAO()
            var game = gameDao.getGameById(gameId).value
            if (game == null) {
                game = gameRepository.fetchGameByIdFromApi(gameId).getOrThrow()
            }

            binding.checkoutTxtGameTitle.text = game.name
            binding.checkoutTxtGamePrice.text = "${game.price} $"
            binding.checkoutTxtDetails.text = "Total: ${game.price} $ (${game.name})"
            binding.checkoutTxtGameDetails.text = game.description
        }
    }
}