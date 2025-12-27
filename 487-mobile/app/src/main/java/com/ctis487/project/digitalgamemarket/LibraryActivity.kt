package com.ctis487.project.digitalgamemarket

import android.os.Bundle
import android.util.Log
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.room.Room
import com.ctis487.project.digitalgamemarket.adapter.LibraryRecyclerViewAdapter
import com.ctis487.project.digitalgamemarket.client.ApiClient
import com.ctis487.project.digitalgamemarket.client.GameMarketApiService
import com.ctis487.project.digitalgamemarket.databinding.ActivityLibraryBinding
import com.ctis487.project.digitalgamemarket.db.CheckoutRepository
import com.ctis487.project.digitalgamemarket.db.DigitalGameAssetRoomDatabase
import com.ctis487.project.digitalgamemarket.db.Utils
import com.ctis487.project.digitalgamemarket.model.Checkout
import com.ctis487.project.digitalgamemarket.model.Game
import com.ctis487.project.digitalgamemarket.model.LibItem
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import kotlin.collections.forEach
import kotlin.collections.joinToString
import kotlin.jvm.java

class LibraryActivity : AppCompatActivity() {

    lateinit var binding: ActivityLibraryBinding
    lateinit var adapter: LibraryRecyclerViewAdapter
    private val apiService by lazy {
        ApiClient.getClient().create(GameMarketApiService::class.java)
    }
    lateinit var checkouts : List<Checkout>
    lateinit var games : List<Game>
    var userGames = ArrayList<Game>()
    var recItems = ArrayList<LibItem>()
    private val db by lazy { DigitalGameAssetRoomDatabase.getDatabase(this) }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        binding = ActivityLibraryBinding.inflate(layoutInflater)
        setContentView(binding.root)

        var userId = Utils.user!!.user.id//userId'yi al

        binding.libraryRecyclerView.setLayoutManager(LinearLayoutManager(this))


        db.checkoutDAO().getCheckoutsByUser(userId).observe(this) { checkOutsOrEmpty ->
            //Log.d("LibraryActivity", checkOutsOrEmpty.toString()+ "if'den önce")
            if (!checkOutsOrEmpty.isEmpty()) {//DB'de varsa DB'den çek
                checkouts = checkOutsOrEmpty

                db.gameDAO().getAllGames().observe(this) { gamesFromDb ->
                    games = gamesFromDb
                    checkouts.forEach {
                        var checkout = it
                        games.forEach {
                            if (it.id == checkout.gameId) {
                                userGames.add(it)
                                recItems.add(LibItem(checkout, it))
                            }
                        }
                    }
                    adapter = LibraryRecyclerViewAdapter(this@LibraryActivity,recItems)
                    binding.libraryRecyclerView.adapter = adapter
                }

            adapter = LibraryRecyclerViewAdapter(this, recItems)
            binding.libraryRecyclerView.adapter = adapter
            } else {//Yoksa Apiden çek
                Log.d("LibraryActivity", "There are no checkout on DB. Fetching from API.")
                lifecycleScope.launch(Dispatchers.IO) {
                    val result: Result<List<Checkout>> = CheckoutRepository(db.checkoutDAO(), apiService).fetchCheckoutsFromApi()
                    val apiCheckouts: List<Checkout> = result.getOrNull()!!

                    checkouts = apiCheckouts

                    db.gameDAO().getAllGames().observe(this@LibraryActivity) { gamesFromDb ->
                        games = gamesFromDb
                        checkouts.forEach {
                            var checkout = it
                            games.forEach {
                                if (it.id == checkout.gameId) {
                                    userGames.add(it)
                                    recItems.add(LibItem(checkout, it))
                                }
                            }
                        }
                        adapter = LibraryRecyclerViewAdapter(this@LibraryActivity,recItems)
                        binding.libraryRecyclerView.adapter = adapter
                    }

                    if (apiCheckouts != null) {//Apiden çekti
                        Log.d("LibraryActivity", checkouts.toString(), result.exceptionOrNull())
                        Log.d("Games", games.joinToString(" "))
                    } else//Apiden çekemedi
                        Log.e("LibraryActivity", "API fetch failed", result.exceptionOrNull())
                        Log.d("Games", games.joinToString(" "))
                    }
            }
        }


    }


}